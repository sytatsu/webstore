#!/usr/bin/env bash
#
# Updates production with one command:
#   1. pulls the latest code of this repo and of every plugin package
#      (cloning a package first if it isn't there yet),
#   2. builds the production Docker images,
#   3. puts the site in maintenance mode and runs the database migrations,
#   4. restarts the stack on the new images and checks that the site responds.
#
# Usage: scripts/deploy.sh [options]
#   --branch <name>   branch to deploy for this repo and the packages (default: main)
#   --no-migrate      skip the database migrations
#   --no-cache        build the images without Docker's layer cache
#   --skip-pull       deploy what's checked out now, without pulling
#   -h, --help        show this help
#
# Everything runs inside main(), which bash reads completely before running it,
# so the `git pull` below can safely update this very file.

# -E makes the ERR trap below also fire inside functions.
set -Eeuo pipefail

main() {
    local root branch migrate=1 no_cache=0 pull=1
    root="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
    branch="main"

    while [ $# -gt 0 ]; do
        case "$1" in
            --branch) branch="${2:?--branch needs a value}"; shift 2 ;;
            --no-migrate) migrate=0; shift ;;
            --no-cache) no_cache=1; shift ;;
            --skip-pull) pull=0; shift ;;
            -h|--help) sed -n '2,18p' "${BASH_SOURCE[0]}" | sed 's/^# \{0,1\}//'; exit 0 ;;
            *) fail "Unknown option: $1 (see --help)" ;;
        esac
    done

    cd "$root"

    # Plugin packages: folder under packages/sytatsu/ and its git remote.
    # composer.json loads them from ./packages/sytatsu/* (a path repository),
    # so they must be present before the image is built.
    local packages=(
        "filamentphp-fleet git@github.com:sytatsu/filamentphp-fleet.git"
        "filamentphp-flagship-rpg git@github.com:sytatsu/filamentphp-flagship-rpg.git"
        "filamentphp-issue-tracker git@github.com:sytatsu/filamentphp-issue-tracker.git"
        "filamentphp-page-visits git@github.com:sytatsu/filamentphp-page-visits.git"
    )

    preflight

    if [ "$pull" -eq 1 ]; then
        step "Pulling the latest code ($branch)"
        pull_repo "$root" "$branch"
        local entry name url
        for entry in "${packages[@]}"; do
            name="${entry%% *}"
            url="${entry#* }"
            pull_package "$root/packages/sytatsu/$name" "$url" "$branch"
        done
    fi

    step "Deploying these versions"
    describe_repo "sytatsu.nl" "$root"
    local entry
    for entry in "${packages[@]}"; do
        describe_repo "${entry%% *}" "$root/packages/sytatsu/${entry%% *}"
    done

    step "Building the Docker images"
    if [ "$no_cache" -eq 1 ]; then
        compose build --pull --no-cache
    else
        compose build --pull
    fi

    if [ "$migrate" -eq 1 ]; then
        step "Running the database migrations"
        maintenance_down
        # From here on, a failure must not leave the site in maintenance mode.
        trap 'maintenance_up_after_failure' ERR
        # Runs on the new image, while the old containers keep serving the
        # maintenance page.
        compose run --rm --no-deps php-fpm php artisan migrate --force
    fi

    step "Restarting the stack"
    compose up -d --remove-orphans
    wait_for_php_fpm
    # The new containers start out of maintenance mode; this is just a safety net.
    compose exec -T php-fpm php artisan up >/dev/null 2>&1 || true
    trap - ERR

    step "Checking the site"
    health_check

    step "Cleaning up old images"
    docker image prune -f >/dev/null

    printf '\n\033[1;32mDeploy finished.\033[0m\n'
}

compose() {
    docker compose -f docker-compose.prod.yml "$@"
}

step() {
    printf '\n\033[1;34m==> %s\033[0m\n' "$1"
}

fail() {
    printf '\033[1;31mError:\033[0m %s\n' "$1" >&2
    exit 1
}

preflight() {
    command -v git >/dev/null || fail "git is not installed."
    command -v docker >/dev/null || fail "docker is not installed."
    docker compose version >/dev/null 2>&1 || fail "docker compose (v2) is not available."
    [ -f docker-compose.prod.yml ] || fail "docker-compose.prod.yml not found in $(pwd)."
    [ -f .env.production ] || fail ".env.production is missing (copy .env.production.example and fill it in)."
}

# Fails instead of overwriting local changes on the server.
ensure_clean() {
    local dir="$1"
    if [ -n "$(git -C "$dir" status --porcelain --untracked-files=no)" ]; then
        git -C "$dir" status --short --untracked-files=no >&2
        fail "$dir has local changes. Commit, stash or discard them first."
    fi
}

pull_repo() {
    local dir="$1" branch="$2"
    echo "$(basename "$dir"):"
    ensure_clean "$dir"
    git -C "$dir" fetch --prune origin
    git -C "$dir" checkout -q "$branch"
    git -C "$dir" pull --ff-only origin "$branch"
}

pull_package() {
    local dir="$1" url="$2" branch="$3"
    if [ ! -e "$dir" ]; then
        echo "$(basename "$dir"): not found, cloning $url"
        git clone --branch "$branch" "$url" "$dir"
        return
    fi
    [ -d "$dir/.git" ] || fail "$dir exists but isn't a git repository."
    pull_repo "$dir" "$branch"
}

describe_repo() {
    printf '  %-28s %s\n' "$1" "$(git -C "$2" log -1 --format='%h %s (%cr)')"
}

# Puts the running site (if any) in maintenance mode.
maintenance_down() {
    if [ -n "$(compose ps -q php-fpm 2>/dev/null)" ]; then
        compose exec -T php-fpm php artisan down --retry=30 || true
    fi
}

maintenance_up_after_failure() {
    printf '\033[1;31mDeploy failed.\033[0m Taking the site out of maintenance mode...\n' >&2
    compose exec -T php-fpm php artisan up >&2 || true
}

wait_for_php_fpm() {
    local i
    for i in $(seq 1 30); do
        if compose exec -T php-fpm php artisan --version >/dev/null 2>&1; then
            return 0
        fi
        sleep 2
    done
    compose logs --tail=50 php-fpm >&2
    fail "php-fpm didn't come up within 60 seconds (logs above)."
}

# Requests the homepage from inside the nginx container, so it works
# whatever port or proxy sits in front of it.
health_check() {
    local i status=""
    for i in $(seq 1 10); do
        status="$(compose exec -T nginx sh -c \
            "wget -q -S -O /dev/null http://localhost/ 2>&1 | awk '/HTTP\\//{code=\$2} END{print code}'" \
            2>/dev/null || true)"
        case "$status" in
            2??|3??) echo "Homepage responds with HTTP $status."; return 0 ;;
        esac
        sleep 3
    done
    compose logs --tail=50 php-fpm nginx >&2
    fail "Homepage responded with HTTP ${status:-nothing} (logs above)."
}

main "$@"
exit

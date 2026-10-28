#!/usr/bin/env bash
#
# One-time server update (October 2026): installs Flagship RPG and removes Fleet.
#
#   1. checks that the server can read the Flagship RPG repo on GitHub,
#   2. backs up the production database without the Fleet tables
#      (to ~/sytatsu-backups by default), and records the deployed commits,
#   3. pulls this repo and deletes the Fleet package checkout
#      (packages/sytatsu/filamentphp-fleet) and its published assets,
#   4. runs scripts/deploy.sh: clones Flagship RPG, builds the images and runs
#      the migrations in maintenance mode. The migrations create the rpg_ tables
#      and drop every fleet_ table plus Fleet's rows in the migrations table,
#   5. checks that nothing of Fleet is left and the /flagship-rpg panel responds.
#
# Usage (on the server, from the repo):
#   git pull --ff-only          # only needed the first time, to get this script
#   scripts/install-flagship-rpg.sh [--yes] [--skip-backup] [--branch <name>]
#
#   --yes             don't ask for confirmation
#   --skip-backup     don't back up the database (not recommended)
#   --branch <name>   branch to deploy (default: main)
#
# Environment:
#   BACKUP_DIR      where backups go (default: ~/sytatsu-backups)
#   MYSQL_IMAGE     image used for mysqldump (default: mysql:8.0)
#
# Everything runs inside main(), which bash reads completely before running it,
# so the `git pull` below can safely update this very file.

set -Eeuo pipefail

RPG_REPO="git@github.com:sytatsu/filamentphp-flagship-rpg.git"
FLEET_DIR="packages/sytatsu/filamentphp-fleet"
FLEET_TABLES=(
    fleet_achievements fleet_battles fleet_captains fleet_commanders
    fleet_fit_templates fleet_fits fleet_missions fleet_modules
    fleet_queue_items fleet_research fleet_ships fleet_squadrons fleet_upgrades
)

main() {
    local root assume_yes=0 backup=1 branch="main"
    root="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
    cd "$root"

    while [ $# -gt 0 ]; do
        case "$1" in
            --yes|-y) assume_yes=1; shift ;;
            --skip-backup) backup=0; shift ;;
            --branch) branch="${2:?--branch needs a value}"; shift 2 ;;
            -h|--help) sed -n '2,28p' "${BASH_SOURCE[0]}" | sed 's/^# \{0,1\}//'; exit 0 ;;
            *) fail "Unknown option: $1 (see --help)" ;;
        esac
    done

    [ -x scripts/deploy.sh ] || fail "scripts/deploy.sh not found. Run 'git pull --ff-only' first."
    [ -f .env.production ] || fail ".env.production is missing."

    step "Checking access to the Flagship RPG repository"
    git ls-remote --exit-code "$RPG_REPO" HEAD >/dev/null \
        || fail "Can't read $RPG_REPO. Add this server's deploy key to that repo on GitHub."
    echo "OK: $RPG_REPO"

    local backup_dir stamp
    backup_dir="${BACKUP_DIR:-$HOME/sytatsu-backups}"
    stamp="$(date +%Y%m%d-%H%M%S)"

    cat <<EOF

This installs Flagship RPG and removes Fleet from production:
  - database backup:  $([ "$backup" -eq 1 ] && echo "$backup_dir/sytatsu-$stamp.sql.gz (without the Fleet tables)" || echo "SKIPPED")
  - pull this repo ($branch), delete $FLEET_DIR
  - deploy: clone Flagship RPG, build the images, run the migrations
    in maintenance mode, restart
  - ALL Fleet data is deleted from the database (${#FLEET_TABLES[@]} fleet_ tables and
    their migration records). This can't be undone.

EOF
    if [ "$assume_yes" -ne 1 ]; then
        local answer
        read -r -p "Continue? [y/N] " answer
        case "$answer" in y|Y|yes|YES) ;; *) echo "Aborted."; exit 1 ;; esac
    fi

    mkdir -p "$backup_dir"

    step "Recording the currently deployed commits"
    local versions_file="$backup_dir/versions-before-$stamp.txt"
    record_versions "$root" > "$versions_file"
    cat "$versions_file"
    echo "Saved to $versions_file"

    if [ "$backup" -eq 1 ]; then
        step "Backing up the database (without the Fleet tables)"
        backup_database "$backup_dir/sytatsu-$stamp.sql.gz"
    fi

    step "Pulling this repo ($branch)"
    if [ -n "$(git status --porcelain --untracked-files=no)" ]; then
        git status --short --untracked-files=no >&2
        fail "This repo has local changes. Commit, stash or discard them first."
    fi
    git fetch --prune origin
    git checkout -q "$branch"
    git pull --ff-only origin "$branch"
    grep -q '"sytatsu/filament-fleet"' composer.json \
        && fail "composer.json on '$branch' still requires Fleet. Merge the Fleet removal into '$branch' first."
    grep -q '"sytatsu/filamentphp-flagship-rpg"' composer.json \
        || fail "composer.json on '$branch' doesn't require Flagship RPG yet. Merge it into '$branch' first."

    step "Removing the Fleet package from the server"
    remove_path "$FLEET_DIR"
    remove_path "public/css/sytatsu/filament-fleet"
    remove_path "public/js/sytatsu/filament-fleet"

    step "Deploying"
    # Already pulled above; deploy.sh pulls again (a no-op for this repo) and
    # clones/updates the plugin packages, including Flagship RPG.
    scripts/deploy.sh --branch "$branch"

    step "Verifying"
    verify

    cat <<EOF

$(printf '\033[1;32mFlagship RPG installed, Fleet removed.\033[0m')

Please check in the browser:
  - /flagship-rpg: log in with a staff account, or sign up as a new player
    (sign-up is on unless FLAGSHIP_RPG_REGISTRATION=false in .env.production)
  - /lunar: the admin panel no longer shows Fleet

To roll back: restore the backup and check out the commits listed in
$versions_file in this repo and each package (clone Fleet again from
git@github.com:sytatsu/filamentphp-fleet.git), then run: scripts/deploy.sh --skip-pull
  gunzip < $backup_dir/sytatsu-$stamp.sql.gz | mysql -h <host> -u <user> -p <database>
Note: the backup has no Fleet tables, so Fleet would start empty.
EOF
}

step() {
    printf '\n\033[1;34m==> %s\033[0m\n' "$1"
}

fail() {
    printf '\033[1;31mError:\033[0m %s\n' "$1" >&2
    exit 1
}

compose() {
    docker compose -f docker-compose.prod.yml "$@"
}

remove_path() {
    if [ -e "$1" ]; then
        rm -rf -- "$1"
        echo "Deleted $1"
    else
        echo "Not present: $1"
    fi
}

# Reads one value from .env.production without executing the file.
env_value() {
    grep -E "^$1=" .env.production | tail -n 1 | cut -d= -f2- \
        | sed -E 's/^"(.*)"$/\1/; s/^'"'"'(.*)'"'"'$/\1/'
}

record_versions() {
    local root="$1" dir
    printf '%-28s %s\n' "sytatsu.nl" "$(git -C "$root" rev-parse HEAD)"
    for dir in "$root"/packages/sytatsu/*/; do
        [ -d "$dir/.git" ] || continue
        printf '%-28s %s\n' "$(basename "$dir")" "$(git -C "$dir" rev-parse HEAD)"
    done
}

# Runs mysqldump in a throwaway container on the same networks as the app,
# so it reaches the database the same way php-fpm does (dbnet, or the host
# via host.docker.internal). The password goes in as an env var, not an argument.
# The Fleet tables are left out: nothing of Fleet is kept on the server.
backup_database() {
    local target="$1" host port database username password table
    host="$(env_value DB_HOST)"
    port="$(env_value DB_PORT)"
    database="$(env_value DB_DATABASE)"
    username="$(env_value DB_USERNAME)"
    password="$(env_value DB_PASSWORD)"
    [ -n "$host" ] && [ -n "$database" ] && [ -n "$username" ] \
        || fail "DB_HOST, DB_DATABASE or DB_USERNAME is empty in .env.production."

    local network_args=(--add-host host.docker.internal:host-gateway)
    if docker network inspect dbnet >/dev/null 2>&1; then
        network_args+=(--network dbnet)
    fi

    local ignore_args=()
    for table in "${FLEET_TABLES[@]}"; do
        ignore_args+=("--ignore-table=$database.$table")
    done

    echo "Dumping $database from $host:${port:-3306}..."
    if ! docker run --rm "${network_args[@]}" -e MYSQL_PWD="$password" "${MYSQL_IMAGE:-mysql:8.0}" \
        mysqldump -h "$host" -P "${port:-3306}" -u "$username" \
        --single-transaction --routines --triggers --no-tablespaces \
        "${ignore_args[@]}" "$database" \
        | gzip > "$target.partial"; then
        rm -f "$target.partial"
        fail "mysqldump failed (see above). Nothing was changed."
    fi

    # A (nearly) empty dump means something went wrong; don't keep it as a "backup".
    if [ "$( (gzip -dc "$target.partial" 2>/dev/null || true) | head -c 4096 | wc -c)" -lt 1000 ]; then
        rm -f "$target.partial"
        fail "The database dump looks empty. Not continuing without a backup."
    fi
    mv "$target.partial" "$target"
    echo "Backup saved: $target ($(du -h "$target" | cut -f1))"
}

verify() {
    local report pending status="" i

    [ ! -e "$FLEET_DIR" ] || fail "$FLEET_DIR still exists."

    # Asks the running app what is installed and what is in the database.
    report="$(compose exec -T php-fpm php -r '
        require "vendor/autoload.php";
        $app = require "bootstrap/app.php";
        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        $db = Illuminate\Support\Facades\DB::connection();
        $tables = array_map(fn ($row) => $row->name, $db->select(
            "select table_name as name from information_schema.tables where table_schema = database()"
        ));
        $count = fn ($prefix) => count(array_filter($tables, fn ($t) => str_starts_with($t, $prefix)));
        $fleetMigrations = $db->table(config("database.migrations.table", "migrations"))
            ->where("migration", "like", "2026_10_%")
            ->where(fn ($q) => $q->where("migration", "like", "%fleet%")
                ->orWhereIn("migration", [
                    "2026_10_23_000007_rename_carrier_to_command_ship",
                    "2026_10_23_000008_remove_legacy_light_craft",
                    "2026_10_23_000009_one_row_per_ship",
                    "2026_10_23_000012_refund_upgrades_replaced_by_research",
                    "2026_10_23_000014_create_starter_squadrons",
                    "2026_10_23_000016_add_flight_legs_and_mining_missions",
                ]))
            ->where("migration", "not like", "%drop_fleet_game_tables%")
            ->count();
        echo "fleet_package=", Composer\InstalledVersions::isInstalled("sytatsu/filament-fleet") ? 1 : 0, PHP_EOL;
        echo "rpg_package=", Composer\InstalledVersions::isInstalled("sytatsu/filamentphp-flagship-rpg") ? 1 : 0, PHP_EOL;
        echo "fleet_tables=", $count("fleet_"), PHP_EOL;
        echo "fleet_migrations=", $fleetMigrations, PHP_EOL;
        echo "rpg_tables=", $count("rpg_"), PHP_EOL;
    ')"
    echo "$report"
    echo "$report" | grep -qx 'fleet_package=0' || fail "The Fleet package is still installed."
    echo "$report" | grep -qx 'rpg_package=1' || fail "Flagship RPG is not installed."
    echo "$report" | grep -qx 'fleet_tables=0' || fail "Some fleet_ tables are still in the database."
    echo "$report" | grep -qx 'fleet_migrations=0' || fail "Fleet rows are still in the migrations table."
    echo "$report" | grep -Eqx 'rpg_tables=[1-9][0-9]*' || fail "No rpg_ tables found: the Flagship RPG migrations didn't run."

    pending="$(compose exec -T php-fpm php artisan migrate:status --pending 2>&1 || true)"
    if echo "$pending" | grep -q "Pending"; then
        echo "$pending"
        fail "Some migrations are still pending (see above)."
    fi
    echo "No pending migrations."

    # Requests the game's login page from inside the nginx container.
    for i in $(seq 1 10); do
        status="$(compose exec -T nginx sh -c \
            "wget -q -S -O /dev/null http://localhost/flagship-rpg/login 2>&1 | awk '/HTTP\\//{code=\$2} END{print code}'" \
            2>/dev/null || true)"
        case "$status" in
            2??) echo "/flagship-rpg/login responds with HTTP $status."; return 0 ;;
        esac
        sleep 3
    done
    compose logs --tail=50 php-fpm nginx >&2
    fail "/flagship-rpg/login responded with HTTP ${status:-nothing} (logs above)."
}

main "$@"
exit

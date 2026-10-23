#!/usr/bin/env bash
#
# One-time server upgrade to Lunar 1.5 / Filament 4 (October 2026).
#
#   1. backs up the production database (to ~/sytatsu-backups by default),
#   2. records the commits currently deployed, so you can roll back,
#   3. runs scripts/deploy.sh: pulls this repo and all plugin packages, builds
#      the images, runs the 7 new migrations in maintenance mode, restarts,
#   4. checks that Lunar 1.5 / Filament 4 are installed and no migrations are pending.
#
# Usage (on the server, from the repo):
#   git pull --ff-only          # only needed the first time, to get this script
#   scripts/upgrade-lunar-1.5.sh [--yes] [--skip-backup]
#
#   --yes           don't ask for confirmation
#   --skip-backup   don't back up the database (not recommended)
#
# Environment:
#   BACKUP_DIR      where backups go (default: ~/sytatsu-backups)
#   MYSQL_IMAGE     image used for mysqldump (default: mysql:8.0)

set -Eeuo pipefail

main() {
    local root assume_yes=0 backup=1
    root="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
    cd "$root"

    while [ $# -gt 0 ]; do
        case "$1" in
            --yes|-y) assume_yes=1; shift ;;
            --skip-backup) backup=0; shift ;;
            -h|--help) sed -n '2,22p' "${BASH_SOURCE[0]}" | sed 's/^# \{0,1\}//'; exit 0 ;;
            *) fail "Unknown option: $1 (see --help)" ;;
        esac
    done

    [ -x scripts/deploy.sh ] || fail "scripts/deploy.sh not found. Run 'git pull --ff-only' first."
    [ -f .env.production ] || fail ".env.production is missing."

    local backup_dir stamp
    backup_dir="${BACKUP_DIR:-$HOME/sytatsu-backups}"
    stamp="$(date +%Y%m%d-%H%M%S)"

    cat <<EOF

This upgrades production to Lunar 1.5 and Filament 4:
  - database backup:  $([ "$backup" -eq 1 ] && echo "$backup_dir/sytatsu-$stamp.sql.gz" || echo "SKIPPED")
  - pull this repo and the plugin packages, build the images
  - maintenance mode while 7 migrations run, then restart
    (includes Lunar's rename of the staff two-factor columns)

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
        step "Backing up the database"
        backup_database "$backup_dir/sytatsu-$stamp.sql.gz"
    fi

    step "Deploying"
    scripts/deploy.sh

    step "Verifying the upgrade"
    verify

    cat <<EOF

$(printf '\033[1;32mUpgrade finished.\033[0m')

Please check in the browser:
  - log in to the admin panel (/lunar), incl. a staff member with two-factor auth
  - the webstore settings pages, the bundles page, the issue tracker and Fleet
  - table filters now apply when you press "Apply" (Filament 4 default)

To roll back: restore the backup and check out the commits listed in
$versions_file in this repo and each package, then run: scripts/deploy.sh --skip-pull --no-migrate
  gunzip < $backup_dir/sytatsu-$stamp.sql.gz | mysql -h <host> -u <user> -p <database>
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
backup_database() {
    local target="$1" host port database username password
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

    echo "Dumping $database from $host:${port:-3306}..."
    if ! docker run --rm "${network_args[@]}" -e MYSQL_PWD="$password" "${MYSQL_IMAGE:-mysql:8.0}" \
        mysqldump -h "$host" -P "${port:-3306}" -u "$username" \
        --single-transaction --routines --triggers --no-tablespaces "$database" \
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
    local versions pending
    versions="$(compose exec -T php-fpm php -r '
        require "vendor/autoload.php";
        foreach (["lunarphp/lunar", "filament/filament", "laravel/framework"] as $p) {
            echo str_pad($p, 20), Composer\InstalledVersions::getPrettyVersion($p), PHP_EOL;
        }')"
    echo "$versions"
    echo "$versions" | grep -Eq 'lunarphp/lunar +v?1\.(5|[6-9])' || fail "Lunar 1.5+ is not installed."
    echo "$versions" | grep -Eq 'filament/filament +v?4\.' || fail "Filament 4 is not installed."

    pending="$(compose exec -T php-fpm php artisan migrate:status --pending 2>&1 || true)"
    if echo "$pending" | grep -q "Pending"; then
        echo "$pending"
        fail "Some migrations are still pending (see above)."
    fi
    echo "No pending migrations."
}

main "$@"
exit

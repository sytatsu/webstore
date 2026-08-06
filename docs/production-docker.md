# Production Docker setup

Mirrors the dev split (nginx + php-fpm) but as a multi-stage build with no bind
mounts, no dev tooling, and no database container — the database is expected
to run outside this stack.

## Images

`.docker/prod/Dockerfile` builds two targets from one file, so both share the
same compiled `public/build` (Vite) output without needing a shared volume:

- `php-fpm` — the app image (PHP 8.4-fpm, opcache on, no xdebug).
- `nginx` — a static image with `public/` baked in, proxying `*.php` to `php-fpm:9000`.

`docker-compose.prod.yml` runs three services from those two images: `nginx`,
`php-fpm`, and `scheduler` (same `php-fpm` image, running `php artisan
schedule:run` in a loop instead of `php-fpm` — see below).

## First-time setup

```
cp .env.production.example .env.production
# fill in APP_KEY, DB_*, mail, AWS/S3, etc.
docker compose -f docker-compose.prod.yml up -d --build
```

`.env.production` is gitignored — never commit real secrets to it.

## Connecting to a database outside Docker

`DB_HOST` in `.env.production` controls this:

- **Same machine as Docker, not containerized**: set `DB_HOST=host.docker.internal`.
  The `php-fpm` service maps that hostname to the host via `extra_hosts:
  host-gateway`, so it resolves on Linux hosts too (not just Docker Desktop).
- **Remote/managed database**: set `DB_HOST` to its real hostname or IP —
  nothing else to configure, the container reaches it over the network like
  any other outbound connection.

## File storage

`FILESYSTEM_DISK=local` writes to the php-fpm container's own filesystem,
which isn't shared with the nginx container or persisted across redeploys.
`.env.production.example` defaults to `FILESYSTEM_DISK=s3` — fill in the
`AWS_*` vars to use it. If you want local disk anyway, put a persistent
volume on `storage/app/public` in both services and accept that public
uploads require plumbing them into the nginx image too.

## Changing the published port

`docker-compose.prod.yml` reads `${APP_PORT:-80}` for the nginx port mapping.
Compose only expands that from its own `.env` file in the project directory
(a separate mechanism from `env_file: .env.production`, which just sets
variables inside the container) — so to change it, either `export APP_PORT=8080`
before `up`, or add `APP_PORT=8080` to a plain `.env` file.

## Scheduled tasks

`app/Console/Kernel.php` schedules `webstore:generate-sitemap` daily. The
`scheduler` service exists solely to call `schedule:run` every minute so that
keeps firing — nothing else in this stack invokes it (no host cron, no
supervisord). If you add more scheduled commands, they run through the same
service automatically.

## Deploying a new release

Cache commands (`config:cache`, `route:cache`, `view:cache`) run automatically
in the php-fpm container's entrypoint on every start. Migrations do not run
automatically — run them explicitly as part of your deploy:

```
docker compose -f docker-compose.prod.yml run --rm php-fpm php artisan migrate --force
```

## Project name

`docker-compose.prod.yml` pins `name: sytatsu-prod` so it never shares a
Compose project namespace with `docker-compose.yml` (the dev stack, which
defaults to a directory-derived name). Without that, running both from this
directory would make Compose treat same-named services as updates to each
other and tear down whichever stack it decided was stale — do not remove it.

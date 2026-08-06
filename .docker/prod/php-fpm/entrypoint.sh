#!/usr/bin/env sh
set -e

php artisan config:cache
php artisan route:cache
php artisan view:cache

if [ ! -L public/storage ]; then
    php artisan storage:link
fi

exec "$@"

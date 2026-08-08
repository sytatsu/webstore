#!/usr/bin/env sh
set -e

echo 'Discovering packages...'
php artisan package:discover --ansi

php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

if [ ! -L public/storage ]; then
    php artisan storage:link
fi

exec "$@"

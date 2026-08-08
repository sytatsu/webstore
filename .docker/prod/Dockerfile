# syntax=docker/dockerfile:1

########################################
# 1. PHP dependencies (composer)
########################################
FROM composer:2 AS vendor
# Matches the final image's WORKDIR: composer's post-autoload-dump scripts
# (storage:link) bake this absolute path into a symlink, so it must already
# be correct here rather than getting rewritten when copied into base below.
WORKDIR /var/www/html
# composer.json points sytatsu/filamentphp-* deps at a local path repository
# (./packages/sytatsu/*), so the full source tree is needed before install
# can run at all -- there's no lock-only layer-caching step to split out here.
COPY . .
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --prefer-dist \
    --no-interaction \
    --ignore-platform-reqs \
    --no-scripts

########################################
# 2. Frontend assets (vite build)
########################################
FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npx vite build

########################################
# 3. Application source, shared by both
#    the php-fpm and nginx runtime images
#    so neither needs a shared volume.
########################################
FROM php:8.4-fpm AS base

RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    libicu-dev \
    libpq-dev \
 && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install exif pcntl \
 && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
 && docker-php-ext-configure intl \
 && docker-php-ext-install gd \
    pdo \
    pdo_mysql \
    intl \
    zip \
    bcmath \
    opcache

RUN groupadd -g 1000 www && useradd -u 1000 -ms /bin/bash -g www www

WORKDIR /var/www/html

COPY --chown=www:www . .
COPY --chown=www:www --from=vendor /var/www/html/vendor ./vendor
# composer's post-autoload-dump scripts (filament:upgrade, storage:link) publish
# vendor CSS/JS and create the storage symlink under the vendor stage's own
# public/ dir -- pull those in too, or they'd be silently dropped.
COPY --chown=www:www --from=vendor /var/www/html/public ./public
COPY --chown=www:www --from=vendor /var/www/html/bootstrap/cache ./bootstrap/cache
COPY --chown=www:www --from=assets /app/public/build ./public/build

# Copied rather than symlinked (unlike the dev container) so the public/
# directory is self-contained when copied into the separate nginx image below.
RUN cp -r /var/www/html/resources/images /var/www/html/public/images \
 && cp -r /var/www/html/resources/files /var/www/html/public/files \
 && chmod -R ug+rwx storage bootstrap/cache

########################################
# 4a. php-fpm runtime
########################################
FROM base AS php-fpm

COPY .docker/prod/php-fpm/php.ini /usr/local/etc/php/conf.d/zz-app.ini
COPY .docker/prod/php-fpm/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

USER www

EXPOSE 9000
ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]

########################################
# 4b. nginx runtime
########################################
FROM nginx:alpine AS nginx

COPY .docker/prod/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY --from=base /var/www/html/public /var/www/html/public

EXPOSE 80

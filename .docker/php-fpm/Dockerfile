FROM composer:latest AS composer
FROM node:latest AS node
FROM php:8.4-fpm

###--- DEVELOPMENT TOOLS
# Copy node from image
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules
COPY --from=node /usr/local/bin/node /usr/local/bin/node
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm

# Copy composer from image
COPY --from=composer /usr/bin/composer /usr/local/bin/composer

# Copy xdebig.ini
COPY configs/xdebug.ini /usr/local/etc/php/conf.d/

# Install & enable xdebug
RUN pecl install xdebug \
 && docker-php-ext-enable xdebug

###--- APPLICATION
# Set working directory
WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    libzip-dev \
    libicu-dev \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libpq-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
RUN docker-php-ext-install exif pcntl \
 && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
 && docker-php-ext-configure intl \
 && docker-php-ext-install gd \
    pdo \
    pdo_mysql \
    intl \
    zip \
    bcmath

# Add user for laravel application
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Change current user to www
USER www

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]

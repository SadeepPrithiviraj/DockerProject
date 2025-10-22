# Use an official PHP image with fpm and composer
FROM php:8.2-fpm

# required packages
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libzip-dev \
        libonig-dev \
        libpng-dev \
        libpq-dev \
        default-mysql-client \
    && docker-php-ext-install pdo pdo_mysql zip

# install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# create app directory
WORKDIR /var/www/html

# copy composer files first for better caching
COPY app/composer.json app/phpunit.xml /var/www/html/

# install dependencies (will fail if dev dependencies not needed, but pipeline handles dev prod)
RUN composer install --no-interaction --no-plugins --no-scripts --prefer-dist || true

# copy the rest of the app
COPY app/ /var/www/html/

# run composer again to get dev deps in CI/test environment if needed
RUN composer install --no-interaction --prefer-dist

# give permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 9000
CMD ["php-fpm"]

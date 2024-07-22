FROM php:7.4-apache

RUN apt-get update && apt-get install -y libzip-dev unzip \
    && docker-php-ext-install zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /var/www/html/

RUN composer install --no-dev

RUN chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html

EXPOSE 80
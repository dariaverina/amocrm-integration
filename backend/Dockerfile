FROM php:8.1-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql

COPY . /var/www/html/

WORKDIR /var/www/html/

ENV ENV_PATH=/var/www/html/.env

CMD ["apache2-foreground"]

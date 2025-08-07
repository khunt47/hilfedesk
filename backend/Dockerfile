FROM composer:2.4 as build

FROM php:7.4-apache-buster as dev

ENV APP_ENV=dev
ENV APP_DEBUG=true
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update && apt-get install -y zip
RUN docker-php-ext-install pdo pdo_mysql
RUN apt install -y libmemcached-dev zlib1g-dev libssl-dev
RUN set -eux \
    && pecl install memcached \
    && docker-php-ext-enable memcached \
    && true

COPY ./app /var/www/html/
COPY --from=build /usr/bin/composer /usr/bin/composer
RUN composer install --prefer-dist --no-interaction

COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

RUN php artisan optimize && \
    php artisan config:cache && \
    php artisan route:cache && \
    chmod 777 -R /var/www/html/storage/ && \
    chown -R www-data:www-data /var/www/ && \
    a2enmod rewrite
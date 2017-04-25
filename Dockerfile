FROM php:7.1

# Programs
RUN apt-get update && apt-get install -y git

# PHP
#RUN docker-php-ext-install mysqli sockets
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

# Deps
COPY composer.lock composer.json /app/
WORKDIR /app/
RUN composer install --prefer-source --no-interaction

COPY etc/php.ini /usr/local/etc/php

# App
COPY . /app

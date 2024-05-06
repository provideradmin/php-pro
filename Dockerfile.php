# Используем базовый образ с PHP 8.3 и Apache на последней стабильной версии Ubuntu
FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
git \
zip \
curl \
mc \
unzip

# Устанавливаем Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
ARG COMPOSER_VERSION=2.7.2
RUN curl -sS https://getcomposer.org/installer | php -- \
--filename=composer \
--install-dir=/usr/local/bin \
--version=${COMPOSER_VERSION} \
&& composer clear-cache

RUN pecl install xdebug \
&& docker-php-ext-enable xdebug

COPY ./xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

EXPOSE 8080

WORKDIR /mnt/c/php-pro

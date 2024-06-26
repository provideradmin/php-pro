# Используем базовый образ с PHP 8.3 и PHP-FPM на последней стабильной версии Ubuntu
FROM php:8.3-fpm

# Installs extra libraries
RUN apt-get update && apt-get install -y \
git \
zip \
unzip

# Installs PHP extensions
RUN docker-php-ext-install \
pdo_mysql \
opcache

# Installs Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
ARG COMPOSER_VERSION=2.7.2
RUN curl -sS https://getcomposer.org/installer | php -- \
--filename=composer \
--install-dir=/usr/local/bin \
--version=${COMPOSER_VERSION} \
&& composer clear-cache

# Setup Xdebug
ARG XDEBUG_ENABLED=false
RUN if [ "$XDEBUG_ENABLED" = "true" ]; then pecl install xdebug \
&& docker-php-ext-enable xdebug \
&& echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
&& echo "xdebug.max_nesting_level=1000" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
; fi

WORKDIR /home/php-pro

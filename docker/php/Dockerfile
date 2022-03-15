FROM php:8.0

COPY --from=composer:2.0 /usr/bin/composer /usr/bin/composer

RUN apt-get update  -y && apt-get upgrade  -y

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/
RUN install-php-extensions simplexml
RUN install-php-extensions zip
RUN install-php-extensions redis
RUN composer install

USER root
WORKDIR /var/www

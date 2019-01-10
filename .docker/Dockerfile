FROM php:7.2-apache

LABEL MAINTAINER="RAY HUANG"

COPY . /srv/app
COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /srv/app

RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    bcmath

RUN apt-get update && apt-get install -y libmcrypt-dev \
    && pecl install mcrypt-1.0.1 \
    && docker-php-ext-enable mcrypt


RUN chown -R www-data:www-data /srv/app \
    && a2enmod rewrite
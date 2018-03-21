FROM php:7-apache

RUN apt-get update && \
    apt-get install -y libicu-dev && \
    docker-php-ext-install intl && \
    a2enmod rewrite headers

COPY config/apache/apache2.conf /etc/apache2/apache2.conf

COPY CHECKS /app/CHECKS

COPY src/ /var/www/html/

FROM php:7-apache

RUN apt-get update && \
    apt-get install -y libicu-dev --no-install-recommends && \
    docker-php-ext-install intl && \
    a2enmod rewrite headers && \
    rm -rf /var/lib/apt/lists/* && \
    apt-get purge -y --auto-remove libicu-dev

COPY config/apache/apache2.conf /etc/apache2/apache2.conf

COPY src/ /var/www/html/

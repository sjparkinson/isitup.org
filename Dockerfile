FROM php:8-apache

LABEL org.opencontainers.image.source https://github.com/sjparkinson/isitup.org

RUN apt-get update && \
    apt-get install -y libicu-dev --no-install-recommends && \
    docker-php-ext-install intl && \
    a2enmod remoteip rewrite headers && \
    rm -rf /var/lib/apt/lists/* && \
    apt-get purge -y --auto-remove libicu-dev

COPY config/apache/apache2.conf /etc/apache2/apache2.conf

COPY src/ /var/www/html/

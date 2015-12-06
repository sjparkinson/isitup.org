FROM php:7.0-apache

RUN a2enmod rewrite headers

COPY config/apache/apache2.conf /etc/apache2/apache2.conf

COPY CHECKS /app/CHECKS

COPY src/ /var/www/html/

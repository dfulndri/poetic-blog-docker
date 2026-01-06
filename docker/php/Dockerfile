FROM php:8.2-apache

# Install extension MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Aktifkan mod rewrite (penting buat framework nanti)
RUN a2enmod rewrite

WORKDIR /var/www/html

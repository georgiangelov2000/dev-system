FROM php:8.0-fpm

RUN usermod -u 1000 www-data

WORKDIR /var/www/html
RUN chown -R www-data:www-data /var/www/html
COPY src .

# Install dependencies for the operating system software
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    libzip-dev \
    unzip \
    git \
    libonig-dev \
    curl \
    libxml2-dev \
    libssl-dev \
    libcurl4-openssl-dev \
    libmemcached-dev \
    libmcrypt-dev \
    libmagickwand-dev \
    libpq-dev
    
# Install extensions for php
RUN docker-php-ext-install pdo pdo_mysql xmlwriter fileinfo calendar mbstring zip exif pcntl bcmath gettext
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd

# Install Redis PHP extension
RUN pecl install redis && docker-php-ext-enable redis

RUN mv "/usr/local/etc/php/php.ini-production" "/usr/local/etc/php/php.ini"
RUN rm "/usr/local/etc/php/php.ini-development"

WORKDIR /usr/local/etc/php
COPY php/php.ini ./

WORKDIR /usr/local/etc/php-fpm.d
COPY php/www.conf .

EXPOSE 9000

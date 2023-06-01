FROM php:8.1.16-fpm


RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) gd \
  && docker-php-ext-install -j$(nproc) pdo_mysql \
  && docker-php-ext-install -j$(nproc) mysqli \
  && docker-php-ext-install -j$(nproc) bcmath \
  && docker-php-ext-install -j$(nproc) opcache 

# if pecl can access
# RUN pecl install redis && docker-php-ext-enable redis


# if pecl cannot access,need download,local install
COPY ./redis-5.3.7.tgz /tmp/redis-5.3.7.tgz
COPY ./zip-1.21.1.tgz /tmp/zip-1.21.1.tgz

RUN cd /tmp \
  && pecl install redis-5.3.7.tgz \
  && pecl install zip-1.21.1.tgz \
  && rm -rf redis-5.3.7.tgz \
  && rm -rf zip-1.21.1.tgz \
  && docker-php-ext-enable redis zip

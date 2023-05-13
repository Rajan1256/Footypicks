FROM php:7.1.7-fpm

RUN apt-get update \
    && apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libmcrypt-dev  libpng12-dev libxml2-dev curl libcurl4-gnutls-dev\
    && docker-php-ext-install -j$(nproc) iconv mcrypt \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install bcmath \
#    && docker-php-ext-install cli \
    && docker-php-ext-install xml \
    && docker-php-ext-install curl \
    && docker-php-ext-install mbstring \
    && docker-php-ext-install pdo pdo_mysql

RUN usermod -u 1000 www-data

COPY config/php.ini /etc/php/7.1/fpm/php.ini

EXPOSE 9000
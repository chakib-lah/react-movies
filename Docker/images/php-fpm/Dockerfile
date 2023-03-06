FROM php:8.1-fpm

LABEL maintainer='Chakib LAHNAKI <chakib.lahnaki@gmail.com>'

RUN apt-get update && apt-get install -y \
    curl \
    openssl \
    unzip \
    make \
    openssl \
    procps \
    sudo \
    unzip \
    vim \
    libpng-dev \
    libjpeg-dev \
    zip \
    zlib1g \
    libfreetype-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg && \
        docker-php-ext-install gd \
    && docker-php-ext-configure opcache --enable-opcache &&\
        docker-php-ext-install opcache \
    && docker-php-ext-install mysqli pdo pdo_mysql

# install xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host = host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
## Todo: conf to be completed

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
&& composer --version

WORKDIR /var/www/ng-poc

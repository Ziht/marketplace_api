FROM php:7.2-fpm

RUN apt-get update && apt-get install -y \
libmcrypt-dev \
curl \
wget \
git \
libfreetype6-dev \
libjpeg62-turbo-dev \
libmcrypt-dev \
libpng-dev \
libzip-dev \
zip \
&& pecl install mcrypt-1.0.2 \
&& docker-php-ext-enable mcrypt\
&& docker-php-ext-configure zip --with-libzip \
&& docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd \
&& docker-php-ext-configure mysqli --with-mysqli=mysqlnd \
&& docker-php-ext-install -j$(nproc) iconv mbstring mysqli pdo pdo_mysql zip \
&& docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
&& docker-php-ext-install -j$(nproc) gd \
&& pecl install apcu-5.1.8 \
&& docker-php-ext-enable apcu \
&& pecl install xdebug-2.7.1 \
&& docker-php-ext-enable xdebug

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ADD ./php.ini /usr/local/etc/php/conf.d/40-custom.ini

WORKDIR /var/www

CMD ["php-fpm"]
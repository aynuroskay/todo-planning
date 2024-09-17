FROM php:7.4-fpm

WORKDIR /var/www/app

RUN apt-get update \
    && apt-get install -y nginx zlib1g-dev g++ git libicu-dev zip libzip-dev zip libfreetype6-dev libpng-dev libxslt1-dev librabbitmq-dev libssh-dev libffi-dev \
    && docker-php-ext-install zip intl opcache pdo pdo_mysql bcmath sockets gettext gd soap calendar exif ffi xsl mysqli pcntl shmop sysvmsg sysvsem sysvshm \
    && docker-php-ext-configure zip
RUN pecl install amqp redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-enable amqp

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . /var/www/app

RUN chown -R www-data:www-data /var/www/app

EXPOSE 9000
CMD ["php-fpm"]
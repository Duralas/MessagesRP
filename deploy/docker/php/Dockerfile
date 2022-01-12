FROM php:8-fpm as php

COPY --from=composer:2.2 /usr/bin/composer /usr/local/bin/composer

# Install Postgre PDO, git, Zip, intl
RUN apt-get update -y \
    && apt-get install -y libpq-dev libzip-dev git zip libicu-dev \
    && docker-php-ext-install pdo pdo_mysql zip intl

# Extra php lib : APCU, Redis, Xdebug
RUN pecl install apcu \
    && pecl install redis \
    && pecl install xdebug \
    && docker-php-ext-enable apcu redis xdebug

# Add custom configuration files
COPY ./conf/custom.xdebug.ini /usr/local/etc/php/conf.d/custom.xdebug.ini
COPY ./conf/custom.php.ini /usr/local/etc/php/conf.d/custom.php.ini

# For bin/ scripts
ENV COMPOSER_HOME=/composer

RUN \
    mkdir /composer \
    && php /usr/local/bin/composer global require \
        steevanb/php-parallel-processes "^0.2" \
        symfony/console "5.4.*" \
    && chmod -R 777 ${COMPOSER_HOME}

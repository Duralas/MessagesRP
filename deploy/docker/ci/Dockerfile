FROM ubuntu:20.04

COPY --from=composer:2.2 /usr/bin/composer /usr/local/bin/composer

ARG XDEBUG_CONFIG
ENV COMPOSER_HOME=/composer

RUN \
    apt-get update \
    && apt-get upgrade -y \
    # To add add-apt-repository
    && apt-get install -y software-properties-common \
    && LC_ALL=C.UTF-8 add-apt-repository ppa:ondrej/php \
    && apt-get install -y \
        bash \
        shellcheck \
        php8.0-cli \
        php8.0-simplexml \
        # For PHPUnit
        php8.0-mbstring \
        php8.0-cli \
        php8.0-simplexml \
        # For coverage \
        php8.0-xdebug \
        # For unused-scanner
        php8.0-mbstring \
        # For Composer
        curl zip php8.0-curl php8.0-zip

# Configure Composer
RUN \
    mkdir ${COMPOSER_HOME} \
    && echo '{"config":{"cache-dir": "/var/www/html/var/composer/cache","sort-packages":true}}' > ${COMPOSER_HOME}/composer.json

# Install CI tools
RUN \
    php8.0 /usr/local/bin/composer global require \
        steevanb/php-parallel-processes "^0.2" \
        symfony/console:5.4.* \
        maglnet/composer-require-checker:3.3.* \
        wapmorgan/php-deprecation-detector:2.0.* \
        steevanb/php-code-sniffs:4.2.* \
        phpstan/phpstan:0.12.* \
        phpstan/phpstan-deprecation-rules:0.12.* \
        phpstan/phpstan-strict-rules:0.12.* \
        phpstan/phpstan-phpunit:0.12.* \
        friendsofphp/php-cs-fixer:3.2.* \
        spaze/phpstan-disallowed-calls:1.9.* \
        ergebnis/phpstan-rules:0.15.* \
        insolita/unused-scanner:2.3.* \
        ergebnis/composer-normalize:2.15.* \
    && ln -s ${COMPOSER_HOME}/vendor/bin/composer-require-checker /usr/local/bin/composer-require-checker \
    && ln -s ${COMPOSER_HOME}/vendor/bin/phpcbf /usr/local/bin/phpcbf \
    && ln -s ${COMPOSER_HOME}/vendor/bin/phpdd /usr/local/bin/phpdd \
    && ln -s ${COMPOSER_HOME}/vendor/bin/phpcs /usr/local/bin/phpcs \
    && ln -s ${COMPOSER_HOME}/vendor/bin/phpstan /usr/local/bin/phpstan \
    && ln -s ${COMPOSER_HOME}/vendor/bin/unused_scanner /usr/local/bin/unused-scanner \

    # Purge
    && apt-get purge -y software-properties-common \
    && apt-get autoremove -y \
    && apt-get clean \
    && apt-get autoclean \
    && rm -rf /tmp/*

RUN chmod -R 777 ${COMPOSER_HOME}

# Configure xdebug
#    As usage is: docker build [OPTIONS] "${PROJECT_DIR}"
COPY ./conf/custom.xdebug.ini /etc/php/8.0/cli/conf.d/custom.xdebug.ini

WORKDIR /var/www/html

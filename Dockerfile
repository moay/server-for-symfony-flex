FROM node:lts-alpine as builder

COPY . /app
WORKDIR /app

RUN CI=true npm install --force
RUN npm run-script build

FROM php:7.4-fpm-alpine

ARG TIMEZONE="UTC"
ARG BUILD_ENV="prod"

SHELL ["sh", "-eo", "pipefail", "-c"]

# user build args
ARG PHP_USER_ID=1000
ARG PHP_GROUP_ID=1000

RUN set -x \
        && addgroup -g $PHP_GROUP_ID -S php \
        && adduser -u $PHP_USER_ID -D -S -G php php

# install composer and extensions: pdo_pgsql, intl, zip
RUN apk update && \
    apk add --no-cache -q \
    $PHPIZE_DEPS \
    bash \
    git \
    subversion \
    zip \
    unzip \
    icu-dev \
    libzip-dev \
    openssh-client && \
    docker-php-ext-configure intl && \
    docker-php-ext-configure zip && \
    docker-php-ext-install intl zip iconv ctype && \
    apk del $PHPIZE_DEPS && \
    rm -rf /tmp/* && \
    rm -rf /var/cache/apk/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/

# set timezone
RUN ln -snf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && \
    echo ${TIMEZONE} > /etc/timezone && \
    printf '[PHP]\ndate.timezone = "%s"\n', "$TIMEZONE" > \
    /usr/local/etc/php/conf.d/tzone.ini && "date"

# set memory limit
RUN echo "memory_limit=2048M" > /usr/local/etc/php/conf.d/memory-limit.ini


RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# automatically add new host keys to the user known hosts
RUN printf "Host *\n    StrictHostKeyChecking no" > /etc/ssh/ssh_config

RUN mkdir /app && chown php:php -R /app
WORKDIR /app

COPY --chown=php:php . .
COPY --from=builder --chown=php:php /app/public/build /app/public

ENV APP_ENV=prod

RUN if [ $BUILD_ENV = "prod" ]; then \
      composer install --no-dev --optimize-autoloader; \
      php bin/console cache:clear --no-warmup; \
      php bin/console cache:warmup; \
    else \
        composer install -o; \
    fi;

USER root


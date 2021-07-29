#############################################################
#                      GLOBAL ARGS                          #
#############################################################

ARG PHP_VERSION=7.4
ARG NGINX_VERSION=1.21
ARG NODE_VERSION=14

#############################################################
#                           NODEJS                          #
#############################################################

FROM node:${NODE_VERSION}-alpine AS server-for-symfony-flex-nodejs

WORKDIR /srv/server-for-symfony-flex

RUN set -eux; \
	apk add --no-cache \
		g++ \
		gcc \
		git \
		make \
		python2 \
	;

COPY package.json package-lock.json webpack.config.js ./
COPY assets ./assets

RUN set -eux; \
	npm install ; \
	npm cache clean --force

RUN npm run build

COPY docker/nodejs/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["npm", "run", "watch"]

#############################################################
#                           PHP-FPM                         #
#############################################################


FROM php:${PHP_VERSION}-fpm-alpine AS server-for-symfony-flex-php

WORKDIR /srv/server-for-symfony-flex

# persistent / runtime deps
RUN apk add --no-cache \
                acl \
                bash \
                file \
                gettext \
                git \
                mariadb-client \
                openssh-client \
                libxml2 \
                libuuid \
                bind-tools \
    ;

ARG XDEBUG_VERSION=3.0.4

RUN set -eux; \
        apk add --no-cache --virtual .build-deps \
                $PHPIZE_DEPS \
                coreutils \
                freetype-dev \
                icu-dev \
                libjpeg-turbo-dev \
                libpng-dev \
                libtool \
                libwebp-dev \
                libzip-dev \
                mariadb-dev \
                zlib-dev \
                libxml2-dev \
                util-linux-dev \
        ; \
        \
        docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype; \
        docker-php-ext-configure zip --with-zip; \
        docker-php-ext-install -j$(nproc) \
                exif \
                gd \
                intl \
                pdo_mysql \
                zip \
                bcmath \
                sockets \
                soap \
        ; \
        pecl install xdebug-${XDEBUG_VERSION}; \
        pecl clear-cache; \
        docker-php-ext-enable \
                xdebug \
        ; \
        runDeps="$( \
                scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
                        | tr ',' '\n' \
                        | sort -u \
                        | awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
        )"; \
        apk add --no-cache --virtual .sylius-phpexts-rundeps $runDeps; \
        \
        apk del .build-deps

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ARG PHP_DATE_TIMEZONE=UTC
COPY docker/php/php.ini.template /usr/local/etc/php/php.ini.template
RUN envsubst '${PHP_DATE_TIMEZONE}' < /usr/local/etc/php/php.ini.template > /usr/local/etc/php/php.ini

ARG FPM_PORT=9000
COPY docker/php/www.conf.template /usr/local/etc/php-fpm.d/www.conf.template
RUN envsubst '${FPM_PORT}' < /usr/local/etc/php-fpm.d/www.conf.template > /usr/local/etc/php-fpm.d/www.conf
RUN sed -i s/9000/$FPM_PORT/g /usr/local/etc/php-fpm.d/zz-docker.conf

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="${PATH}:/root/.composer/vendor/bin"

COPY composer.json composer.lock symfony.lock ./

RUN set -eux; \
    composer install --prefer-dist --no-autoloader --no-scripts --no-progress --no-dev; \
	composer clear-cache

COPY .env ./
COPY assets assets/
COPY bin bin/
COPY config config/
COPY public public/
COPY src src/
COPY templates templates/

RUN set -eux; \
	mkdir -p var/cache var/log; \
    chown -R www-data:www-data var/log; \
	composer dump-autoload --classmap-authoritative; \
	composer run-script post-install-cmd; \
	chmod +x bin/console; \
	sync

COPY --from=server-for-symfony-flex-nodejs /srv/server-for-symfony-flex/public public/

COPY docker/php/php-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

#############################################################
#                           NGINX                           #
#############################################################
FROM nginx:${NGINX_VERSION}-alpine AS server-for-symfony-flex-nginx

WORKDIR /srv/server-for-symfony-flex

COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf

ARG NGINX_PORT=8080
ARG FPM_HOSTNAME=localhost
ARG FPM_PORT=9000
COPY docker/nginx/conf.d/default.conf.template /etc/nginx/conf.d/default.conf.template
RUN envsubst '${NGINX_PORT} ${FPM_HOSTNAME} ${FPM_PORT}' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf

COPY --from=server-for-symfony-flex-php /srv/server-for-symfony-flex/public public/

RUN apk add --no-cache bash

COPY docker/nginx/wait-for-it.sh /
RUN chmod +x /wait-for-it.sh

ENV FPM_PORT=$FPM_PORT
ENV FPM_HOSTNAME=$FPM_HOSTNAME
CMD /wait-for-it.sh -t 0 $FPM_HOSTNAME:$FPM_PORT -- nginx -g "daemon off;"

FROM debian:trixie-slim AS base

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN set -xe \
    && apt-get update \
    && apt-get install -y --no-install-recommends \
    ca-certificates \
    gettext-base \
    mariadb-client \
    curl \
    php \
    php-cli \
    php-bcmath \
    php-common \
    php-curl \
    php-fpm \
    php-gd \
    php-iconv \
    php-intl \
    php-json \
    php-mbstring \
    php-mysql \
    php-tokenizer \
    php-xml \
    php-zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY docker/ /docker

WORKDIR /srv

# Application
FROM base AS app

ENV FPM_USER=www-data
ENV FPM_GROUP=www-data
ENV FPM_BIN_PATH=/usr/sbin/php-fpm8.4

EXPOSE 9000
CMD [ "/docker/init.sh" ]

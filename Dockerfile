FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        ca-certificates \
        curl \
        git \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libzip-dev \
        unzip \
        libicu-dev \
        libonig-dev \
        libxml2-dev \
        libpq-dev \
        libsqlite3-dev \
        libzip-dev \
        libssl-dev \
        libmemcached-dev \
        libyaml-dev \
        imagemagick \
        libmagickwand-dev \
        exiftool \
        mariadb-client \
        ghostscript \
        graphicsmagick \
        poppler-utils \
        pkg-config \
        g++ \
        cron \
        zip \
        jq \
        redis-tools \
        htop \
        crun \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        bcmath \
        pdo_mysql \
        gd \
        intl \
        exif \
        fileinfo \
        dom \
        opcache \
        mbstring \
        zip \
        pcntl \
        ctype \
        iconv \
        soap \
        sockets \
        simplexml \
        posix \
        pgsql \
        pdo_pgsql \
        pdo_sqlite

# Install PECL extensions
RUN pecl install redis \
    && pecl install apcu \
    && pecl install memcached \
    && pecl install yaml \
    && docker-php-ext-enable redis apcu memcached yaml \
    && pecl install imagick \
    && docker-php-ext-enable imagick

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy PHP config files (update paths for php:8.2-fpm)
COPY docker/00_docker.ini /usr/local/etc/php/conf.d/
COPY docker/pool.conf /usr/local/etc/php-fpm.d/www.conf

WORKDIR /srv

EXPOSE 9000
CMD [ "php-fpm", "-F" ]


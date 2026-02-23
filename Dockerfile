# syntax=docker/dockerfile:1

FROM php:8.2-cli

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libonig-dev \
        libxml2-dev \
    && docker-php-ext-install \
        mbstring \
        dom \
        xml \
        xmlwriter \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && rm -rf /var/lib/apt/lists/*

RUN { \
      echo "xdebug.mode=coverage"; \
      echo "xdebug.start_with_request=no"; \
    } > /usr/local/etc/php/conf.d/zz-xdebug-settings.ini

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /app

RUN git config --global --add safe.directory /app

CMD ["bash"]

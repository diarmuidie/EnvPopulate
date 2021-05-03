FROM php:8-cli

RUN apt-get update && apt-get install -y \
  git \
  zip \
  && rm -rf /var/lib/apt/lists/*

ENV XDEBUG_MODE=coverage
RUN pecl install xdebug-3.0.3 \
    && docker-php-ext-enable xdebug

# Install Composer
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /tmp
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

COPY composer.json ./

RUN composer install

COPY . .

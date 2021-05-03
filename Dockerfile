FROM php:7-cli

RUN apt-get update && apt-get install -y \
  git \
  zip \
  && rm -rf /var/lib/apt/lists/*

RUN pecl install xdebug-2.8.1 \
    && docker-php-ext-enable xdebug

# Install Composer
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /tmp
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY composer.lock composer.json ./

RUN composer install

COPY . .

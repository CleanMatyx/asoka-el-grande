FROM php:8.3-fpm-alpine
RUN apk add --no-cache $PHPIZE_DEPS icu-dev libzip-dev oniguruma-dev nodejs npm \
    && docker-php-ext-install pdo_mysql intl mbstring zip opcache \
    && apk del $PHPIZE_DEPS
WORKDIR /var/www/html
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN addgroup -g 1000 app && adduser -D -u 1000 -G app app \
    && sed -i 's/^user = www-data/user = app/; s/^group = www-data/group = app/' /usr/local/etc/php-fpm.d/www.conf
USER app
CMD ["php-fpm"]

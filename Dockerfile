FROM php:8.3-fpm-alpine

# Copy Caddy binary from official image — no download needed
COPY --from=caddy:2-alpine /usr/bin/caddy /usr/local/bin/caddy

RUN apk add --no-cache \
    nodejs \
    npm \
    postgresql-client \
    postgresql-dev \
    supervisor \
    libzip-dev \
    zip \
    unzip \
    $PHPIZE_DEPS

RUN docker-php-ext-install pdo pdo_pgsql opcache pcntl zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS postgresql-dev

RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN npm install && npm run build && rm -rf node_modules

RUN chown -R www-data:www-data storage bootstrap/cache

#COPY docker/Caddyfile       /etc/caddy/Caddyfile
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh   /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80 443

ENTRYPOINT ["/entrypoint.sh"]

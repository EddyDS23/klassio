FROM php:8.3-fpm AS php-base

RUN apt-get update && apt-get install -y --no-install-recommends \
    libcurl4-openssl-dev \
    libexif-dev \
    libonig-dev \
    libzip-dev \
    && docker-php-ext-install curl exif mbstring opcache pcntl pdo_mysql sockets zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

FROM php-base AS app

COPY . .

RUN mkdir -p \
        storage/app/private \
        storage/app/public \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/framework/testing \
        storage/logs \
    && composer install \
        --no-dev \
        --no-interaction \
        --prefer-dist \
        --optimize-autoloader \
    && php artisan storage:link --force \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]

FROM php-base AS development

COPY . .

RUN mkdir -p \
        storage/app/private \
        storage/app/public \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/framework/testing \
        storage/logs \
<<<<<<< HEAD
    && composer install --no-interaction --prefer-dist --optimize-autoloader
=======
        bootstrap/cache \
    && composer install \
        --no-interaction \
        --prefer-dist \
        --optimize-autoloader
>>>>>>> master

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]

FROM nginx:alpine AS web

COPY --from=app /var/www/html/public /var/www/html/public
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

RUN nginx -t

FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    libzip-dev \
    libexif-dev \
    libonig-dev \
    libicu-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring zip opcache bcmath intl exif pcntl gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/zz-opcache.ini
COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-klassio.ini

COPY . .

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT [ "/entrypoint.sh" ]

CMD [ "php-fpm" ]

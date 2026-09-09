#!/bin/bash
set -e

if [ "$1" = 'php-fpm' ] || [ -z "$1" ]; then

    echo "Instalando dependencias..."
    composer install --no-interaction --prefer-dist --optimize-autoloader

    echo "Generando app key..."
    php artisan key:generate --force

    echo "Esperando a MariaDB..."
    until php -r "
        try {
            new PDO(
                'mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'),
                getenv('DB_USERNAME'),
                getenv('DB_PASSWORD')
            );
            exit(0);
        } catch (Exception \$e) {
            exit(1);
        }
    "; do
        echo "MariaDB aún no está lista, reintentando..."
        sleep 2
    done
    echo "MariaDB lista."

    echo "Ejecutando migraciones..."
    php artisan migrate --force

    echo "Limpiando caché..."
    php artisan config:clear
    php artisan cache:clear
    php artisan view:clear
    php artisan route:clear

    echo "Ajustando permisos..."
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

    exec php-fpm
fi

exec "$@"
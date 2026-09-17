#!/bin/bash
set -e

if [ "$1" = 'php-fpm' ] || [ -z "$1" ]; then

    if [ ! -f vendor/autoload.php ]; then
        echo "Instalando dependencias..."
        composer install --no-interaction --prefer-dist --optimize-autoloader --no-progress
    fi

    if [ -z "${APP_KEY}" ]; then
        echo "Generando app key..."
        php artisan key:generate --force
    fi

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
    php artisan migrate --force --no-interaction

    echo "Optimizando Laravel (cachés regenerables, sin cambiar código)..."
    php artisan view:cache --no-interaction || true
    php artisan route:cache --no-interaction || true
    php artisan event:cache --no-interaction || true
    # config:cache solo si no es debug local, para no ocultar cambios de .env en desarrollo.
    if [ "${APP_ENV}" != "local" ] || [ "${APP_DEBUG}" = "false" ]; then
        php artisan config:cache --no-interaction || true
    else
        php artisan config:clear --no-interaction || true
    fi

    echo "Ajustando permisos (solo si hace falta, para no ralentizar el arranque)..."
    for d in /var/www/html/storage /var/www/html/bootstrap/cache; do
        if [ "$(stat -c %U "$d" 2>/dev/null)" != "www-data" ]; then
            chown -R www-data:www-data "$d"
        fi
    done
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

    exec php-fpm
fi

exec "$@"

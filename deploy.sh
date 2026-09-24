#!/bin/bash

set -e

cd /var/www/klassio

echo "==> Actualizando código..."

git fetch origin master
git reset --hard origin/master

echo "==> Construyendo contenedores..."

docker compose -f docker-compose.yml build

echo "==> Levantando servicios..."

docker compose -f docker-compose.yml up -d

echo "==> Ejecutando migraciones..."

docker compose -f docker-compose.yml exec -T app \
    php artisan migrate --force

echo "==> Optimizando Laravel..."

docker compose -f docker-compose.yml exec -T app \
    php artisan optimize

echo "==> Deployment terminado."

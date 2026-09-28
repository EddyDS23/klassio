#!/bin/bash
set -e

cd /var/www/klassio

echo "==> Actualizando código..."
git fetch origin master
git reset --hard origin/master

echo "==> Construyendo contenedores..."
docker compose build

echo "==> Levantando servicios..."
docker compose up -d

echo "==> Esperando a que la aplicación esté lista..."
sleep 5

echo "==> Limpiando caché de Laravel..."
docker compose exec -T app php artisan optimize:clear

echo "==> Verificando migraciones..."
docker compose exec -T app php artisan migrate:status

echo "==> Deployment terminado."

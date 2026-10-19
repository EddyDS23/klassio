#!/usr/bin/env bash
set -Eeuo pipefail

cd /var/www/klassio

compose=(docker compose --env-file .env -f docker-compose.yml -f docker-compose.prod.yml --profile realtime)
export IMAGE_TAG="${IMAGE_TAG:-latest}"

if [[ -n "$(git status --porcelain)" ]]; then
    echo "ERROR: Hay cambios locales en el VPS. No se actualizará el código para protegerlos." >&2
    git status --short >&2
    exit 1
fi

echo "==> Actualizando referencias de master..."
git fetch origin master
git merge --ff-only origin/master

echo "==> Validando configuración de producción..."
"${compose[@]}" config --quiet

echo "==> Descargando las imágenes de Docker Hub (tag ${IMAGE_TAG})..."
"${compose[@]}" pull app nginx reverb worker scheduler

echo "==> Iniciando MariaDB y esperando el estado saludable..."
"${compose[@]}" up -d mariadb

database_container="$("${compose[@]}" ps -q mariadb)"
database_status=""
for attempt in $(seq 1 45); do
    database_status="$(docker inspect --format '{{.State.Health.Status}}' "$database_container" 2>/dev/null || true)"
    if [[ "$database_status" == "healthy" ]]; then
        break
    fi
    sleep 2
done

if [[ "$database_status" != "healthy" ]]; then
    echo "ERROR: MariaDB no alcanzó el estado saludable." >&2
    "${compose[@]}" logs --tail=100 mariadb >&2
    exit 1
fi

echo "==> Ejecutando migraciones de forma explícita..."
"${compose[@]}" run --rm --no-deps app php artisan migrate --force

echo "==> Levantando la aplicación, Reverb, colas, scheduler y Nginx..."
"${compose[@]}" up -d --remove-orphans

echo "==> Esperando el estado saludable de Nginx..."
nginx_container="$("${compose[@]}" ps -q nginx)"
nginx_status=""
for attempt in $(seq 1 45); do
    nginx_status="$(docker inspect --format '{{.State.Health.Status}}' "$nginx_container" 2>/dev/null || true)"
    if [[ "$nginx_status" == "healthy" ]]; then
        break
    fi
    sleep 2
done

if [[ "$nginx_status" != "healthy" ]]; then
    echo "ERROR: Nginx no alcanzó el estado saludable." >&2
    "${compose[@]}" ps >&2
    "${compose[@]}" logs --tail=100 app nginx >&2
    exit 1
fi

echo "==> Despliegue terminado."
"${compose[@]}" ps

#!/bin/sh
set -eu

echo "[start] Booting Laravel application"

# Railway injects env vars at runtime. Clear old caches so Laravel reads fresh values.
php artisan optimize:clear

echo "[start] Discovering packages"
php artisan package:discover --ansi

if [ "${DB_CONNECTION:-}" = "mysql" ]; then
	: "${DB_HOST:?DB_HOST is required when DB_CONNECTION=mysql}"
	: "${DB_PORT:?DB_PORT is required when DB_CONNECTION=mysql}"
	: "${DB_DATABASE:?DB_DATABASE is required when DB_CONNECTION=mysql}"
	: "${DB_USERNAME:?DB_USERNAME is required when DB_CONNECTION=mysql}"
fi

echo "[start] Running migrations"
php artisan migrate --force

echo "[start] Caching configuration"
php artisan config:cache
php artisan route:cache

PORT="${PORT:-8080}"
echo "[start] Starting server on port ${PORT}"
exec php artisan serve --host=0.0.0.0 --port="${PORT}"
#!/bin/bash
set -e

echo "==> Running migrations..."
php artisan migrate --force

echo "==> Seeding (skipped if data exists)..."
php artisan db:seed-if-empty

echo "==> Starting server on port $PORT..."
exec php artisan serve --host 0.0.0.0 --port "$PORT"

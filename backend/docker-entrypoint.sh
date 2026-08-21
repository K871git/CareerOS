#!/bin/bash
set -e

echo "==> Starting server on port $PORT..."
php artisan serve --host 0.0.0.0 --port "$PORT" &
SERVER_PID=$!

echo "==> Running migrations..."
php artisan migrate --force

echo "==> Seeding (skipped if data exists)..."
php artisan db:seed-if-empty

echo "==> App ready."
wait $SERVER_PID

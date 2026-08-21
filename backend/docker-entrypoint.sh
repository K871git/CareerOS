#!/bin/bash
set -e

echo "==> Starting server on port $PORT..."
php artisan serve --host 0.0.0.0 --port "$PORT" &
SERVER_PID=$!

echo "==> Running migrations..."
php artisan migrate --force

if [ "$FORCE_SEED" = "true" ]; then
    echo "==> FORCE_SEED enabled — running all seeders..."
    php artisan db:seed --force
    echo "==> Seeding complete. Remove FORCE_SEED from env vars now."
else
    echo "==> Seeding (skipped if data exists)..."
    php artisan db:seed-if-empty
fi

echo "==> App ready."
wait $SERVER_PID

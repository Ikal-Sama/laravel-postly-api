#!/usr/bin/env bash
# Kept for reference; startup logic lives in scripts/start.sh
set -e
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --working-dir=/var/www/html
php artisan config:cache
php artisan route:cache
php artisan view:cache || true
php artisan migrate --force

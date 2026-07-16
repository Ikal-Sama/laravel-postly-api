#!/usr/bin/env bash
set -e

export PORT="${PORT:-10000}"

# Render assigns $PORT — bind Nginx to it from template
envsubst '${PORT}' < /var/www/html/conf/nginx/nginx-site.conf.template \
  > /etc/nginx/sites-available/default

# Ensure writable dirs exist
mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache

if [ ! -f vendor/autoload.php ]; then
  composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
fi

echo "Caching config..."
php artisan config:clear
php artisan config:cache

echo "Caching routes..."
php artisan route:cache

echo "Caching views..."
php artisan view:cache || true

echo "Running migrations..."
php artisan migrate --force

echo "Starting Nginx + PHP-FPM on port ${PORT}..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf

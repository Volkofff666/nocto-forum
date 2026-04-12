#!/bin/sh
set -e

echo "=== nocto.hub starting ==="

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force

echo "=== Starting supervisor (php-fpm + caddy) ==="
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf

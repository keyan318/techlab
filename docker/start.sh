#!/usr/bin/env bash
# Runs every time the Render container starts.
set -e
cd /var/www/html

php artisan storage:link >/dev/null 2>&1 || true
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force

# Background jobs (e.g. reading uploaded course materials). Restarts itself if it stops.
( while true; do
    su -s /bin/bash www-data -c "php artisan queue:work --sleep=3 --tries=3 --max-time=3600" || true
    sleep 5
  done ) &

chown -R www-data:www-data storage bootstrap/cache
exec apache2-foreground

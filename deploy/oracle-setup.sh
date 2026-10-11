#!/usr/bin/env bash
# TechLab one-shot server setup for a fresh Ubuntu 24.04 machine (Oracle Always Free ARM works).
# Run as the default user (ubuntu) with:  bash oracle-setup.sh
# Safe to re-run: it updates the code and restarts the app.
set -euo pipefail

REPO="${REPO:-https://github.com/keyan318/techlab.git}"
BRANCH="${BRANCH:-networking-course}"
APP_DIR=/var/www/techlab

echo "==> Installing packages (PHP 8.3, Nginx, Supervisor, Node 20)"
sudo apt-get update -y
sudo apt-get install -y nginx supervisor git unzip curl ca-certificates \
  php8.3-fpm php8.3-cli php8.3-pgsql php8.3-sqlite3 php8.3-mbstring php8.3-xml \
  php8.3-curl php8.3-zip php8.3-bcmath php8.3-intl php8.3-gd
if ! command -v node >/dev/null || [ "$(node -v | cut -d. -f1 | tr -d v)" -lt 20 ]; then
  curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
  sudo apt-get install -y nodejs
fi
if ! command -v composer >/dev/null; then
  curl -fsSL https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
fi

echo "==> Getting the code ($BRANCH)"
sudo mkdir -p "$APP_DIR" && sudo chown "$USER":www-data "$APP_DIR"
if [ -d "$APP_DIR/.git" ]; then
  git -C "$APP_DIR" fetch origin && git -C "$APP_DIR" checkout "$BRANCH" && git -C "$APP_DIR" pull origin "$BRANCH"
else
  git clone --branch "$BRANCH" "$REPO" "$APP_DIR"
fi
cd "$APP_DIR"

if [ ! -f .env ]; then
  cp .env.example .env
  echo
  echo "!! .env created from .env.example. Edit it NOW (nano .env), then re-run this script."
  echo "   Set: APP_ENV=production APP_DEBUG=false APP_URL=<your url> DB_* (Supabase),"
  echo "        NVIDIA_NIM_API_KEY, WEB_SEARCH_API_KEY, MAIL_*  and SESSION_SECURE_COOKIE=true"
  exit 0
fi

echo "==> Installing dependencies and building assets"
composer install --no-dev --optimize-autoloader --no-interaction
npm ci && npm run build
if [ -f resources/netsim-app/package.json ]; then
  (cd resources/netsim-app && npm ci && npm run build) || echo "NetSim build failed - check its README"
fi

grep -q '^APP_KEY=base64' .env || php artisan key:generate --force
php artisan migrate --force
php artisan storage:link || true

sudo chown -R "$USER":www-data storage bootstrap/cache
sudo chmod -R ug+rwX storage bootstrap/cache

php artisan config:cache && php artisan route:cache && php artisan view:cache

echo "==> Nginx"
sudo tee /etc/nginx/sites-available/techlab >/dev/null <<NGINX
server {
    listen 80 default_server;
    server_name _;
    root $APP_DIR/public;
    index index.php;
    client_max_body_size 50M;

    location / { try_files \$uri \$uri/ /index.php?\$query_string; }
    location ~ \.php\$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_read_timeout 300;      # Astro streams answers
        fastcgi_buffering off;
    }
    location ~ /\.(?!well-known) { deny all; }
}
NGINX
sudo ln -sf /etc/nginx/sites-available/techlab /etc/nginx/sites-enabled/techlab
sudo rm -f /etc/nginx/sites-enabled/default
sudo sed -i 's/^;\?upload_max_filesize.*/upload_max_filesize = 50M/; s/^;\?post_max_size.*/post_max_size = 50M/' /etc/php/8.3/fpm/php.ini
sudo nginx -t && sudo systemctl reload nginx && sudo systemctl restart php8.3-fpm

echo "==> Queue worker (Supervisor)"
sudo tee /etc/supervisor/conf.d/techlab-worker.conf >/dev/null <<SUP
[program:techlab-worker]
command=php $APP_DIR/artisan queue:work --sleep=3 --tries=3 --max-time=3600
user=$USER
autostart=true
autorestart=true
stopasgroup=true
stdout_logfile=$APP_DIR/storage/logs/worker.log
SUP
sudo supervisorctl reread && sudo supervisorctl update && sudo supervisorctl restart techlab-worker || true

echo "==> Scheduler (cron)"
( crontab -l 2>/dev/null | grep -v 'techlab.*schedule:run' ; echo "* * * * * cd $APP_DIR && php artisan schedule:run >> /dev/null 2>&1" ) | crontab -

echo
echo "DONE. Test locally on the server:  curl -I http://localhost"
echo "Quick public test (no domain needed):"
echo "  curl -fsSL -o cloudflared.deb https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-arm64.deb && sudo dpkg -i cloudflared.deb"
echo "  cloudflared tunnel --url http://localhost:80"

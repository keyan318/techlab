# TechLab production image for Render (Docker runtime): PHP 8.4 + Apache serving public/.
# No Vite step: the pages load their CSS/JS straight from public/ (no @vite in any view),
# and the simulators (netsim-app, citadel-sim, relay-console) are already built in public/.

FROM php:8.4-apache-bookworm

ADD https://github.com/mlocati/docker-php-extension-installer/releases/download/2.7.13/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && install-php-extensions pdo_pgsql pgsql zip intl gd bcmath exif opcache pcntl apcu \
 && apt-get update && apt-get install -y --no-install-recommends unzip git \
 && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Apache: serve public/, listen on Render's $PORT, no gzip (it would hold back Astro's streamed answers).
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/zz-techlab.ini
COPY docker/apache-tuning.conf /etc/apache2/conf-available/techlab-tuning.conf
RUN a2enmod rewrite headers && a2dismod -f deflate && a2enconf techlab-tuning \
 && sed -i 's/^Listen 80$/Listen ${PORT}/' /etc/apache2/ports.conf

WORKDIR /var/www/html
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction --prefer-dist

COPY . .
RUN composer dump-autoload --optimize --no-dev \
 && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs storage/app/public bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache

COPY docker/start.sh /usr/local/bin/techlab-start
RUN chmod +x /usr/local/bin/techlab-start
ENV PORT=10000 APP_ENV=production APACHE_MAX_WORKERS=25
EXPOSE 10000
CMD ["techlab-start"]

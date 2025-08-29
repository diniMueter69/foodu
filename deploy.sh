#!/usr/bin/env bash
set -euo pipefail
git fetch --all
git reset --hard origin/main
php /usr/local/bin/composer install --no-dev --prefer-dist --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "✅ Deploy ok"

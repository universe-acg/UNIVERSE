#!/usr/bin/env bash
set -euo pipefail
cd "${FORGE_SITE_PATH:?FORGE_SITE_PATH must be set}"
exec 9>var/deployment.lock
flock -n 9 || { echo 'Another UNIVERSE deployment is running.'; exit 1; }
git pull --ff-only origin master
export APP_ENV=prod APP_DEBUG=0
php8.2 /usr/local/bin/composer install --no-dev --no-interaction --prefer-dist --no-scripts --no-plugins --optimize-autoloader
npm ci --include=dev
php8.2 bin/console assets:install public
npm run build
php8.2 bin/console doctrine:migrations:migrate --no-interaction
php8.2 bin/console cache:clear --no-warmup
php8.2 bin/console cache:warmup
php8.2 bin/console doctrine:schema:validate
curl --fail --silent --show-error "${APP_URL:?APP_URL must be set}/health"

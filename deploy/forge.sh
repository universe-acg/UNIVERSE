#!/usr/bin/env bash
set -euo pipefail
cd "${FORGE_SITE_PATH:?FORGE_SITE_PATH must be set}"
mkdir -p var
exec 9>"${FORGE_SITE_ROOT:-$FORGE_SITE_PATH}/deployment.lock"
flock -n 9 || { echo 'Another UNIVERSE deployment is running.'; exit 1; }
if [ -z "${FORGE_RELEASE_DIRECTORY:-}" ]; then git pull --ff-only origin master; fi
export APP_ENV=prod APP_DEBUG=0
php8.2 scripts/validate_demo_data.php
php8.2 /usr/local/bin/composer install --no-dev --no-interaction --prefer-dist --no-scripts --no-plugins --optimize-autoloader
npm ci --include=dev
php8.2 bin/console assets:install public
npm run build
php8.2 bin/console lint:twig templates
php8.2 bin/console doctrine:migrations:migrate --no-interaction
php8.2 bin/console app:menus:sync
php8.2 bin/console app:help-center:seed
php8.2 bin/console app:community:seed
php8.2 bin/console app:demo:repair-images
php8.2 bin/console app:pages:sync
php8.2 bin/console app:brand:install
php8.2 bin/console cache:clear --no-warmup
php8.2 bin/console cache:warmup
php8.2 bin/console doctrine:schema:validate
curl --fail --silent --show-error "${APP_URL:?APP_URL must be set}/health"

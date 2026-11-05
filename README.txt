UNIVERSE, Deree demonstration

Original interface, orange theme, form styling, navigation and branding are
preserved. See IMPLEMENTATION.md for verified results and remaining test scope.

Runtime: PHP 8.2, MySQL 8, Composer 2, Node 22.18 or later within Node 22.
Install the PHP extensions required by composer.json, including GD and intl.

Local installation:
1. composer install --no-scripts --no-plugins
2. Create private .env.local overrides for APP_SECRET, APP_URL and DATABASE_URL.
   Never commit credentials. Use a separate database for the demonstration.
3. php bin/console doctrine:migrations:migrate --no-interaction
4. APP_DEMO=1 php bin/console app:demo:seed --reference-date=2026-11-05
5. php bin/console assets:install public
6. npm ci --include=dev
7. npm run build
8. php -S 127.0.0.1:8098 -t public public/index.php

The seed command is transactional and repeatable. It does not restore the
historical SQL backup or purge unrelated records. Generated demo credentials
are written privately to var/demo-credentials.json; never publish that file.
Email and SMS are captured privately in var/log/delivery and are not delivered.

Verification:
  vendor/bin/phpunit
  php scripts/audit_routes.php
  php bin/console lint:container
  php bin/console lint:twig templates
  php bin/console doctrine:schema:validate
  composer audit --locked
  npm audit --package-lock-only

For production use APP_ENV=prod and APP_DEBUG=0, serve only public/, and keep
var/private, logs, sessions and environment files outside the web root.
Production: https://universe-deree-live-3b97bb.on-forge.com
Forge: helppoint-production, site 3483627.
A passing template/route check is not an exhaustive workflow audit.

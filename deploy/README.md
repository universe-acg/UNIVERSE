Deploy only `public/` through Nginx/PHP 8.2 FPM. Keep `.env.local`, `var/private`,
sessions, delivery captures and demo credentials outside the document root.

Set private `.env.local`: APP_ENV=prod, APP_DEBUG=0, APP_URL=https://the-site,
APP_SECRET=random, DATABASE_URL=the-dedicated-MySQL-database, APP_DEMO=1,
MAILER_DSN=null://null. No real email or SMS deliveries occur.

Use `deploy/forge.sh` as the Forge deployment script with FORGE_SITE_PATH and
APP_URL exported. It never reseeds on a routine deploy. Run demo seeding explicitly
with the intended reference date. Back up the database and private uploads before
schema changes. Roll back code to the previous verified commit; restore the
matching database backup for a migration rollback. Do not run migration down on
an unreviewed production schema.

Scheduler, Forge user, every day at 08:00 Europe/Athens:
`cd /home/forge/universe-deree-7734d2.on-forge.com && APP_ENV=prod APP_DEBUG=0 php8.2 bin/console app:send-reminders`

The chat UI uses authenticated HTTP polling. The optional `websocket:server`
command runs only on 127.0.0.1:8302 and requires signed, expiring subscriptions.
Do not expose that port directly.

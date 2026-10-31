# Deployment Guide

## Local development (Windows / XAMPP)

```
# from C:\xampp\htdocs\edudev
composer install            # or: php C:/ProgramData/ComposerSetup/bin/composer.phar install
npm install
copy .env.example .env      # then set DB_* and MYSQLDUMP_PATH
php artisan key:generate
mysql -uroot -e "CREATE DATABASE edudev CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
php artisan migrate --seed
php artisan edudev:create-admin you@example.com --name="Your Name"
npm run build               # or `npm run dev` while editing
php artisan serve           # http://127.0.0.1:8000
```

- User sign-in: `http://127.0.0.1:8000/login`
- Staff sign-in: `http://127.0.0.1:8000/admin/login` (or your `ADMIN_PATH`)
- Emails go to `storage/logs/laravel.log` (`MAIL_MAILER=log`).
- Queued jobs need `php artisan queue:work`. `composer run dev` starts the server, queue, logs and Vite together.

## Production (recommended: a small Linux VPS)

Use Ubuntu LTS with Nginx, PHP 8.3-FPM, MySQL 8, Redis and Supervisor (for queue workers), plus a cron entry for `php artisan schedule:run` every minute. Use HTTPS (Let's Encrypt).

```
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force && php artisan db:seed --force
php artisan config:cache route:cache view:cache event:cache
```

**Shared hosting** works for a pilot only if it offers SSH, cron and PHP 8.3. Without persistent workers, set `QUEUE_CONNECTION=database` and run `queue:work --stop-when-empty` from cron each minute. AI requests will be slower to start.

See the production checklist in SECURITY_AND_PRIVACY.md.

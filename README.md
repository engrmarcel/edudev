# EduDEV

**Learn Smarter. Achieve More.** An AI-powered learning and examination preparation platform by MacoDev Limited.

Status: **Phase 1 (foundation and brand system) complete.** See `docs/IMPLEMENTATION_ROADMAP.md`.

## Quick start (Windows / XAMPP)

```
composer install && npm install && npm run build
copy .env.example .env && php artisan key:generate
php artisan migrate --seed
php artisan edudev:create-admin you@example.com --name="Your Name"
php artisan serve
```

- Students and users: http://127.0.0.1:8000/login
- Staff / admin panel: http://127.0.0.1:8000/admin/login (path set by `ADMIN_PATH`)
- Tests: `vendor/bin/pest` (needs the MySQL database `edudev_testing`)
- Schema export: `php artisan edudev:export-schema` → `database/sql/edudev.sql`

Documentation lives in `docs/`. Start with `REPOSITORY_AUDIT.md` and `ARCHITECTURE.md`.

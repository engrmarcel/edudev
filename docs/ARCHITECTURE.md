# Architecture

## In plain English

EduDEV is one Laravel application (a "modular monolith"). Different areas such as tutoring, exams and billing live in separate folders with clear boundaries, but they run as one program on one database. This is simpler to build, test and host than many small services, and areas can be split out later if traffic ever requires it.

## Stack

| Layer | Technology |
|---|---|
| Language / framework | PHP 8.3, Laravel 12 |
| UI | Livewire 4 + Volt (single-file components), Flux UI, Tailwind CSS 4, Vite |
| Database | MySQL 8 / MariaDB 10.4+ (utf8mb4) |
| Auth | Laravel session auth (starter kit), spatie/laravel-permission |
| Queue / cache / sessions | Database driver locally; Redis recommended in production |
| Tests | Pest 3 (PHPUnit 11) |

## Folder map

```
app/
  Console/Commands/      edudev:create-admin, edudev:export-schema
  Enums/                 Role, Permission (+ role→permission matrix)
  Http/Controllers/      LegalPageController, System/HealthController
  Http/Middleware/       AssignRequestId, SecurityHeaders, EnsureAccountIsActive
  Models/                User, SystemSetting, AuditLog
  Services/Branding/     BrandManager  (single source of truth for branding)
  Services/Audit/        AuditLogger   (append-only audit records)
  Support/helpers.php    brand(), money_format_minor()
config/edudev.php        Brand defaults, admin path, locale, policy versions
resources/views/
  components/layouts/    app (signed-in), auth (user sign-in), admin-auth (staff sign-in), public
  livewire/admin/        dashboard, branding, audit-log, auth/login
  legal/                 draft policy pages
  errors/                branded error pages
```

As modules arrive, each one gets its own `app/Domain/<Module>` (or `app/Services/<Module>`) namespace, with its models, services, policies and tests.

## Key design rules

1. **Permissions, not role names, in code.** Code checks `can('settings.branding.manage')`, never "is admin". Roles are bundles of permissions defined in `App\Enums\Permission::matrix()`.
2. **Two sign-in doors, one account system.** Users sign in at `/login` and staff at `/{ADMIN_PATH}/login`. Both use the same `web` guard and `users` table. Each page refuses the other's accounts, and the admin routes also require the `admin.access` permission. Signed-out visits to admin URLs go to the staff page.
3. **Branding is data.** Templates use `$brand->get(...)` or `brand('key')`, never hard-coded names or colours.
4. **Money is integers** in minor units (kobo), never floats.
5. **Time is stored in UTC** and displayed in `Africa/Lagos` by default.
6. **Secrets live only in `.env`** or a secret manager, never in the database, config defaults or the browser.
7. **Slow or expensive work (AI, email, exports) goes on queues.**
8. **Every sensitive action is audited** through `AuditLogger`.

## Request lifecycle additions

`AssignRequestId` (first) → … → `EnsureAccountIsActive` (web group) → controller → `SecurityHeaders` (last).

Each response carries `X-Request-Id`, which also appears in every log line and on error pages. Support can ask a user for that reference to find the matching log lines.

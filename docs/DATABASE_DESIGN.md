# Database Design

**Source of truth:** `database/migrations/`. The SQL file `database/sql/edudev.sql` is generated from the migrated database. Never edit it by hand.

## Current tables (end of Phase 2)

| Table | Purpose | Notes |
|---|---|---|
| users | Accounts | + `status` (active/suspended, indexed), `suspended_at`, `suspension_reason`, `last_login_at`. Status is not mass-assignable. |
| password_reset_tokens, sessions | Laravel auth | |
| cache, cache_locks, jobs, job_batches, failed_jobs | Framework | Database drivers locally |
| roles, permissions, model_has_roles, model_has_permissions, role_has_permissions | spatie/laravel-permission | Reference data seeded by `RolesAndPermissionsSeeder` |
| system_settings | Admin-editable settings (`group`, `key`, JSON `value`, `updated_by`) | Unique (group, key). **Never secrets.** |
| audit_logs | Append-only audit trail | `actor_id` set to NULL if the user is deleted; `actor_label` keeps the name. Model blocks update and delete. Indexed by (action, created_at). |
| migrations | Migration history | |
| user_profiles *(Phase 2)* | One per user: `account_type`, plus only the fields that type needs (class level and exam goals; organisation name, state and phone) and `onboarded_at` | Unique user_id; cascades with the user. No date of birth, address or ID numbers. |
| policy_acceptances *(Phase 2)* | Which policy version a user accepted, when, from which IP | Unique (user, policy, version) |
| account_deletion_requests *(Phase 2)* | pending → completed / rejected / cancelled, with reviewer and note | `user_id` set to NULL when the account is deleted, so the record of the deletion survives |

See `ERD.md` for relationships.

## Conventions

- `utf8mb4_unicode_ci` for all tables.
- Foreign keys are always declared. Deletion behaviour is chosen per relationship: financial, exam and audit records are **never** cascade-deleted.
- Money is held in integer minor units, in columns named `*_minor`, with a `currency` column.
- Statuses are short strings with application enums, not MySQL `ENUM`, so new states don't need a table rebuild.
- Every institution-owned table will carry `institution_id`, and every query on it will be scoped server-side (Phase 9).

## SQL export

```
php artisan migrate
php artisan db:seed
php artisan edudev:export-schema        # writes database/sql/edudev.sql
```

- **Contents:** the full schema, plus reference rows from `migrations`, `roles`, `permissions` and `role_has_permissions`. No users, sessions, payments or student content.
- **Tested:** imported into an empty database → 19 tables (end of Phase 2).
- Requires `mysqldump`. Set `MYSQLDUMP_PATH` in `.env` (XAMPP: `C:/xampp/mysql/bin/mysqldump.exe`).
- This is **not a backup**. See BACKUP_AND_RESTORE.md.

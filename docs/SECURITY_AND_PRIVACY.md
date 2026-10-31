# Security and Privacy

This document describes what is **implemented**. It is not a compliance certificate. Compliance with the Nigeria Data Protection Act 2023 needs review by qualified counsel.

## Implemented (Phase 1)

| Control | Where |
|---|---|
| **Separate staff sign-in** at `/{ADMIN_PATH}/login`. User `/login` refuses staff accounts; the staff page refuses non-staff with the same generic error as a wrong password (no account discovery). Stricter lockout: 5 tries, then 15 minutes. No "remember me". Successful staff sign-ins are audited; rejected ones are logged with a hashed email. | `livewire/admin/auth/login`, `livewire/auth/login` |
| Configurable admin path (`ADMIN_PATH`), `noindex` on the staff page | `config/edudev.php` |
| Permission checks on routes **and** inside every Livewire action | `routes/web.php`, `Gate::authorize` in components |
| Least-privilege role matrix; only super admins hold `roles.manage` | `App\Enums\Permission::matrix()` |
| Suspended accounts blocked at sign-in **and** signed out on their next request | `EnsureAccountIsActive` |
| `status` and suspension fields are not mass-assignable; strict models in dev and tests | `User`, `AppServiceProvider` |
| Append-only audit log | `AuditLog`, `AuditLogger` |
| Security headers: nosniff, SAMEORIGIN framing, referrer policy, permissions policy, HSTS on HTTPS | `SecurityHeaders` |
| Request IDs, with client-supplied IDs validated | `AssignRequestId` |
| Health check exposes only ok/fail | `HealthController` |
| Brand colour values validated as strict hex before being printed into CSS; branding key allow-list | `BrandManager`, branding page |
| Passwords and tokens never flashed to the session or logs | `bootstrap/app.php` |
| Admin passwords entered interactively only (min. 12 characters, mixed case, numbers, symbols) | `edudev:create-admin` |
| HTTPS forced in production | `AppServiceProvider` |

## Two-step login (Phase 2.1)

| Setting | Default | Effect |
|---|---|---|
| `TWO_FACTOR_ENABLED` | unset → off locally, **on when `APP_ENV=production`** | Shows Settings → Two-step login and asks for codes at sign-in |
| `TWO_FACTOR_REQUIRED_ROLES` | all staff roles | These users must set it up before using the app, and can't turn it off |

- **Codes:** TOTP (works with Google Authenticator, Microsoft Authenticator and Authy). ±1 step allowed for clock drift. Each code works only once.
- **Recovery:** 8 single-use recovery codes. Admins can reset two-step login for a user (audited), after verifying who they are by other means.
- **Storage:** the secret and recovery codes are encrypted with `APP_KEY`. Rotating `APP_KEY` makes existing secrets unreadable, so every user would have to set it up again. See the deployment guide.
- **Sign-in:** no session is created until the password, account status, portal and code checks all pass.

## Planned

- Content-Security-Policy, once the payment and analytics scripts are chosen.
- Upload scanning and private storage (Phase 5).
- Webhook signature verification and idempotency (Phase 7).
- Tenant isolation tests (Phase 9).
- Data export and deletion workflows (Phase 2).
- Breach response procedure (before launch).

## Production checklist

`APP_ENV=production` (this also switches two-step login on), `APP_DEBUG=false`, HTTPS only, `SESSION_SECURE_COOKIE=true`, a non-obvious `ADMIN_PATH`, a least-privilege database user, `.env` outside the web root, and `storage/` not publicly listable.

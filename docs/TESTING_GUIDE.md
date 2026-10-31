# Testing Guide

## One-time setup

```
mysql -uroot -e "CREATE DATABASE edudev_testing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
npm run build        # pages need the built assets
```

## Run

```
vendor/bin/pest                    # everything
vendor/bin/pest --filter=Admin     # one area
```

Tests use the **MySQL** database `edudev_testing` (see `phpunit.xml`), reset between tests. They never touch `edudev`.

## Phase 1 coverage

| File | Covers |
|---|---|
| `Feature/Admin/AdminSignInTest` | Separate staff and user sign-in pages; correct redirects for signed-out visitors; each page refuses the other's accounts; suspended staff; staff lockout; logout destination; audit of staff sign-in |
| `Feature/Admin/AdminAccessTest` | Students blocked from admin; content managers can't edit branding or read audit logs; Livewire action authorisation; audit log is append-only |
| `Feature/Foundation/BrandingTest` | Defaults, overrides shown site-wide, CSS-injection protection, key allow-list, reset |
| `Feature/Foundation/PlatformFoundationTest` | Security headers, request IDs, health check, legal pages, branded 404, student role on registration, suspended-session sign-out, mass-assignment guard |
| `Feature/Accounts/RegistrationAndOnboardingTest` *(Phase 2)* | Consent required and recorded; onboarding per account type with required and discarded fields; staff roles can't be self-selected; role swap; role-aware redirects |
| `Feature/Accounts/AccountAdministrationTest` *(Phase 2)* | User search; permission blocks; suspend/reactivate with session kill and audit; no self-action; only super admins change roles or touch super admins; last super admin protected |
| `Feature/Accounts/AccountDeletionTest` *(Phase 2)* | One pending request; user cancel; staff complete (record kept), reject needs a note; no double-processing; permission and staff-account guards |
| `Feature/Accounts/TwoFactorTest` *(Phase 2.1)* | Off unless enabled; setup with an encrypted secret; wrong and replayed codes rejected; recovery codes single-use; no session before the code; staff portal; staff forced to set up and can't turn off; admin reset; secrets never serialised |
| `Unit/FoundationUnitTest` | Money formatting, permission matrix |
| `Feature/Auth/*`, `Feature/Settings/*` | Starter-kit authentication and profile tests (unchanged) |

Not yet automated: browser and visual tests (Phase 12), and the SQL export (verified manually by importing into an empty database).

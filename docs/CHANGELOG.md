# Changelog

## [Phase 2.1] — 2026-10-31 — Two-step login

### Added
- Two-step login for **all users** (authenticator-app codes, RFC 6238), on both the user and staff sign-in pages.
- Switch: `TWO_FACTOR_ENABLED`. Unset means **off** in local and testing and **on** when `APP_ENV=production`.
- When on: anyone can turn it on in Settings → Two-step login. Roles in `TWO_FACTOR_REQUIRED_ROLES` (default: all staff) are held on the setup screen until they do, and can't turn it off.
- 8 single-use recovery codes, which can be regenerated with a password.
- Each code can only be used once (replay protection). 5 wrong codes per 5 minutes locks the step. A half-finished sign-in expires after 10 minutes.
- The secret and recovery codes are encrypted at rest and hidden from serialisation. The QR code is generated locally.
- Admins can reset two-step login for a user who lost their phone (audited; ends the user's sessions).
- Sign-in now checks the password, suspension and portal **before** creating a session.

### Tests
- 111 passed, 0 failed (326 assertions). Migration rollback tested; SQL export regenerated.

## [Phase 2] — 2026-10-31 — Authentication, permissions and onboarding

### Added
- The full product brief is saved as `docs/PRODUCT_REQUIREMENTS.md`, and the roadmap is realigned to brief §23.
- Registration requires agreeing to the Terms and Privacy Policy; the accepted versions are recorded (`policy_acceptances`).
- Onboarding at `/welcome/setup`: student, guardian, teacher, tutor, school administrator or CBT centre operator, each with a minimal profile (`user_profiles`). Staff roles can't be self-selected.
- Role-aware redirects after sign-in, registration and onboarding.
- Admin → Users: search, filter, details, suspend (reason required; sessions ended at once) and reactivate, plus role changes (super admins only). Guards stop self-action, stop non-super-admins changing super admins, and keep at least one active super admin.
- Account deletion requests: users request or cancel; staff complete (account deleted, request record kept) or reject with a note (`account_deletion_requests`). Admin → Deletion requests.
- Dashboards by account type; non-student types see an honest "coming soon" panel.

### Changed
- Settings → Delete account now submits a request instead of deleting immediately.

### Tests
- 96 passed, 0 failed (267 assertions). Migration rollback of the three new tables tested. SQL export regenerated: 19 tables, imports cleanly.

## [Phase 1] — 2026-10-31 — Foundation and brand system

### Added
- Laravel 12 Livewire starter kit on MySQL; Pest 3; spatie/laravel-permission.
- Central configurable branding (`config/edudev.php` + Admin → Branding), runtime brand colours, design tokens, original draft logo and favicon.
- Public layout, homepage and draft legal pages (terms, privacy, acceptable use, refunds, AI safety), with a "not affiliated with WAEC/NECO/JAMB" notice.
- **Separate sign-in pages for the admin panel (`/admin/login`, path configurable) and for user accounts (`/login`)**, each refusing the other's accounts, with correct signed-out redirects and logout destinations.
- 10 roles and 14 permissions; admin overview, branding editor, audit log viewer.
- Append-only audit log; account suspension; request IDs; security headers; `/health`; branded error pages.
- `edudev:create-admin`, `edudev:export-schema`; `database/sql/edudev.sql`.
- Docs: audit, roadmap, architecture, database, ERD, branding, security, testing, deployment, backup, admin guide, limitations.

### Fixed
- Starter kit incompatible with Livewire 4's default layout path.
- Forced dark mode removed from layouts.

### Tests
- 65 passed, 0 failed (176 assertions), on MySQL `edudev_testing`.

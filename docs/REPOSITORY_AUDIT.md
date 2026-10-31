# Phase 0 — Repository Audit and Baseline

**Date:** 2026-10-31  
**Location:** `C:\xampp\htdocs\edudev`

## 1. What was found

The directory was **empty**: no code, no database, no Git repository. There were no existing features to preserve, so this is a new build on the recommended stack.

## 2. Environment

| Item | Found | Notes |
|---|---|---|
| OS | Windows 10 Pro (XAMPP) | Local development only |
| PHP | 8.3.33 (ZTS, x64) | Required extensions present: pdo_mysql, mbstring, openssl, intl, gd, fileinfo, curl, zip, sodium, bcmath |
| Composer | Installed at `C:\ProgramData\ComposerSetup\bin` | Not on the bash `PATH`; call it as `php C:/ProgramData/ComposerSetup/bin/composer.phar` or use `composer` from PowerShell/CMD |
| Node / npm | 24.20 / 11.19 | |
| Database | MariaDB 10.4.32 (XAMPP), running | MySQL-compatible. Production should use MySQL 8 or MariaDB 10.6+ |
| Redis | Not installed | Database-backed cache, queue and sessions are used locally |
| Git | **Not installed** | See risk R1 |

## 3. Decisions taken

| Decision | Reason |
|---|---|
| **Laravel 12** (12.70) with the official **Livewire starter kit** (Livewire, Volt, Flux, Tailwind CSS 4, Vite) | Matches the recommended stack. It ships tested login, registration, password reset, email verification and profile settings, so that code doesn't have to be written from scratch. |
| **Pest 3** for tests | The kit's tests are written for Pest, but Pest wasn't installed. Pest 4 needs PHPUnit 12, which conflicts with the kit's PHPUnit 11, so Pest 3 was chosen. |
| **spatie/laravel-permission 8** for roles and permissions | Widely used and maintained, and integrates with Laravel's Gates and Policies. |
| **MySQL for development and tests** (`edudev`, `edudev_testing`) | The kit defaulted to SQLite. Testing on the production engine catches foreign-key and JSON differences early. |

## 4. Problems found in the starter kit, and fixes

1. **Livewire version mismatch.** Composer installed Livewire 4, but the kit's templates expect Livewire 3's default page layout. One test failed: "No hint path defined for [layouts]". **Fix:** published `config/livewire.php` and set `component_layout` to `components.layouts.app`.
2. **Pest missing.** Fixed as described above.
3. **Dark mode forced on.** Every layout had `class="dark"` hard-coded. This was removed so the brand's light theme is the default. Users can still choose dark in Settings → Appearance.

## 5. Baseline test results

| Run | Result |
|---|---|
| Fresh starter kit, before any EduDEV changes | 26 passed / 1 failed (the Livewire layout issue above) |
| After the layout fix | **27 passed / 0 failed** |
| End of Phase 1 | See `docs/CHANGELOG.md` |

## 6. Risk register (initial)

| ID | Risk | Impact | Mitigation |
|---|---|---|---|
| R1 | No Git, so there is no change history or rollback | High | Install Git for Windows, run `git init`, and commit before Phase 2. |
| R2 | Network to packagist.org dropped intermittently during setup | Low | Retry. `composer.lock` pins exact versions. |
| R3 | Shared hosting may lack queue workers, cron or Redis | Medium | See DEPLOYMENT_GUIDE; a VPS is recommended. |
| R4 | AI provider cost overruns | High | Usage reservation and limits are built before the AI Tutor launches (Phase 4). |
| R5 | Processing children's data (Nigeria Data Protection Act 2023) | High | Data minimisation, guardian linking, legal review before launch. |
| R6 | Brand name / trademark not yet cleared | Medium | All branding is configurable (Phase 1). |
| R7 | Copyrighted past-question content | High | Only original or licensed content; licence metadata on every question. |

# Implementation Roadmap

Follows section 23 of [PRODUCT_REQUIREMENTS.md](PRODUCT_REQUIREMENTS.md). Each phase ends with passing tests, updated docs and a phase summary before the next one starts.

| Phase | Scope (brief §23) | Status |
|---|---|---|
| 0 | Repository audit and project definition | ✅ Done |
| 1 | Application foundation and brand system | ✅ Done |
| 2 | Authentication, permissions and onboarding | ✅ Done (staff 2FA is a follow-up) |
| 3 | Curriculum, resources and question bank | ⏳ Next |
| 4 | Student dashboard and practice engine | Planned |
| 5 | AI provider foundation and EduDEV AI Tutor (text, then image) | Planned |
| 6 | Mastery engine and study plans | Planned |
| 7 | CBT examinations | Planned |
| 8 | Schools, teachers, parents and CBT centres (tenancy) | Planned |
| 9 | Subscriptions, billing and payments | Planned |
| 10 | Super Admin, reporting and support | Planned |
| 11 | Hardening, performance and release preparation | Planned |
| 12 | Mobile readiness and future expansion | Planned |

## MVP (brief §24)

The minimum sellable release is Phases 1–7, 9 and the core of 10:
- website and branding
- secure sign-in
- student dashboard
- curriculum and subject selection
- AI Tutor for text and image questions
- question bank
- timed CBT mock exams
- basic progress tracking and study plans
- subscriptions with one verified payment provider
- Super Admin
- AI usage and cost controls
- privacy and security essentials
- tests, deployment docs and the SQL export

School and guardian portals, advanced analytics, extra payment providers, mobile apps and white-labelling can follow. Their foundations (tenant columns, permissions, entitlements) are still laid early.

## Deviations from the brief (recorded as required)

| # | Deviation | Reason |
|---|---|---|
| D1 | **Separate staff sign-in page** (`/{ADMIN_PATH}/login`), added in Phase 1 | Requested by the product owner on 2026-10-31. |
| D2 | AI usage limits in Phase 5 are **configurable per role** until subscription plans exist in Phase 9; Phase 9 then moves them onto plan entitlements | Payments come after the AI Tutor in the brief's order. Without limits, AI cost would be uncontrolled in the meantime. |
| D3 | Pest 3 instead of the newest Pest | Pest 4 needs PHPUnit 12, which conflicts with the Laravel 12 starter kit. |

---

## Phase 1 — Application foundation and brand system ✅

**Delivered:**
- Laravel 12 with Livewire, Volt, Flux and Tailwind 4, on MySQL.
- Central branding: `config/edudev.php` defaults plus admin overrides.
- Design tokens and shared components.
- Original draft logo.
- Public layout, homepage and draft legal pages.
- Separate user and staff sign-in pages.
- 10 roles and 14 permissions.
- Admin overview, branding editor and audit log.
- Account suspension.
- Request IDs, security headers and `/health`.
- Branded error pages.
- The `edudev:create-admin` and `edudev:export-schema` commands.

**Acceptance criteria (brief):**

| Criterion | Result |
|---|---|
| Starts from documented instructions | ✅ DEPLOYMENT_GUIDE |
| Brand changed centrally | ✅ tested |
| Consistent design tokens | ✅ |
| Existing functionality intact | ✅ all 27 starter-kit tests pass |
| Tests pass | ✅ 65/65 |

## Phase 2 — Authentication, permissions and onboarding ✅

**Acceptance criteria (brief):**

| Criterion | Result |
|---|---|
| Register and authenticate | ✅ with recorded consent |
| Unauthorised access rejected | ✅ tested per screen and per service method |
| Role redirects work | ✅ `User::homeUrl()`, tested |
| Validation and recovery flows | ✅ starter-kit reset and verification tests still pass |
| Security tests on privileged operations | ✅ self-action, escalation, last super admin, staff-role self-selection |

**Change to existing behaviour:** account deletion went from instant to a reviewed request. The brief asks for deletion requests (Module B, §13), and the review step lets retained records be handled first. The two starter-kit deletion tests were updated to match.

Planned tasks:

| Task | Database | Permissions | Tests |
|---|---|---|---|
| Terms and privacy acceptance at registration, recorded with the policy version | `policy_acceptances` | — | registration needs consent; version recorded |
| Onboarding: choose account type (student, guardian, teacher, tutor, school, CBT centre), then a minimal profile | `user_profiles` | self only | each type completes onboarding and lands on its dashboard |
| Role-aware redirects after sign-in, registration and onboarding | — | — | redirect per role |
| Admin user management: search, filter, view, suspend/reactivate with a reason, change roles | — | `users.manage`, `roles.manage` | non-admins refused; no self-escalation; super admin role only grantable by a super admin; every change audited |
| Account deletion requests (user asks, admin reviews) | `account_deletion_requests` | `users.manage` | request created, then processed and audited |
| Security tests on privileged operations | — | — | ✓ |

Deferred within Phase 2: required 2FA for staff (the next increment, using Fortify's two-factor support); school and CBT-centre verification (needs the institutions tables from Phase 8, so onboarding here records the request only).

## Phases 3–12

The scope and acceptance criteria are exactly as in brief §23. A detailed task table like Phase 2's is written at the start of each phase.

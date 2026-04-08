<!--
  Sync Impact Report
  ==================
  Version change: 0.0.0 → 1.0.0
  Modified principles: N/A (initial ratification)
  Added sections:
    - Core Principles (I–XII)
    - Technology & Infrastructure Constraints
    - Development Workflow & Quality Gates
    - Governance
  Removed sections: N/A
  Templates requiring updates:
    - .specify/templates/plan-template.md        ✅ reviewed — Constitution Check
      section aligns with principles
    - .specify/templates/spec-template.md         ✅ reviewed — no conflicts
    - .specify/templates/tasks-template.md        ✅ reviewed — task phases
      compatible with principle-driven categories
  Follow-up TODOs: none
-->

# FadeBook Constitution

## Core Principles

### I. Tech Stack Lock

The following technology versions are the only permitted
dependencies for the platform. No alternative frameworks,
ORMs, or front-end libraries may be introduced without a
formal constitution amendment.

| Layer | Technology | Version Lock |
|---|---|---|
| Backend framework | Laravel | 13 |
| Admin panel | Filament | 5 |
| Reactive UI | Livewire | 4 |
| Frontend interactivity | Alpine.js | latest |
| Styling | Tailwind CSS | 4 |
| Database | MySQL | 8+ |
| Testing | Pest | 4 |
| Code formatting | Pint | 1 |

**Rationale**: A locked stack eliminates version-drift
surprises across phases and ensures every agent or developer
operates with identical assumptions.

### II. Database Conventions

- All table and column names MUST use `snake_case`.
- All models MUST enable soft deletes (`SoftDeletes` trait)
  unless explicitly justified and documented.
- Primary keys MUST use auto-incrementing `BIGINT` (`id`
  column) — UUIDs are NOT used in this project.
- All foreign keys MUST have explicit `constrained()`
  declarations in migrations.
- All date/time columns MUST use `timestamp` type.
- Indexes MUST be added for every foreign key and for
  columns used in `WHERE`, `ORDER BY`, or `GROUP BY`
  clauses in known queries.

**Rationale**: Consistent naming and defensive defaults
(soft deletes, indexes) prevent data loss and keep queries
performant at scale.

### III. Enum Convention (NON-NEGOTIABLE)

- All enum-like columns in migrations MUST use
  `tinyInteger` — MySQL `ENUM` type is strictly forbidden.
- Every `tinyInteger` enum column MUST have a corresponding
  backed PHP Enum class (`int`-backed) in `app/Enums/`.
- Enum keys MUST use `TitleCase`
  (e.g., `Pending`, `Confirmed`, `InProgress`).
- Models MUST cast enum columns to their Enum class via the
  `$casts` property.

**Rationale**: Backed int enums are portable across
databases, versionable without migrations, and type-safe at
the PHP level.

### IV. Code Style & PHP Standards

- All PHP files MUST follow PSR-12.
- `declare(strict_types=1);` is NOT required unless the
  file explicitly benefits from it — follow Laravel
  conventions.
- All methods MUST declare explicit return types and
  parameter type hints.
- PHP 8 constructor property promotion MUST be used where
  applicable.
- No raw SQL queries — all database access MUST go through
  Eloquent or the Query Builder.
- `vendor/bin/pint --dirty --format agent` MUST be run
  after every PHP file change.

**Rationale**: Consistent style reduces cognitive load and
eliminates formatting debates during reviews.

### V. Testing Discipline

- All tests MUST be written with Pest 4.
- Tests are created via `php artisan make:test --pest`.
- Feature tests are the default; unit tests require explicit
  justification.
- Factories MUST exist for every model and MUST be used in
  tests — no manual model instantiation.
- No end-to-end / browser tests in the MVP — feature and
  unit tests only.
- Tests MUST pass (`php artisan test --compact`) before any
  phase is considered complete.

**Rationale**: Pest + factories give fast, reliable coverage
without the brittleness of E2E tests in early phases.

### VI. RTL-First & Egyptian Arabic

- The entire UI MUST render in RTL layout by default.
- All user-facing copy MUST be written in Egyptian Arabic
  dialect (عامية مصرية) — not Modern Standard Arabic.
- Tailwind `rtl:` variants and logical properties
  (`ms-`, `me-`, `ps-`, `pe-`) MUST be used instead of
  `ml-`/`mr-`/`pl-`/`pr-`.
- All Blade templates MUST include `dir="rtl"` and
  `lang="ar"` on the root `<html>` element.
- Laravel's localization (`__()`, `trans()`) MUST be used
  for all UI strings with locale files under `lang/ar/`.

**Rationale**: RTL is not a retrofit — it must be the
default from day one to avoid costly layout rewrites.

### VII. PWA Requirements

- The app MUST include a valid `manifest.json` with: name,
  short_name, icons (192px + 512px), `theme_color`,
  `background_color`, `display: standalone`, `dir: rtl`,
  `lang: ar`, and `start_url`.
- A service worker MUST be registered for offline fallback
  (a single offline page is sufficient for MVP).
- The app MUST be installable via "Add to Home Screen" on
  both iOS Safari and Android Chrome.
- Safe area insets MUST be respected for iPhone notch and
  home indicator (`env(safe-area-inset-*)`) via
  `viewport-fit=cover`.

**Rationale**: PWA replaces native apps entirely — install
quality must match native expectations.

### VIII. Liquid Glass Design System

- All UI surfaces MUST use frosted glass cards:
  `backdrop-filter: blur(20px)` with semi-transparent
  backgrounds (`bg-white/60 dark:bg-gray-900/60` or
  equivalent Tailwind utilities).
- Navigation MUST use a fixed bottom tab bar that respects
  safe area insets.
- Transitions between screens MUST use iOS-style slide
  animations.
- Dark mode MUST be supported and follow system preference
  (`prefers-color-scheme`).
- All interactive elements MUST have hover/active micro-
  animations.
- Design tokens (blur radius, glass opacity, border radius,
  shadow) MUST be defined in a shared Tailwind config or
  CSS custom properties — no magic numbers in templates.

**Rationale**: Liquid Glass is the brand identity — every
screen must feel like a native iOS 26 app.

### IX. WhatsApp-Only Notifications

- WhatsApp custom API is the ONLY permitted notification
  channel.
- No SMS, no email, no push notifications — these channels
  are explicitly forbidden.
- All notification messages MUST be written in Egyptian
  Arabic dialect.
- WhatsApp integration MUST be abstracted behind a
  `NotificationChannel` interface so the underlying API
  provider can be swapped without touching business logic.
- Every notification trigger defined in Phase 6 of PLAN.md
  MUST be implemented — no silent failures.

**Rationale**: Egyptian users overwhelmingly prefer
WhatsApp; a single channel simplifies ops and reduces cost.

### X. Payment Gateway Interface

- Payment integration MUST be built behind an adapter /
  interface pattern (`PaymentGateway` contract).
- The gateway MUST support split payments (marketplace /
  sub-merchant model).
- The concrete gateway (Fawaterk, Kashier, or other) MUST
  be swappable without modifying business logic, service
  classes, or controllers.
- All payment and refund events MUST be logged in
  dedicated database tables (`payments`, `refunds`).

**Rationale**: The gateway is TBD — the architecture must
not couple to any single provider.

### XI. Multi-Tenancy & Commission

- Each barbershop operates as a logical tenant with fully
  isolated data (bookings, barbers, services, financials).
- Commission percentage is set **per shop individually** by
  the super admin — there is no global default rate.
- Commission is deducted automatically at the gateway split
  level — no manual transfers.
- Both the platform admin and the shop owner MUST see a
  transparent commission breakdown in their dashboards.

**Rationale**: Per-shop commission gives pricing flexibility;
gateway-level splits eliminate manual accounting.

### XII. Booking Code Generation

- Every confirmed booking MUST be assigned a short, unique,
  human-readable booking code (4–6 alphanumeric uppercase
  characters, e.g., `#A4K9`).
- Codes MUST be unique across the system (not just per
  shop).
- Codes are the primary method of identity verification at
  the shop — no QR codes, no phone display required.
- Generation MUST use a collision-resistant algorithm with
  retry logic.

**Rationale**: Verbal relay of short codes is the fastest,
most inclusive check-in method across literacy levels.

## Technology & Infrastructure Constraints

- **Folder structure**: Follow standard Laravel directory
  layout. No new top-level folders without a constitution
  amendment.
- **Artisan generators**: All new files (controllers,
  models, migrations, requests, policies, jobs, tests) MUST
  be scaffolded via `php artisan make:*` commands with
  `--no-interaction`.
- **No raw queries**: All database access through Eloquent
  or Query Builder. `DB::raw()` is forbidden unless
  explicitly justified per-case.
- **No email**: The application has no email configuration
  or email-based features.
- **No push notifications**: Web Push, Firebase, or any
  push channel is forbidden.
- **Named routes**: All internal links MUST use named routes
  via `route()` helper.
- **API resources**: API responses MUST use Eloquent API
  Resources.
- **Filament admin**: The super admin panel lives at
  `/admin` and is powered exclusively by Filament 5. No
  public registration — admin accounts are seeded via
  Artisan.

## Development Workflow & Quality Gates

1. **Spec-Driven Development**: Every feature follows the
   Spec Kit workflow:
   `/constitution → /specify → /plan → /tasks → /implement`
2. **No code before spec**: Implementation MUST NOT begin
   until the spec and plan are approved.
3. **Pint on save**: `vendor/bin/pint --dirty --format agent`
   MUST be run after any PHP file modification.
4. **Tests before merge**: All tests MUST pass before a
   phase is considered complete.
5. **Factory coverage**: Every model MUST have a factory.
   Every factory MUST have meaningful states matching the
   model's enum columns.
6. **Migration safety**: Migrations MUST be reversible
   (`down()` method) unless dropping a column with data
   loss — in that case, document the irreversibility.
7. **Commit discipline**: One commit per logical task or
   task group. Commit messages follow Conventional Commits
   (`feat:`, `fix:`, `docs:`, `chore:`, `test:`).

## Governance

- This constitution supersedes all other development
  practices, agent rules, or ad-hoc decisions.
- Any amendment MUST be documented with: the change
  description, version bump rationale, and a migration plan
  for affected code.
- Version increments follow Semantic Versioning:
  - **MAJOR**: Principle removed or fundamentally redefined.
  - **MINOR**: New principle added or materially expanded.
  - **PATCH**: Wording clarifications or typo fixes.
- Compliance with this constitution MUST be verified at each
  phase checkpoint before proceeding to the next phase.
- The `PLAN.md` document serves as the authoritative
  product blueprint; this constitution governs *how* it is
  built.

**Version**: 1.0.0 | **Ratified**: 2026-04-08 | **Last Amended**: 2026-04-08

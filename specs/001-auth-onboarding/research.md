# Research & Technical Decisions: Authentication & Onboarding

**Feature**: 001-auth-onboarding
**Status**: Complete

## 1. Tech Stack (Mandated by Constitution)
- **Decision**: Laravel 13, Livewire 4, Alpine.js, Tailwind CSS v4, MySQL 8+, Pest 4.
- **Rationale**: Strict mandate from `constitution.md`. Ensures identical assumptions across all agents and team members.

## 2. Phone Number Normalisation
- **Decision**: Normalize all Egyptian phone numbers by stripping `+20` or `0020` prefixes, storing exclusively as 11-digit strings starting with `01` (e.g., `01012345678`).
- **Rationale**: Prevents duplicate accounts where a user might register as `+2010...` and log in as `010...`. Simplifies the unique constraint on the database.

## 3. WhatsApp OTP Generation & Verification
- **Decision**: Use an `OtpCode` Eloquent model to store 6-digit OTPs. Use random_int(100000, 999999) for generation. Include an expiry timestamp (created_at + 10 mins) and tracking for attempts.
- **Rationale**: Simplest approach without needing external caching dependencies (like Redis) while providing full tracking of rate-limiting and expiry.

## 4. Role-Based Access Control (RBAC)
- **Decision**: Define roles using a backed `int` PHP Enum (`UserRole::cases()`). Use Laravel Middleware to intercept requests and check the authenticated user's role against the required role. Redirect to their home route with a flash session toast if unauthorized.
- **Rationale**: Avoids overly complex ACL packages (e.g., Spatie Permissions) for a rigid, 4-role system. Fits perfectly within Laravel's default middleware framework.

## 5. Shop Onboarding & Wizard State
- **Decision**: Build the shop onboarding flow as a single Livewire component with step management, storing intermediate state in component properties.
- **Rationale**: Avoids partial database records or complex draft states. Only persists to the database upon final submission, moving the shop straight to `pending`.

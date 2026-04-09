---
description: "Task list for Authentication & Onboarding implementation"
---

# Tasks: Authentication & Onboarding

**Input**: Design documents from `/specs/001-auth-onboarding/`
**Prerequisites**: plan.md (required), spec.md (required for user stories), research.md, data-model.md, contracts/

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Project initialization and basic structure

- [x] T001 Initialize basic UI layout with RTL and Arabic boilerplate in `resources/views/components/layouts/app.blade.php`
- [x] T002 [P] Create UserRole, ShopStatus, and OtpPurpose PHP backed enums in `app/Enums/`
- [x] T003 Implement WhatsAppNotificationChannel interface in `app/Contracts/WhatsAppNotificationChannel.php`
- [x] T004 Implement LogWhatsAppNotifier in `app/Services/LogWhatsAppNotifier.php` and bind it in `AppServiceProvider`

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Core infrastructure that MUST be complete before ANY user story can be implemented

**⚠️ CRITICAL**: No user story work can begin until this phase is complete

- [x] T005 [P] Setup base User migration with phone, role, status, and no_show_strike_count in `database/migrations/`
- [x] T006 [P] Update User model in `app/Models/User.php` with casts, fillable array, and soft deletes
- [x] T007 [P] Update UserFactory in `database/factories/UserFactory.php` to match the new schema

**Checkpoint**: Foundation ready - user story implementation can now begin in parallel

## Phase 3: User Story 1 - Client Registration (Priority: P1) 🎯 MVP

**Goal**: Allow clients to create accounts via Egyptian phone number and password.

**Independent Test**: User registers and gets redirected to the marketplace.

- [x] T008 [P] [US1] Create Auth/RegisterTest in `tests/Feature/Auth/RegisterTest.php` covering registration acceptance criteria
- [x] T009 [US1] Create Livewire component Auth\Register in `app/Livewire/Auth/Register.php` and corresponding view
- [x] T010 [US1] Implement phone canonicalization and validation logic in the Register component
- [x] T011 [US1] Register route linking the component to `/register`

## Phase 4: User Story 2 - Client Login (Priority: P1)

**Goal**: Allow returning clients to sign in using phone number and password.

**Independent Test**: Registered client enters valid credentials and is authenticated.

- [x] T012 [P] [US2] Create Auth/LoginTest in `tests/Feature/Auth/LoginTest.php` covering generic error and rate-limiting scenarios
- [x] T013 [US2] Create Livewire component Auth\Login in `app/Livewire/Auth/Login.php` and corresponding view
- [x] T014 [US2] Implement login logic with `RateLimiter` (max 5 attempts per minute per number)
- [x] T015 [US2] Register route linking the component to `/login`

## Phase 5: User Story 7 - Role-Based Access Control (Priority: P1)

**Goal**: Restrict users from accessing features outside their role.

**Independent Test**: Unauthorized access attempts redirect to the appropriate home route with an Arabic toast.

- [x] T016 [P] [US7] Create Middleware/RoleTest in `tests/Feature/Middleware/RoleTest.php`
- [x] T017 [US7] Create RoleMiddleware in `app/Http/Middleware/RoleMiddleware.php`
- [x] T018 [US7] Implement redirect logic and session flash toast with "مش مسموحلك تدخل هنا" in the middleware

## Phase 6: User Story 3 - Barbershop Owner Registration & Onboarding (Priority: P1)

**Goal**: Shop owners register and complete a multi-step form to list their shop (kept pending).

**Independent Test**: Owner completes multi-step form and lands on pending status screen.

- [x] T019 [P] [US3] Create Area and Shop migrations in `database/migrations/`
- [x] T020 [P] [US3] Create ShopOpeningHour migration in `database/migrations/`
- [x] T021 [P] [US3] Create Area, Shop, and ShopOpeningHour models in `app/Models/` with Eloquent relations
- [x] T022 [P] [US3] Create Area, Shop, ShopOpeningHour factories in `database/factories/`
- [x] T023 [P] [US3] Create Shop/OnboardingTest in `tests/Feature/Shop/OnboardingTest.php`
- [x] T024 [US3] Create Livewire component Shop\OnboardingWizard in `app/Livewire/Shop/OnboardingWizard.php` and view
- [x] T025 [US3] Implement image upload logic (max 2MB, JPEG/PNG) and onboarding steps state management
- [x] T026 [US3] Register route linking the wizard to `/onboarding` inside owner auth middleware
- [x] T027 [US3] Create PendingShop dashboard component and redirect logic for completed onboarding

## Phase 7: User Story 4 - Shop Approval / Rejection by Super Admin (Priority: P2)

**Goal**: Allow super admin to review shops, triggering notifications to owners.

**Independent Test**: Administrator action changes shop status and sends WhatsApp log message.

- [x] T028 [P] [US4] Create Admin/ShopReviewTest in `tests/Feature/Admin/ShopReviewTest.php`
- [x] T029 [US4] Create logic in `app/Services/ShopReviewService.php` to handle approval/rejection and WhatsApp notifications
- [x] T030 [US4] Add re-submission capabilities for owners whose shops are rejected (update logic in Shop controller or Livewire component)

## Phase 8: User Story 5 - Password Reset via WhatsApp OTP (Priority: P2)

**Goal**: OTP-based reset flow for forgotten passwords.

**Independent Test**: Full OTP generation, sending, and verification flow works.

- [x] T031 [P] [US5] Create OtpCode migration in `database/migrations/`
- [x] T032 [P] [US5] Create OtpCode model in `app/Models/OtpCode.php`
- [x] T033 [P] [US5] Create OtpCodeFactory in `database/factories/OtpCodeFactory.php`
- [x] T034 [P] [US5] Create Auth/PasswordResetTest in `tests/Feature/Auth/PasswordResetTest.php`
- [x] T035 [US5] Create Livewire components Auth\ForgotPassword and Auth\ResetPassword in `app/Livewire/Auth/`
- [x] T036 [US5] Implement 6-digit OTP generation, expiry logic (10 mins), attempts tracking (max 5), rate limiting, and WhatsApp hook
- [x] T037 [US5] Register routes linking components to `/forgot-password` and `/reset-password`

## Phase 9: User Story 6 - Super Admin Seeding (Priority: P2)

**Goal**: Automatically provision a super admin.

**Independent Test**: Running seeders generates an admin without errors.

- [x] T038 [P] [US6] Create SuperAdminSeeder in `database/seeders/SuperAdminSeeder.php`
- [x] T039 [US6] Implement idempotent seeding logic to create the primary SuperAdmin user

## Final Phase: Polish & Cross-Cutting Concerns

**Purpose**: Improvements that affect multiple user stories

- [x] T040 Review validation messages across all auth/onboarding views to ensure natural Egyptian Arabic phrasing in `lang/ar/validation.php`
- [x] T041 Verify safe-area insets configuration in `app.blade.php` for liquid glass PWA presentation
- [x] T042 Ensure all `artisan test --compact` passes with 100% test suite completion

---

## Target Workflow Strategy

### MVP Delivery (US1)
- Phase 1 & 2 -> Phase 3 -> STOP to test registration MVP.

### Full Delivery
- Execute Phase 4 (Login) and Phase 5 (RBAC).
- Execute Phase 6 (Onboarding) and Phase 7 (Approval).
- Non-critical flows (Phase 8 OTP reset and Phase 9 admin seeding) can be implemented sequentially at the end.

### Parallel Execution Examples

For US3 (Barbershop Owner Registration):
- Developer A could work on T019-T022 (Migrations/Models/Factories)
- Developer B could stub out the UI components concurrently (T024-T026)

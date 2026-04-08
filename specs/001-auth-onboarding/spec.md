# Feature Specification: Authentication & Onboarding

**Feature Branch**: `001-auth-onboarding`
**Created**: 2026-04-08
**Status**: Draft
**Input**: User description: "Phase 1 from PLAN.md — client registration & login, barbershop owner registration & onboarding, password reset, super admin seeding, and role system"

## Clarifications

### Session 2026-04-08

- Q: After a shop registration is rejected, can the owner edit and re-submit? → A: Yes — the owner can edit their shop details and re-submit for review. The shop status returns to `pending`.
- Q: What format should OTP codes use? → A: 6-digit numeric (e.g., `482391`).
- Q: Should the shop area be free text or a predefined list? → A: Predefined list of areas managed by the super admin via Filament.
- Q: When an authenticated user accesses a route outside their role, what happens? → A: Redirect to their own home route with an Arabic toast message ("مش مسموحلك تدخل هنا").
- Q: How should opening hours be structured? → A: Per-day open/close time pair with a “closed today” toggle for each day of the week.

## User Scenarios & Testing *(mandatory)*

### User Story 1 — Client Registration (Priority: P1)

A new user opens the app for the first time and wants to
create an account so they can browse barbershops and book
appointments. They tap "سجّل حساب جديد" (Register), choose
the "عميل" (Client) role, enter their name, Egyptian phone
number, and a password. Upon successful registration they
land on the marketplace homepage, fully authenticated.

**Why this priority**: Without client accounts, no bookings
can happen — this is the foundational user identity flow.

**Independent Test**: A user can open the registration
screen, fill in valid data, submit, and be redirected to
the marketplace as a logged-in client.

**Acceptance Scenarios**:

1. **Given** an unauthenticated user, **When** they fill in
   a valid name, phone number (Egyptian format), and
   password and submit, **Then** a client account is
   created, they are logged in, and redirected to the
   marketplace.
2. **Given** an unauthenticated user, **When** they submit
   a phone number that is already registered, **Then** a
   clear error in Egyptian Arabic is shown and no duplicate
   account is created.
3. **Given** an unauthenticated user, **When** they submit
   a password shorter than 8 characters, **Then** a
   validation error is shown.
4. **Given** an unauthenticated user, **When** they submit
   an invalid phone format (not Egyptian), **Then** a
   validation error is shown.

---

### User Story 2 — Client Login (Priority: P1)

A returning client opens the app and logs in with their
phone number and password to access their account and
bookings.

**Why this priority**: Login is paired with registration as
the core authentication gate — both are required for any
authenticated feature.

**Independent Test**: A registered client can enter valid
credentials and be redirected to the marketplace.

**Acceptance Scenarios**:

1. **Given** a registered client, **When** they enter
   correct phone number and password, **Then** they are
   authenticated and redirected to the marketplace.
2. **Given** a registered client, **When** they enter an
   incorrect password, **Then** a generic error "بيانات
   الدخول غلط" is shown (no hint about which field is
   wrong).
3. **Given** a registered client, **When** they enter
   a phone number that does not exist, **Then** the same
   generic error is shown.
4. **Given** an authenticated user, **When** they visit the
   login page, **Then** they are redirected to the
   marketplace (no double login).

---

### User Story 3 — Barbershop Owner Registration & Onboarding (Priority: P1)

A barbershop owner wants to list their shop on FadeBook.
They tap "سجّل حساب جديد", choose the "صاحب صالون" (Shop
Owner) role, and enter their name, phone number, and
password. After basic account creation, they are guided
through a multi-step onboarding form to provide full shop
details: shop name, address, area/neighbourhood, shop phone
number, logo upload, opening hours for each day, a basic
list of services, and the number of barbers. Upon
submitting the onboarding form, the shop status is set to
"pending" and the owner sees a confirmation screen telling
them their application is under review.

**Why this priority**: Shop owners are the supply side — the
platform has no value without shops. Registration +
onboarding is the critical funnel.

**Independent Test**: A user can register as an owner, fill
in all onboarding steps, submit, and see the pending
confirmation screen with their shop status as "pending".

**Acceptance Scenarios**:

1. **Given** an unauthenticated user on the registration
   page, **When** they choose the shop owner role and
   submit valid credentials, **Then** an account with the
   `barber_owner` role is created and they are redirected
   to the onboarding form.
2. **Given** a newly registered shop owner, **When** they
   fill in all required shop details (name, address, area,
   phone, opening hours) and submit, **Then** a shop record
   is created with status `pending` and the owner sees a
   "طلبك تحت المراجعة" confirmation.
3. **Given** a shop owner in the onboarding flow, **When**
   they upload a logo image larger than 2 MB, **Then** a
   validation error is shown.
4. **Given** a shop owner in the onboarding flow, **When**
   they skip a required field (e.g., shop name), **Then**
   a validation error is shown and they cannot proceed.
5. **Given** a shop owner who has already completed
   onboarding, **When** they log in again, **Then** they
   see the pending status screen (not the onboarding form
   again).

---

### User Story 4 — Shop Approval / Rejection by Super Admin (Priority: P2)

The super admin sees a new barbershop registration in the
admin panel. They review the submitted details and either
approve or reject the shop. The owner is notified of the
decision via WhatsApp.

**Why this priority**: Without approval, shops never go
live. This completes the owner registration funnel but
depends on the admin panel (Filament) which is a separate
phase — this spec covers the backend logic and notification
triggers, not the full Filament UI.

**Independent Test**: An admin can approve a pending shop
via the admin panel, and the shop status changes to
"active". A WhatsApp notification is sent to the owner.

**Acceptance Scenarios**:

1. **Given** a shop with status `pending`, **When** the
   super admin approves it, **Then** the shop status changes
   to `active` and a WhatsApp approval message is sent to
   the owner.
2. **Given** a shop with status `pending`, **When** the
   super admin rejects it with a reason, **Then** the shop
   status changes to `rejected`, the reason is stored, and
   a WhatsApp rejection message with the reason is sent.
3. **Given** a shop with status `active`, **When** the
   owner logs in, **Then** they see the shop dashboard (not
   the pending screen).
4. **Given** a shop with status `rejected`, **When** the
   owner logs in, **Then** they see a rejection notice with
   the reason provided by the admin and an option to edit
   and re-submit.
5. **Given** a shop with status `rejected`, **When** the
   owner edits their shop details and re-submits, **Then**
   the shop status changes back to `pending` and the admin
   is notified of the re-submission.

---

### User Story 5 — Password Reset via WhatsApp OTP (Priority: P2)

A user has forgotten their password. They tap "نسيت كلمة
السر", enter their phone number, receive a one-time
password (OTP) via WhatsApp, enter the code, and set a new
password.

**Why this priority**: Password reset is a standard
authentication safety net. It is important but not blocking
for initial development.

**Independent Test**: A user can request a password reset,
receive an OTP, enter it correctly, set a new password, and
log in with the new password.

**Acceptance Scenarios**:

1. **Given** a registered user, **When** they request a
   password reset and enter their phone number, **Then** an
   OTP is sent via WhatsApp.
2. **Given** a user who received an OTP, **When** they enter
   the correct code, **Then** they are allowed to set a new
   password.
3. **Given** a user who received an OTP, **When** they enter
   an incorrect code 5 times, **Then** the OTP is
   invalidated and they must request a new one.
4. **Given** a user who received an OTP, **When** 10 minutes
   pass without entry, **Then** the OTP expires and they
   must request a new one.
5. **Given** a user who just reset their password, **When**
   they log in with the new password, **Then** they are
   authenticated successfully.
6. **Given** a non-registered phone number, **When** someone
   requests a password reset, **Then** a generic success
   message is shown (to prevent phone enumeration), but no
   OTP is sent.

---

### User Story 6 — Super Admin Seeding (Priority: P2)

The platform operator creates the super admin account via a
command-line seeder. There is no public registration path
for admin accounts.

**Why this priority**: The super admin is needed for shop
approvals, but the account is created once — it's a simple
setup step, not a user-facing flow.

**Independent Test**: Running the seeder command creates an
admin account that can log in to the admin panel.

**Acceptance Scenarios**:

1. **Given** a fresh database, **When** the seeder is run,
   **Then** a super admin account is created with the
   `super_admin` role.
2. **Given** an existing super admin, **When** the seeder
   is run again, **Then** no duplicate account is created.
3. **Given** a super admin account, **When** the admin logs
   in at the admin panel URL, **Then** they see the Filament
   dashboard.
4. **Given** a client or barber_owner account, **When** they
   attempt to access the admin panel URL, **Then** they are
   denied access.

---

### User Story 7 — Role-Based Access Control (Priority: P1)

The system enforces four user roles: `client`,
`barber_owner`, `barber_staff`, and `super_admin`. Each role
determines which parts of the platform the user can access.

**Why this priority**: Role enforcement is foundational —
without it, any user could access any dashboard, making the
platform insecure.

**Independent Test**: A client cannot access shop management
routes; a shop owner cannot access the admin panel; only
super admin can access `/admin`.

**Acceptance Scenarios**:

1. **Given** a user with the `client` role, **When** they
   attempt to access shop management routes, **Then** they
   are redirected to the marketplace with an Arabic toast
   message.
2. **Given** a user with the `barber_owner` role, **When**
   they attempt to access the admin panel, **Then** they
   are redirected to their shop dashboard with an Arabic
   toast message.
3. **Given** a user with the `super_admin` role, **When**
   they access the admin panel, **Then** they are granted
   access.
4. **Given** an unauthenticated user, **When** they attempt
   to access any protected route, **Then** they are
   redirected to the login page.

---

### Edge Cases

- What happens when a user tries to register with a phone
  number that has leading zeros or includes the country
  code `+20`? The system must normalise phone numbers to a
  canonical format before storing.
- What happens when the WhatsApp API is temporarily
  unavailable during password reset? The system must show a
  user-friendly error and allow retry.
- What happens when a shop owner abandons the onboarding
  form halfway? Their account is created but the shop
  record is incomplete — they must be returned to the
  onboarding flow on next login.
- What happens when two users on different devices try to
  register the same phone number simultaneously? Only one
  account should be created; the second attempt must fail
  with a duplicate error.
- What happens when a rejected shop owner re-submits? The
  shop status returns to `pending`, previous rejection
  reason is cleared, and the admin sees it as a new review
  request.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: System MUST allow users to register with a
  name, Egyptian phone number, and password.
- **FR-002**: System MUST allow users to choose their role
  (client or shop owner) during registration.
- **FR-003**: System MUST validate phone numbers against
  Egyptian format (11 digits starting with `01`).
- **FR-004**: System MUST normalise phone numbers to a
  canonical format (strip leading `+20` or `0020`, store
  as `01XXXXXXXXX`).
- **FR-005**: System MUST enforce password minimum length of
  8 characters.
- **FR-006**: System MUST authenticate users via phone
  number and password.
- **FR-007**: System MUST redirect authenticated users away
  from login/register pages.
- **FR-008**: System MUST support a multi-step onboarding
  form for shop owners collecting: shop name, address,
  area (selected from predefined list), shop phone, logo,
  opening hours (per-day open/close time pair with closed
  toggle for each day of the week), basic services list,
  and number of barbers.
- **FR-009**: System MUST set newly onboarded shop status to
  `pending`.
- **FR-010**: System MUST allow the super admin to approve
  or reject pending shops.
- **FR-011**: System MUST send a WhatsApp notification to
  the shop owner upon approval or rejection.
- **FR-012**: System MUST support password reset via
  WhatsApp OTP.
- **FR-013**: OTP codes MUST expire after 10 minutes.
- **FR-014**: OTP codes MUST be invalidated after 5 failed
  attempts.
- **FR-015**: System MUST create the super admin account
  exclusively via a command-line seeder.
- **FR-016**: System MUST enforce role-based access control
  for all protected routes.
- **FR-017**: System MUST store user roles using a
  `tinyInteger` column with a backed PHP Enum class (per
  constitution).
- **FR-018**: System MUST display all UI text, validation
  messages, and error messages in Egyptian Arabic dialect.
- **FR-019**: Login error messages MUST be generic (not
  revealing whether the phone or password was wrong) to
  prevent user enumeration.
- **FR-020**: System MUST throttle login attempts (max 5
  attempts per minute per phone number) to prevent brute
  force attacks.
- **FR-021**: System MUST throttle password reset requests
  (max 3 requests per hour per phone number).
- **FR-022**: Logo uploads MUST accept JPEG and PNG formats
  only, with a maximum file size of 2 MB.
- **FR-023**: System MUST allow rejected shop owners to edit
  their shop details and re-submit for review, resetting
  the shop status to `pending`.
- **FR-024**: OTP codes MUST be 6-digit numeric strings
  generated with cryptographically secure randomness.
- **FR-025**: System MUST maintain a predefined list of
  areas/neighbourhoods managed by the super admin via
  Filament. Shop owners MUST select from this list during
  onboarding.
- **FR-026**: When an authenticated user accesses a route
  outside their role, the system MUST redirect them to
  their role-appropriate home route and display an Arabic
  toast notification ("مش مسموحلك تدخل هنا").
- **FR-027**: Opening hours MUST be stored as one record per
  day of the week (Saturday–Friday), each with an open time,
  close time, and a boolean closed flag. A closed day has
  no bookable time slots.

### Key Entities

- **User**: Represents any person on the platform. Key
  attributes: name, phone number (unique), password (hashed),
  role (client / barber_owner / barber_staff / super_admin),
  no-show strike count, status (active / blocked).
- **Shop**: Represents a barbershop on the platform. Key
  attributes: owner (belongs to a User), name, address,
  area (belongs to an Area), phone, logo, opening hours
  (per-day open/close time + closed flag, Saturday–Friday),
  status (pending / active / rejected / suspended),
  rejection reason.
- **OtpCode**: Represents a one-time password for
  verification. Key attributes: phone number, code (6-digit
  numeric string), purpose (password_reset /
  booking_confirm), expiry timestamp, attempt count, used
  flag.
- **Area**: Represents a geographic area/neighbourhood for
  shop location tagging. Key attributes: name (Arabic),
  slug, active flag. Managed exclusively by the super admin.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: A new client can complete registration in
  under 60 seconds.
- **SC-002**: A new shop owner can complete registration
  and the full onboarding form in under 5 minutes.
- **SC-003**: A returning user can log in within 15 seconds.
- **SC-004**: Password reset flow (request → OTP → new
  password) can be completed in under 2 minutes.
- **SC-005**: 100% of protected routes are inaccessible to
  unauthorised roles.
- **SC-006**: Zero user enumeration vectors exist in login
  and password reset flows.
- **SC-007**: All user-facing text is displayed in Egyptian
  Arabic dialect with correct RTL layout.
- **SC-008**: The super admin seeder is idempotent — running
  it multiple times produces exactly one admin account.

## Assumptions

- The WhatsApp notification integration (API setup, message
  templates) will be fully implemented in Phase 6. This
  phase defines the triggers and expected messages but may
  use a placeholder/logging adapter until Phase 6 is
  complete.
- Phone number validation assumes the Egyptian format only
  (`01XXXXXXXXX`, 11 digits). International formats are
  out of scope.
- The multi-step onboarding form is a single-page wizard
  (not separate URLs per step) — the owner can navigate
  back and forth between steps before final submission.
- Logo images will be stored locally on disk (using
  Laravel's `storage` disk). Cloud storage (S3) is
  deferred.
- The `barber_staff` role exists in the enum but is not
  actively used in this phase — staff onboarding is part of
  Phase 3 (Shop Dashboard).
- Rate limiting uses the application's built-in rate
  limiter — no external service required.
- Session-based authentication is used (Laravel's default)
  — no token-based API auth in this phase.

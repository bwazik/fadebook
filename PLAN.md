# FadeBook — Implementation Plan
> For AI-assisted development. Each phase is a fully working vertical slice.

---

## Ground Rules for the LLM

Before touching any phase, internalize these rules. They are non-negotiable and apply to every single line of code in every phase:

1. **Stack is locked:** Laravel 13, Livewire 4, Alpine.js 3, Tailwind CSS v4, MySQL 8, Filament 5. No substitutions.
2. **Every PHP file starts with:** `<?php` then `declare(strict_types=1);`
3. **PSR-12** coding standard everywhere.
4. **No raw SQL.** Eloquent only. Complex queries go in Repository classes.
5. **No hardcoded config values.** Everything dynamic lives in the `settings` table, fetched via a `SettingsService`.
6. **All enum columns are `tinyInteger`** in migrations. Every enum column has a corresponding backed PHP Enum class in `app/Enums/`.
7. **All models** (except pivots) have: `id` (auto-increment), `uuid` (unique), `timestamps()`, `softDeletes()`.
8. **UUIDs are used in all public-facing URLs** and route model binding. Never expose integer IDs to the frontend.
9. **All routes use Livewire `wire:navigate`** for page transitions — no full page reloads between internal pages.
10. **RTL first.** Every blade view uses `dir="rtl"` and Arabic Egyptian dialect for all UI copy.
11. **All UI must follow the Liquid Glass design system:** glass cards, iOS buttons, iOS inputs, bottom nav, safe area insets, dark mode. Use the components defined in the constitution.
12. **Primary font is Tajawal.** Loaded via Google Fonts in the main layout.
13. **WhatsApp is the only notification channel.** No email notifications, no SMS, no push.
14. **All WhatsApp messages are queued**, never sent synchronously. Use the `whatsapp_messages` table.
15. **Factories and seeders** must be created for every model in every phase.
16. **Pest tests** must be written within each phase — unit tests for services, feature tests for user flows.
17. **Images** use Laravel's `public` disk via the polymorphic `images` table.
18. **Booking state** during the multi-step flow is persisted in the session.
19. **Page navigation** always uses `wire:navigate` (Livewire SPA mode).
20. **When you reach a STOP point**, do not continue. Output exactly what is asked and wait for the developer to provide the required code.

---

## Project Starting State

The following are already installed and configured. Do NOT re-install or re-scaffold:
- Laravel 13
- Laravel Breeze (with existing controller-based auth files)
- Livewire 4
- Filament 5
- Tailwind CSS v4
- Alpine.js 3
- Pest PHP

---

## Phase 0 — Foundation & Database

**Goal:** Set up the entire database, all models, all enums, all factories, seeders, and the global layout shell. No UI features yet — just the complete foundation every future phase builds on.

---

### 0.1 — Tailwind v4 & Design System Setup

**File:** `resources/css/app.css`

Replace the default Tailwind config with the CSS-first v4 configuration:

```css
@import "tailwindcss";

@theme {
  --font-sans: "Tajawal", ui-sans-serif, system-ui, sans-serif;
  --color-fadebook-dark: #0f172a;
  --color-fadebook-accent: #ff2d55;
  --radius-card: 2rem;
  --radius-button: 1rem;
  --safe-area-top: env(safe-area-inset-top);
  --safe-area-bottom: env(safe-area-inset-bottom);
  --safe-area-left: env(safe-area-inset-left);
  --safe-area-right: env(safe-area-inset-right);
}

* { -webkit-tap-highlight-color: transparent; }
body { @apply font-sans antialiased; }
```

Add to `resources/views/layouts/app.blade.php`:
- `<html lang="ar" dir="rtl">`
- Tajawal font from Google Fonts
- `<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">`
- Dark/light meta theme-color tags
- `@livewireScripts` with `persist` plugin enabled for SPA navigation
- Theme switcher JS (reads from localStorage, applies CSS var)
- Dark mode JS (reads from localStorage or system preference)

---

### 0.2 — Blade Components

Create all the following reusable Blade components in `resources/views/components/`. Each component must be fully implemented exactly as defined in the constitution:

| Component file | Description |
|---|---|
| `glass-card.blade.php` | Frosted glass card with backdrop blur |
| `ios-button.blade.php` | Full-width accent button with Livewire loading state |
| `ios-input.blade.php` | Borderless input with label, inside input group |
| `ios-input-group.blade.php` | Container for ios-input rows |
| `ios-textarea.blade.php` | Rounded textarea with focus ring |
| `ios-toggle.blade.php` | iOS-style toggle switch |
| `ios-select.blade.php` | Custom styled select |
| `ios-alert.blade.php` | Inline alert component (success, error, warning) |
| `toast.blade.php` | Toast notification (dispatched via Livewire events) |
| `bottom-sheet.blade.php` | Bottom sheet modal with drag handle and safe area |
| `bottom-nav.blade.php` | Fixed bottom navigation bar with safe area padding |

**Bottom nav items (client):**
- الرئيسية → `home`
- حجوزاتي → `bookings.index`
- البحث → `search`
- حسابي → `profile.index`

**Bottom nav items (shop owner):** different routes, same component — conditional rendering based on user role.

---

### 0.3 — Main Layout

**File:** `resources/views/layouts/app.blade.php`

This is the single layout for all PWA pages. It must:
- Include the bottom nav component (hidden on auth pages)
- Include the toast component
- Add safe area padding to the main content area
- Support `$slot` for page content
- Use `wire:navigate` compatible `<livewire:...>` structure

---

### 0.4 — Enums

Create all backed PHP Enum classes in `app/Enums/`. Each enum must have a `getLabel(): string` method returning Egyptian Arabic labels, and where applicable a `getColor(): string` method for Filament badge colors.

| File | Cases |
|---|---|
| `UserRole.php` | `Client = 1`, `BarberOwner = 2`, `SuperAdmin = 3` |
| `ShopStatus.php` | `Pending = 0`, `Active = 1`, `Suspended = 2`, `Rejected = 3` |
| `BookingStatus.php` | `Pending = 0`, `Confirmed = 1`, `InProgress = 2`, `Completed = 3`, `Cancelled = 4`, `NoShow = 5` |
| `CancelledBy.php` | `Client = 1`, `Shop = 2` |
| `OtpType.php` | `Registration = 1`, `PhoneVerification = 2`, `PasswordReset = 3`, `BookingConfirmation = 4` |
| `PaymentMode.php` | `NoPayment = 0`, `PartialDeposit = 1`, `FullPayment = 2` |
| `BarberSelectionMode.php` | `AnyAvailable = 1`, `ClientPicks = 2` |
| `RefundReason.php` | `ClientCancelEarly = 1`, `ShopCancel = 2`, `Other = 3` |
| `RefundStatus.php` | `Pending = 0`, `Processed = 1`, `Failed = 2` |
| `DiscountType.php` | `Percentage = 1`, `Fixed = 2` |
| `WhatsAppQueueType.php` | `Instant = 1`, `Urgent = 2`, `Default = 3` |
| `WhatsAppStatus.php` | `Queued = 1`, `Sent = 2`, `Failed = 3` |

---

### 0.5 — HasPublicUuid Trait

**File:** `app/Models/Concerns/HasPublicUuid.php`

This trait must:
- Auto-generate a UUID v4 on the `creating` model event
- Override `getRouteKeyName()` to return `'uuid'`
- Prevent duplicate UUID generation

---

### 0.6 — Migrations (Full Database)

Run all migrations in this exact order. Use the exact schema defined in the constitution for every table. Do not invent columns or change types.

Order:
1. `users` — modify the existing Breeze migration to match the constitution schema exactly (add `uuid`, `phone`, `role`, `no_show_count`, `is_blocked`, `otp_request_count`, `last_otp_sent_at`, remove `email` as required, make it nullable)
2. `password_reset_tokens` — modify to use `phone` as primary instead of `email`
3. `phone_verifications`
4. `phone_change_history`
5. `areas`
6. `shops`
7. `barbers`
8. `barber_unavailability`
9. `services`
10. `coupons`
11. `coupon_usages`
12. `bookings`
13. `refunds`
14. `images`
15. `reviews`
16. `views`
17. `whatsapp_messages`
18. `settings`

**Important for `bookings`:** The foreign key to `coupons` must use `->onDelete('set null')` since coupon is nullable.

---

### 0.7 — Models

Create all Eloquent models. Each model must have:
- `use HasPublicUuid;` trait (except pivot models)
- `$fillable` array (all columns except `id`, `uuid`, `created_at`, `updated_at`, `deleted_at`)
- `$casts` array (all enum columns cast to their Enum class, all datetime columns cast to `datetime`, booleans cast to `boolean`, JSON columns cast to `array`)
- `$hidden = ['id']`
- All relationships defined with proper return type hints
- Required model scopes

**Models and their key scopes:**

`User`:
- `scopeClients()`, `scopeOwners()`, `scopeBlocked()`
- Relationships: `bookings()`, `shop()`, `reviews()`

`Shop`:
- `scopeActive()`, `scopePending()`, `scopeOnline()`
- Relationships: `owner()`, `barbers()`, `services()`, `bookings()`, `reviews()`, `images()`

`Barber`:
- `scopeActive()`
- Relationships: `shop()`, `user()`, `bookings()`, `unavailabilities()`, `images()`

`Service`:
- `scopeActive()`
- Relationships: `shop()`, `bookings()`, `images()`

`Booking`:
- `scopePending()`, `scopeConfirmed()`, `scopeInProgress()`, `scopeCompleted()`, `scopeCancelled()`, `scopeNoShow()`
- Relationships: `shop()`, `client()`, `barber()`, `service()`, `coupon()`, `refund()`

`Coupon`:
- `scopeActive()`, `scopeValid()` (checks date range and usage limit)
- Relationships: `shop()`, `usages()`

`Refund`: Relationships: `booking()`

`Review` (polymorphic): `reviewable()`, `user()`, `booking()`

`Image` (polymorphic): `imageable()`

`Area`: `scopeActive()`

`WhatsAppMessage`: `scopeQueued()`, `scopeFailed()`

`Setting`: static `get(string $key)` and `set(string $key, mixed $value)` helper methods

---

### 0.8 — SettingsService

**File:** `app/Services/SettingsService.php`

A service class that wraps the `Setting` model with caching. Must provide:
- `get(string $key, mixed $default = null): mixed`
- `set(string $key, mixed $value): void`
- Cache key prefix: `setting_`
- Cache TTL: 60 minutes
- Cache is invalidated on `set()`

Bind in `AppServiceProvider`:
```php
$this->app->singleton(SettingsService::class);
```

---

### 0.9 — Seeders

**File:** `database/seeders/DatabaseSeeder.php`

Run in this order:

1. **SettingsSeeder** — inserts into `settings` table:
   - `terms_content` → placeholder HTML
   - `privacy_content` → placeholder HTML
   - `default_commission_rate` → `10.00`
   - `platform_whatsapp_number` → placeholder
   - `otp_expiry_minutes` → `5`
   - `max_otp_attempts` → `3`
   - `max_otp_requests_per_hour` → `5`
   - `max_pending_bookings_per_client` → `3`
   - `no_show_grace_period_minutes` → `15`
   - `cancellation_window_hours` → `2`

2. **AreaSeeder** — seed all major Egyptian governorates and cities as areas. Minimum 20 areas including: القاهرة، الجيزة، الإسكندرية، المنصورة، طنطا، أسيوط، الأقصر، أسوان، الزقازيق، دمياط، بورسعيد، السويس، الإسماعيلية، المنيا، سوهاج، قنا، شرم الشيخ، الغردقة، العريش، الفيوم. Each with a slug.

3. **SuperAdminSeeder** — creates the super admin user:
   - `name`: `مدير النظام`
   - `phone`: read from `.env` key `SUPER_ADMIN_PHONE`
   - `password`: read from `.env` key `SUPER_ADMIN_PASSWORD`
   - `role`: `UserRole::SuperAdmin`

4. **ShopSeeder** (development only, wrapped in `if (app()->isLocal())`) — creates 5 fake shops with owners, barbers, and services using factories.

---

### 0.10 — Factories

Create factories for every model using `fake()` with Arabic-friendly data where applicable:

- `UserFactory` — generates Egyptian phone numbers (`010`, `011`, `012`, `015` prefixes)
- `ShopFactory` — random area, realistic shop names in Arabic
- `BarberFactory`
- `ServiceFactory` — realistic barbershop service names in Arabic (قص شعر، حلاقة ذقن، etc.)
- `BookingFactory`
- `CouponFactory`
- `AreaFactory`

---

### 0.11 — Booking Code Generator

**File:** `app/Services/BookingCodeGenerator.php`

Implement exactly as defined in the constitution:
- Charset: `ABCDEFGHJKLMNPQRSTUVWXYZ23456789` (excludes ambiguous characters)
- Length: 6
- Loop until unique against `bookings.booking_code`

---

### 0.12 — Booking Status Scheduler

**File:** `app/Console/Commands/UpdateBookingStatuses.php`

Command signature: `app:update-booking-statuses`

Logic:
1. Find all `confirmed` bookings where `scheduled_at <= now()` → set to `in_progress`
2. Find all `in_progress` bookings where `scheduled_at <= now()->subHour()` → set to `completed`, dispatch WhatsApp review request event
3. Find all `confirmed` bookings where `scheduled_at <= now()->subMinutes(grace_period)` and still not arrived → set to `no_show`, increment `users.no_show_count`, trigger strike logic

Register in `routes/console.php`:
```php
Schedule::command('app:update-booking-statuses')->everyFiveMinutes();
```

---

### 0.13 — PWA Files

**File:** `public/manifest.json` — implement exactly as defined in the constitution.

**File:** `public/sw.js` — implement service worker with cache-first strategy and offline fallback.

**Route:** Add `GET /offline` route returning `view('offline')`.

**View:** `resources/views/offline.blade.php` — Liquid Glass card with Arabic offline message.

Register service worker in the main layout's `<head>`.

---

### 0.14 — Phase 0 Tests

Write Pest tests in `tests/Unit/`:

- `BookingCodeGeneratorTest` — generates 6-char code, only valid charset, generates unique codes
- `SettingsServiceTest` — get/set works, cache is used, cache is invalidated on set
- `EnumsTest` — every enum `getLabel()` returns a non-empty string

---

## Phase 1 — Authentication

**Goal:** Full working auth flow — login, register, forgot password — all in Livewire with Liquid Glass design. The developer will provide the OTP service and WhatsApp service. The LLM converts Breeze controllers to Livewire and integrates with the provided services.

---

### STOP — Before starting Phase 1:

The LLM must output this message and wait:

> "Phase 1 requires the following service classes before I can continue. Please provide the complete implementation for:
> 1. `app/Services/WhatsAppService.php` — with a `send(string $phone, string $template, array $data, WhatsAppQueueType $queueType): void` method
> 2. `app/Services/OtpService.php` — with `send(string $phone, OtpType $type, ?int $userId = null): void` and `verify(string $phone, string $otp, OtpType $type): bool` methods
> 3. `config/whatsapp.php` — with all message templates
>
> Once you provide these, I will continue with Phase 1."

---

### 1.1 — Convert Breeze Auth to Livewire

**Delete** the following Breeze controller files if they exist:
- `app/Http/Controllers/Auth/RegisteredUserController.php`
- `app/Http/Controllers/Auth/AuthenticatedSessionController.php`
- `app/Http/Controllers/Auth/PasswordResetLinkController.php`
- `app/Http/Controllers/Auth/NewPasswordController.php`

**Delete** the corresponding Breeze blade views in `resources/views/auth/`.

**Do NOT delete** any routes file or middleware — only the controller classes and their views.

---

### 1.2 — Auth Routes

**File:** `routes/web.php`

Replace Breeze auth routes with Livewire full-page component routes:

```php
// Guest only
Route::middleware('guest')->group(function () {
    Route::get('/login', \App\Livewire\Auth\Login::class)->name('login');
    Route::get('/register', \App\Livewire\Auth\Register::class)->name('register');
    Route::get('/forgot-password', \App\Livewire\Auth\ForgotPassword::class)->name('password.request');
    Route::get('/reset-password/{token}', \App\Livewire\Auth\ResetPassword::class)->name('password.reset');
});

// Authenticated
Route::middleware('auth')->group(function () {
    Route::get('/', \App\Livewire\Home::class)->name('home');
    Route::get('/profile', \App\Livewire\Profile\Index::class)->name('profile.index');
    Route::post('/logout', \App\Http\Controllers\Auth\AuthenticatedSessionController::class . '@destroy')->name('logout');
});
```

---

### 1.3 — Livewire Auth Components

Create the following full-page Livewire components. Each must use the Liquid Glass design system. All labels, placeholders, and messages in Egyptian Arabic.

#### `app/Livewire/Auth/Login.php`

Properties: `$phone`, `$password`, `$errorMessage`

Methods:
- `authenticate()` — validates input, calls Laravel's `Auth::attempt()` using phone + password, redirects to `home` on success, sets `$errorMessage` on failure
- Uses the developer-provided backend — do not rewrite auth logic, only wire the form to existing `Auth::attempt()`

View: `resources/views/livewire/auth/login.blade.php`
- Glass card centered on screen
- FadeBook logo at top
- iOS input group with phone (dir="ltr") and password fields
- iOS button "دخول"
- Links to register and forgot password using `wire:navigate`
- No bottom nav on this page

#### `app/Livewire/Auth/Register.php`

**This is a 2-step form within one Livewire component.**

Step 1 — Account info:
- Properties: `$name`, `$phone`, `$password`, `$password_confirmation`
- `nextStep()` — validates step 1 fields, advances to step 2

Step 2 — Role selection:
- Properties: `$role` (client or barber_owner)
- If `client` → calls registration logic and redirects to `home`
- If `barber_owner` → redirects to shop registration flow (Phase 3)

`register()` method:
- Creates `User` with validated data
- Role set from selection
- Calls `Auth::login()` after creation
- **Does NOT call OtpService** — OTP is only for booking confirmation and password reset

View: `resources/views/livewire/auth/register.blade.php`
- Step indicator at top
- Animated transition between steps using Alpine.js
- No bottom nav

#### `app/Livewire/Auth/ForgotPassword.php`

**3-step flow:**

Step 1 — Enter phone:
- `$phone`
- `sendOtp()` → calls `OtpService::send($phone, OtpType::PasswordReset)`, advances to step 2

Step 2 — Enter OTP:
- `$otp`
- `verifyOtp()` → calls `OtpService::verify($phone, $otp, OtpType::PasswordReset)`, advances to step 3 on success

Step 3 — New password:
- `$password`, `$password_confirmation`
- `resetPassword()` → updates user password, redirects to login

**STOP:** Before implementing `sendOtp()` and `verifyOtp()`, confirm that `OtpService` has been provided by the developer.

View: `resources/views/livewire/auth/forgot-password.blade.php`
- Same Liquid Glass style
- Show/hide steps using Alpine.js `x-show`

---

### 1.4 — Guest & Auth Middleware

Ensure the existing Laravel middleware `auth` and `guest` are applied correctly to the routes defined in 1.2. Do not create new middleware — use the existing ones from Breeze.

---

### 1.5 — Phase 1 Tests

**Feature tests in** `tests/Feature/Auth/`:

- `LoginTest`:
  - Can login with valid phone + password
  - Cannot login with wrong password
  - Cannot login if account is blocked (`is_blocked = true`)
  - Redirected to home after successful login

- `RegisterTest`:
  - Can register as client with valid data
  - Cannot register with duplicate phone
  - Password confirmation must match
  - Registered user is logged in automatically

- `ForgotPasswordTest`:
  - OTP is sent to valid phone
  - Invalid OTP is rejected
  - Password is updated after valid OTP

---

## Phase 2 — Marketplace & Shop Pages

**Goal:** The public-facing marketplace homepage and individual shop pages. Fully browsable without login. Booking button visible but requires auth.

---

### 2.1 — Routes

```php
Route::get('/', \App\Livewire\Home::class)->name('home');
Route::get('/search', \App\Livewire\Search::class)->name('search');
Route::get('/{areaSlug}/{shopSlug}', \App\Livewire\Shop\ShopPage::class)->name('shop.show');
```

Note: `/{areaSlug}/{shopSlug}` uses UUID-based route model binding via the `HasPublicUuid` trait's slug approach. Resolve `Shop` by matching both `area.slug` and `shop.slug`.

---

### 2.2 — Livewire Components

#### `app/Livewire/Home.php`

Properties: `$shops` (paginated, 12 per page), `$selectedArea`, `$sortBy` (default: `rating`)

On mount: load active + online shops, eager-load `area`, `images` (logo collection), aggregate `average_rating`.

Methods:
- `filterByArea(int $areaId)` — filters shops by area
- `sortShops(string $sortBy)` — sorts by `rating` or `newest`
- `loadMore()` — pagination

View: `resources/views/livewire/home.blade.php`
- Sticky header with FadeBook logo and search icon (navigates to `/search`)
- Horizontal scrollable area filter chips
- Grid of shop cards (2 columns on mobile)
- Each shop card (glass card style) shows:
  - Logo (from `images` polymorphic, `logo` collection)
  - Shop name
  - Area name
  - Star rating + review count
  - "متاح النهارده" green badge or "مش متاح دلوقتي" gray badge
  - Tapping navigates to `/{areaSlug}/{shopSlug}` via `wire:navigate`
- Bottom nav visible

#### `app/Livewire/Search.php`

Properties: `$query`, `$results`, `$selectedArea`

Methods:
- `search()` — searches `shops.name` and `areas.name`, live search with 300ms debounce
- `updatedQuery()` — triggers search automatically

View: `resources/views/livewire/search.blade.php`
- Search input at top (auto-focused on mount)
- Results list with same shop card style
- Empty state with Arabic message

#### `app/Livewire/Shop/ShopPage.php`

Properties: `$shop` (with all eager-loaded relationships)

On mount: resolve shop from route parameters (area slug + shop slug), increment view count (async, queued job), load barbers, services, reviews.

View: `resources/views/livewire/shop/shop-page.blade.php`
- Full-width banner image (from `images`, `banner` collection)
- Overlaid shop logo + name + area
- Star rating + review count
- If `is_online = false`: show "مش متاح دلوقتي" banner, disable book button
- Services section: list of active services with name, duration, price. Inactive services show "مش متاح" badge and are not tappable.
- Barbers section: horizontal scroll of barber cards with photo, name, specialty, rating
- Reviews section: list of reviews with star rating, comment, first name, date. Sortable by newest/highest/lowest.
- Sticky bottom "احجز دلوقتي" button — if not authenticated, redirects to login. If authenticated, navigates to booking flow.
- Bottom nav visible

---

### 2.3 — View Count Job

**File:** `app/Jobs/IncrementShopView.php`

Dispatched on `ShopPage` mount. Inserts a record into the `views` table (polymorphic, `Shop` model). Checks for duplicate view from same IP within 24 hours before inserting. After insert, updates `shops.total_views`.

---

### 2.4 — Phase 2 Tests

- `HomePageTest`:
  - Homepage loads and shows active shops
  - Offline shops show badge but appear in list
  - Area filter works correctly
  - Unauthenticated users can browse

- `ShopPageTest`:
  - Shop page loads with correct data
  - Inactive services show badge
  - Offline shop shows unavailable banner
  - View count increments on visit
  - 404 if shop slug or area slug is invalid

- `SearchTest`:
  - Search returns shops matching query
  - Search by area name works
  - Empty query returns no results

---

## Phase 3 — Shop Registration & Owner Onboarding

**Goal:** Shop owner can complete their shop profile after registering. Super admin is notified. Owner receives WhatsApp on approval/rejection.

---

### 3.1 — Routes

```php
Route::middleware('auth')->group(function () {
    Route::get('/onboarding/shop', \App\Livewire\Onboarding\ShopSetup::class)->name('onboarding.shop');
});
```

After registering as `barber_owner`, the user is redirected here automatically.

---

### 3.2 — Shop Setup Livewire Component

**File:** `app/Livewire/Onboarding/ShopSetup.php`

**Multi-step form — 3 steps:**

Step 1 — Basic Info:
- `$shopName`, `$phone`, `$address`, `$areaId`, `$description`
- Validation: all required

Step 2 — Hours & Settings:
- `$openingHours` (array keyed by day, each with `open` and `close` or `null` for closed)
- Days: Saturday through Friday
- Toggle per day: open/closed
- Time pickers for open/close

Step 3 — Initial Services:
- Repeater: add at least 1 service (name, price, duration)
- Uses Alpine.js for dynamic add/remove rows

`submit()` method:
- Creates `Shop` with `status = ShopStatus::Pending`, `owner_id = auth()->id()`
- Creates initial services
- Sends WhatsApp to super admin (template: `shop_registration_pending`)
- Redirects to a "pending approval" holding page

**View:** `resources/views/livewire/onboarding/shop-setup.blade.php`
- Step indicator
- Liquid Glass design
- No bottom nav

---

### 3.3 — Pending Approval Page

**File:** `app/Livewire/Onboarding/PendingApproval.php`

Simple full-page Livewire component showing a waiting message. Polls every 30 seconds (using Livewire polling) to check if shop status changed. If approved → redirect to shop dashboard. If rejected → show rejection reason.

---

### 3.4 — Phase 3 Tests

- `ShopSetupTest`:
  - Owner can complete shop setup with valid data
  - Shop is created with `pending` status
  - At least 1 service is required
  - WhatsApp notification is queued for super admin

---

## Phase 4 — Booking Flow (Client Side)

**Goal:** The complete multi-step booking flow. Session-persisted state. OTP confirmation before finalizing.

---

### 4.1 — Routes

```php
Route::middleware('auth')->group(function () {
    Route::get('/book/{shopUuid}', \App\Livewire\Booking\CreateBooking::class)->name('booking.create');
    Route::get('/bookings', \App\Livewire\Booking\BookingList::class)->name('bookings.index');
    Route::get('/bookings/{bookingUuid}', \App\Livewire\Booking\BookingDetails::class)->name('booking.show');
});
```

---

### 4.2 — CreateBooking Livewire Component

**File:** `app/Livewire/Booking/CreateBooking.php`

**Single full-page Livewire component with internal step management.** State is persisted in the session under `booking_draft_{shopUuid}`.

Steps:
1. **Pick service** — list of active services
2. **Pick barber** *(conditional)* — only shown if `shop.barber_selection_mode = ClientPicks`. If `AnyAvailable`, skip this step automatically.
3. **Pick date & time** — calendar limited to `now()` to `now()->addDays($shop->advance_booking_days)`. Available slots are calculated server-side based on barber schedule and existing bookings.
4. **Confirm & pay** — summary of booking, policy acceptance checkbox, coupon code input (scaffold only — no validation logic, await developer), payment or direct confirm based on `shop.payment_mode`.
5. **OTP verification** — OTP sent to client phone via `OtpService`. Client enters 6-digit code.

Properties: `$step`, `$shopUuid`, `$shop`, `$selectedServiceId`, `$selectedBarberId`, `$selectedDate`, `$selectedSlot`, `$couponCode`, `$policyAccepted`, `$otp`

Methods:
- `mount(string $shopUuid)` — load shop, restore draft from session
- `selectService(int $serviceId)` — set service, advance step
- `selectBarber(int $barberId)` — set barber, advance step
- `selectDate(string $date)` — load available slots for date
- `selectSlot(string $slot)` — set slot, advance step
- `confirmBooking()` — validates policy accepted, calls `BookingService::initiate()`, sends OTP
- `verifyOtp()` — calls `OtpService::verify()`, on success calls `BookingService::confirm()`, clears session draft, redirects to booking details

**STOP — Before implementing `confirmBooking()` and `verifyOtp()`:**

> "Phase 4 requires `OtpService` to be available. If you have already provided it in Phase 1, I will proceed. If not, please provide `app/Services/OtpService.php` before continuing."

---

### 4.3 — BookingService

**File:** `app/Services/BookingService.php`

Methods:

`initiate(User $client, Shop $shop, array $data): Booking`
- Validates: client not blocked, client has < `max_pending_bookings` pending bookings, slot is still available (re-check), policy accepted
- Creates booking with `status = Pending`
- Generates booking code via `BookingCodeGenerator::generate()`
- Saves draft to session
- Returns the `Booking`

`confirm(Booking $booking): void`
- Sets `status = Confirmed`, `confirmed_at = now()`
- Sends WhatsApp confirmation to client (includes booking code)
- Sends WhatsApp notification to shop owner
- Clears session draft
- Schedules smart reminder (see below)

`cancel(Booking $booking, CancelledBy $by): void`
- Determines refund eligibility:
  - If `$by = Client` and `scheduled_at > now()->addHours(cancellation_window_hours)` → full refund
  - If `$by = Client` and within window → no refund
  - If `$by = Shop` → full refund always
- Creates `Refund` record if applicable
- Sets `status = Cancelled`, `cancelled_at`, `cancelled_by`
- Sends WhatsApp notification to the other party

`markArrived(Booking $booking): void`
- Sets `status = InProgress`, `arrived_at = now()`
- Sends WhatsApp to client: "وصلنا عندك"

`markCompleted(Booking $booking): void`
- Sets `status = Completed`, `completed_at = now()`
- Sends WhatsApp review request to client

`markNoShow(Booking $booking): void`
- Sets `status = NoShow`
- Increments `client.no_show_count`
- If `no_show_count == 1` → sends WhatsApp warning
- If `no_show_count >= 2` → sets `client.is_blocked = true`, sends WhatsApp block notice
- No refund if paid

---

### 4.4 — Smart Reminder Scheduler

**File:** `app/Console/Commands/SendBookingReminders.php`

Command: `app:send-booking-reminders`

Logic:
- Find confirmed bookings where `scheduled_at` is between `now()->addHours(1)` and `now()->addHours(1)->addMinutes(5)` → send 1-hour reminder
- Find confirmed bookings where `scheduled_at` is between `now()->addHours(24)` and `now()->addHours(24)->addMinutes(5)` → send 24-hour reminder
- Do not send if booking was made less than 1 hour ago

Register in `routes/console.php`:
```php
Schedule::command('app:send-booking-reminders')->everyFiveMinutes();
```

---

### 4.5 — Available Slots Calculator

**File:** `app/Services/SlotCalculatorService.php`

`getAvailableSlots(Shop $shop, ?Barber $barber, Service $service, string $date): array`

Logic:
1. Get shop opening hours for the day of week of `$date`. If closed → return `[]`
2. Generate all possible slots from open to close time, with `$service->duration_minutes` intervals
3. Remove slots that overlap with existing confirmed/in_progress bookings for the barber (or any barber if `AnyAvailable` mode)
4. Remove slots in the past
5. Return array of time strings: `['09:00', '09:30', '10:00', ...]`

---

### 4.6 — BookingList Component

**File:** `app/Livewire/Booking/BookingList.php`

Shows the authenticated client's bookings. Tabs: upcoming (pending + confirmed), completed, cancelled.

Each booking card shows: shop logo, shop name, service, barber name, date + time, booking code, status badge. Tapping navigates to booking details.

---

### 4.7 — BookingDetails Component

**File:** `app/Livewire/Booking/BookingDetails.php`

Shows full booking details. If status is `confirmed` or `pending` and cancellation is still possible → show "إلغاء الحجز" button which calls `BookingService::cancel()`.

Shows booking code prominently in a glass card.

---

### 4.8 — Phase 4 Tests

- `BookingFlowTest`:
  - Client can complete full booking flow (no payment mode)
  - Client cannot book if blocked
  - Client cannot book more than max pending bookings
  - Slot is locked after booking
  - OTP must be verified to confirm booking
  - Booking code is generated and stored

- `BookingServiceTest`:
  - Cancel > 2 hours before → refund created
  - Cancel < 2 hours before → no refund
  - Shop cancel → refund created
  - No-show increments strike count
  - 2nd no-show blocks user

- `SlotCalculatorTest`:
  - Returns correct slots for open day
  - Returns empty for closed day
  - Excludes already booked slots
  - Excludes past slots

---

## Phase 5 — Shop Owner Dashboard

**Goal:** The shop owner's management dashboard. Same UI shell as client. Manages shop settings, barbers, services, and reservations including check-in flow.

---

### 5.1 — Routes

```php
Route::middleware(['auth', 'role:barber_owner'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', \App\Livewire\Dashboard\Home::class)->name('home');
    Route::get('/settings', \App\Livewire\Dashboard\ShopSettings::class)->name('settings');
    Route::get('/barbers', \App\Livewire\Dashboard\ManageBarbers::class)->name('barbers');
    Route::get('/services', \App\Livewire\Dashboard\ManageServices::class)->name('services');
    Route::get('/reservations', \App\Livewire\Dashboard\Reservations::class)->name('reservations');
    Route::get('/clients', \App\Livewire\Dashboard\ClientList::class)->name('clients');
    Route::get('/financials', \App\Livewire\Dashboard\Financials::class)->name('financials');
});
```

Create a `role` middleware: `app/Http/Middleware/EnsureUserRole.php`. Checks `auth()->user()->role === UserRole::from($role)`. Register in `bootstrap/app.php`.

---

### 5.2 — Dashboard Layout

The shop owner dashboard uses the **exact same** `layouts/app.blade.php` but with a different bottom nav (owner tabs):
- الرئيسية → `dashboard.home`
- الحجوزات → `dashboard.reservations`
- المحل → `dashboard.settings`
- حسابي → `profile.index`

Conditionally render the correct bottom nav based on user role inside the `bottom-nav` component.

---

### 5.3 — Dashboard Home Component

Properties: `$shop`, `$todayBookings`, `$weekBookings`, `$stats`

Stats: total bookings this month, gross earnings, commission deducted, net payout.

View: Glass cards for stats, list of today's upcoming bookings.

---

### 5.4 — ShopSettings Component

Allows editing all shop settings in one page with sections:
- Basic info (name, phone, description, address, area)
- Opening hours (per day toggle + time pickers)
- Advance booking window (number input, 1-90 days)
- Barber selection mode (segmented control)
- Payment mode (segmented control)
- Deposit percentage (shown only if `PartialDeposit` mode)
- Go offline toggle

`save()` method updates the shop. Dispatches toast on success.

---

### 5.5 — ManageBarbers Component

List of barbers with: photo, name, specialty, rating, active toggle.

Actions:
- Add barber (bottom sheet form): name, phone, specialties, photo upload
- Edit barber (bottom sheet form)
- Toggle active/inactive
- Mark barber as unavailable for a date (date picker in bottom sheet):
  - Inserts into `barber_unavailability`
  - Queries all confirmed bookings for that barber on that date
  - Calls `BookingService::cancel()` with `CancelledBy::Shop` for each
  - WhatsApp sent to each affected client

---

### 5.6 — ManageServices Component

List of services with: name, price, duration, active badge.

Actions:
- Add service (bottom sheet)
- Edit service (bottom sheet)
- Toggle active/inactive (badge changes, service not deleted)

---

### 5.7 — Reservations Component

Calendar view (week view by default, switchable to list view).

Each booking card shows: client name, booking code, service, barber, time, status badge.

Action buttons per booking (based on current status):
- `confirmed` → "وصل؟" (Mark Arrived) + "إلغاء"
- `in_progress` → "خلص؟" (Mark Completed) + "ما جاش" (Mark No-Show)
- `completed`, `cancelled`, `no_show` → read-only

All actions call the corresponding `BookingService` method.

Search by booking code: input at top, searches live.

---

### 5.8 — Financials Component

Date range filter (this month / last month / custom range).

Shows:
- Total gross revenue
- Commission deducted (with rate shown)
- Net payout
- Breakdown table: per service and per barber
- Transaction list
- Refund history

---

### 5.9 — Phase 5 Tests

- `ShopSettingsTest` — owner can update shop settings
- `ManageBarbersTest` — can add, edit, deactivate barber; barber unavailability cancels bookings
- `ManageServicesTest` — can add, edit, deactivate service
- `ReservationsTest` — mark arrived, completed, no-show all work correctly; cancel triggers refund logic

---

## Phase 6 — Ratings & Reviews

**Goal:** Post-visit review flow. Triggered by booking completion. Client rates shop and/or barber.

---

### 6.1 — Routes

```php
Route::middleware('auth')->group(function () {
    Route::get('/review/{bookingUuid}', \App\Livewire\Review\SubmitReview::class)->name('review.create');
});
```

---

### 6.2 — SubmitReview Component

**File:** `app/Livewire/Review\SubmitReview.php`

On mount: load booking by UUID, verify it belongs to the authenticated user, verify status is `completed`, verify no review exists yet.

Properties: `$shopRating`, `$barberRating`, `$comment`, `$booking`

`submit()`:
- Creates `Review` for the shop (polymorphic: `reviewable_type = Shop`)
- If `$barberRating > 0` → creates `Review` for the barber (polymorphic: `reviewable_type = Barber`)
- Recalculates and updates `shops.average_rating` and `barbers.average_rating`
- Redirects to booking details with success toast

View: Star rating component (Alpine.js, 1-5 stars, tappable), optional comment textarea, submit button.

---

### 6.3 — Rating Recalculation

**File:** `app/Services/RatingService.php`

> ⚠️ **STOP:** This service involves aggregation logic. Scaffold the class with method signatures only:
> ```php
> public function recalculateShopRating(Shop $shop): void {}
> public function recalculateBarberRating(Barber $barber): void {}
> ```
> Then output:
> "Please provide the implementation for `app/Services/RatingService.php`. The methods should recalculate `average_rating` and `total_reviews` on the `Shop` and `Barber` models from the `reviews` table."
> Wait for developer input before calling these methods.

---

### 6.4 — Phase 6 Tests

- `ReviewTest`:
  - Client can submit review after completed booking
  - Cannot review twice for same booking
  - Cannot review if booking is not completed
  - Shop and barber ratings update after review

---

## Phase 7 — Profile & Settings

**Goal:** Client profile page with booking history, personal info editing, and app settings (theme, dark mode).

---

### 7.1 — Routes

```php
Route::middleware('auth')->group(function () {
    Route::get('/profile', \App\Livewire\Profile\Index::class)->name('profile.index');
    Route::get('/profile/edit', \App\Livewire\Profile\EditProfile::class)->name('profile.edit');
    Route::get('/settings', \App\Livewire\Profile\AppSettings::class)->name('app.settings');
});
```

---

### 7.2 — Profile Index Component

Shows:
- User name, phone, avatar (initials-based placeholder)
- Quick stats: total bookings, completed bookings
- Recent bookings (last 3, links to full list)
- Links to edit profile, app settings, terms, privacy
- Logout button

---

### 7.3 — EditProfile Component

Editable fields: name, email (optional), birthday.
Phone number is displayed but **not editable** in this phase (phone change is deferred).
Password change section: current password, new password, confirm new password.

---

### 7.4 — AppSettings Component

- Theme switcher: 5 colored circles (classic, ocean, mint, sunset, lavender). Tapping one calls JS `changeTheme()` and saves to localStorage.
- Dark mode toggle: calls JS `toggleDarkMode()`.
- Language: display-only (Arabic only for now).

---

### 7.5 — Static Pages

**File:** `app/Livewire/StaticPage.php`

Single Livewire component used for both `/terms` and `/privacy`. Reads content from `SettingsService::get('terms_content')` or `SettingsService::get('privacy_content')` based on route parameter.

Routes:
```php
Route::get('/terms', \App\Livewire\StaticPage::class)->name('terms')->defaults('page', 'terms');
Route::get('/privacy', \App\Livewire\StaticPage::class)->name('privacy')->defaults('page', 'privacy');
```

View renders the HTML content safely using `{!! $content !!}`.

---

### 7.6 — Phase 7 Tests

- `ProfileTest` — profile page loads, edit saves correctly
- `AppSettingsTest` — settings page loads without error

---

## Phase 8 — Shop Analytics Dashboard

**Goal:** Analytics screens inside the shop owner dashboard.

---

### 8.1 — Routes

```php
Route::middleware(['auth', 'role:barber_owner'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/analytics', \App\Livewire\Dashboard\Analytics::class)->name('analytics');
});
```

---

### 8.2 — Analytics Component

**File:** `app/Livewire/Dashboard/Analytics.php`

Date range filter: this week / this month / last month / custom.

Sections:

**Bookings Overview:**
- Total, completed, cancelled, no-show counts for the period
- Rendered as a bar chart using Chart.js (loaded via CDN in this view only)

**Revenue Overview:**
- Gross revenue, commission deducted, net payout for the period
- Line chart

**Top Services:** table sorted by booking count

**Top Barbers:** table sorted by booking count and average rating

**Repeat Client Rate:** count of clients with more than 1 booking / total unique clients — shown as percentage

**Busiest Days:** bar chart of bookings by day of week

All chart labels in Egyptian Arabic. All currency in EGP.

---

### 8.3 — Phase 8 Tests

- `AnalyticsTest` — analytics page loads, date filter changes data, no SQL errors

---

## Phase 9 — Filament Super Admin Panel

**Goal:** Full Filament 5 admin panel. Built after all client-facing phases are complete.

---

### 9.1 — Filament Resources

Create the following Filament resources. Each resource must have full list, view, create, and edit pages unless noted.

#### `ShopResource`

List columns: name, owner name, area, status badge (color from `ShopStatus::getColor()`), commission rate, total bookings, created at.

Filters: status, area.

Actions:
- Approve (sets `status = Active`, sends WhatsApp to owner) — only visible on `pending` records
- Reject (modal with reason input, sets `status = Rejected`, sends WhatsApp) — only visible on `pending` records
- Suspend / Reactivate

Edit page: can change commission rate, status.

#### `UserResource`

List columns: name, phone, role badge, no-show count, is_blocked badge, created at.

Filters: role, is_blocked.

Actions:
- Ban / Unban
- Unblock (resets `is_blocked = false` and `no_show_count = 0`)
- Send WhatsApp (modal with free-text message input)

#### `BookingResource`

List columns: booking code, client name, shop name, service, scheduled at, status badge, paid amount.

Filters: status, date range, shop.

Read-only view only — no create/edit.

#### `TransactionResource` (read-only)

Lists all bookings with payment. Columns: booking code, shop, client, service price, commission amount, shop earnings, date.

Export to CSV action.

#### `RefundResource` (read-only)

Lists all refunds. Columns: booking code, amount, reason, status badge, processed at.

Filter by status.

#### `ReviewResource`

List columns: shop name, client name, rating, comment (truncated), is_flagged badge, created at.

Filters: is_flagged.

Actions:
- Delete review
- Mark as reviewed (sets `is_flagged = false`)

#### `SettingsPage` (custom Filament page, not a resource)

A single custom page at `/admin/settings`. Form with:
- Terms of Use content (rich text editor — use Filament's built-in `RichEditor`)
- Privacy Policy content (rich text editor)
- Default commission rate (number input)
- Platform WhatsApp number (text input)
- OTP expiry minutes (number input)
- Max OTP attempts (number input)
- Cancellation window hours (number input)
- No-show grace period minutes (number input)

`save()` calls `SettingsService::set()` for each field. Shows success notification.

#### `AreaResource`

List, create, edit. Columns: name, slug, is_active. Toggle active.

---

### 9.2 — Filament Navigation

Group resources into navigation groups:
- **المحلات:** ShopResource, AreaResource
- **المستخدمين:** UserResource
- **الحجوزات:** BookingResource
- **المالية:** TransactionResource, RefundResource
- **المحتوى:** ReviewResource
- **الإعدادات:** SettingsPage

---

### 9.3 — Phase 9 Tests

- `FilamentShopApprovalTest` — approve action sends WhatsApp, changes status
- `FilamentUserBlockTest` — ban/unblock works correctly
- `FilamentSettingsTest` — settings save and reflect via SettingsService

---

## Appendix — File Structure Reference

```
app/
├── Console/Commands/
│   ├── UpdateBookingStatuses.php
│   └── SendBookingReminders.php
├── Enums/               (all 12 enum files)
├── Filament/
│   ├── Pages/SettingsPage.php
│   └── Resources/
│       ├── ShopResource.php
│       ├── UserResource.php
│       ├── BookingResource.php
│       ├── TransactionResource.php
│       ├── RefundResource.php
│       ├── ReviewResource.php
│       └── AreaResource.php
├── Http/Middleware/EnsureUserRole.php
├── Jobs/IncrementShopView.php
├── Livewire/
│   ├── Auth/
│   │   ├── Login.php
│   │   ├── Register.php
│   │   └── ForgotPassword.php
│   ├── Booking/
│   │   ├── CreateBooking.php
│   │   ├── BookingList.php
│   │   └── BookingDetails.php
│   ├── Dashboard/
│   │   ├── Home.php
│   │   ├── ShopSettings.php
│   │   ├── ManageBarbers.php
│   │   ├── ManageServices.php
│   │   ├── Reservations.php
│   │   ├── ClientList.php
│   │   ├── Financials.php
│   │   └── Analytics.php
│   ├── Onboarding/
│   │   ├── ShopSetup.php
│   │   └── PendingApproval.php
│   ├── Profile/
│   │   ├── Index.php
│   │   ├── EditProfile.php
│   │   └── AppSettings.php
│   ├── Review/
│   │   └── SubmitReview.php
│   ├── Shop/
│   │   └── ShopPage.php
│   ├── Home.php
│   ├── Search.php
│   └── StaticPage.php
├── Models/Concerns/HasPublicUuid.php
├── Models/             (all model files)
└── Services/
    ├── BookingCodeGenerator.php
    ├── BookingService.php
    ├── RatingService.php        ← developer provides implementation
    ├── SettingsService.php
    ├── SlotCalculatorService.php
    ├── OtpService.php           ← developer provides implementation
    └── WhatsAppService.php      ← developer provides implementation

resources/views/
├── components/          (all 11 Blade components)
├── layouts/app.blade.php
├── livewire/            (mirrors Livewire folder structure)
└── offline.blade.php

database/
├── factories/           (factory per model)
├── migrations/          (all 18 tables)
└── seeders/
    ├── DatabaseSeeder.php
    ├── AreaSeeder.php
    ├── SettingsSeeder.php
    └── SuperAdminSeeder.php

tests/
├── Feature/
│   ├── Auth/
│   ├── Booking/
│   ├── Shop/
│   └── Filament/
└── Unit/
    ├── BookingCodeGeneratorTest.php
    ├── EnumsTest.php
    └── SettingsServiceTest.php
```

---

*Plan Version: 1.0 — FadeBook Implementation Plan for AI-assisted development*
*Companion documents: blueprint-v2.md, constitution-v1.md*

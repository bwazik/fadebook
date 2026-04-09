# Phase 0 — Constitution
> Non-negotiable rules for the entire FadeBook project

---

## 1. Introduction

### 1.1 Project Overview
FadeBook is a commission-based barbershop booking SaaS platform for the Egyptian market. It is a mobile-first Progressive Web App (PWA) built with Laravel 13, Livewire 4, and Tailwind CSS v4, following the **Liquid Glass design language** (iOS 26 aesthetic).

### 1.2 Tech Stack (Locked)
The following technologies are **non-negotiable** and cannot be substituted:

| Layer | Technology | Version |
|---|---|---|
| Backend | Laravel | 13.x |
| Admin Panel | Filament | 5.x |
| Reactive UI | Livewire | 4.x |
| Frontend Interactivity | Alpine.js | 3.x |
| Styling | Tailwind CSS | 4.x (CSS-first config) |
| Database | MySQL | 8.x |
| Notifications | WhatsApp Custom API | — |
| PWA | Vite + Manifest + Service Worker | — |

### 1.3 Language & Market
- **Primary Language:** Arabic (Egyptian dialect — عامية مصرية)
- **Layout Direction:** RTL (right-to-left)
- **Market:** Egypt only (for MVP)
- **Currency:** EGP (Egyptian Pound)

### 1.4 Business Model
- **No subscription fees** for barbershops
- **Commission-only**: dynamic percentage per shop, deducted automatically at payment gateway level
- Commission rates set individually per shop by super admin

---

## 2. Database Architecture

### 2.1 Schema Design Principles

#### Primary Keys
- Every table uses **auto-incrementing integer** `id` as primary key: `$table->increments('id');`
- Every table (except pivots) has a **UUID** column for public-facing identifiers: `$table->uuid('uuid')->unique();`
- UUIDs are used in URLs, API responses, and anywhere the ID is exposed to users

#### Timestamps & Soft Deletes
```php
$table->timestamps();      // created_at, updated_at (on every table)
$table->softDeletes();     // deleted_at (on every table except pivots)
```

#### Foreign Keys
- Always use **explicit foreign key constraints** with cascade rules:
```php
$table->foreign('user_id')->references('id')->on('users')
    ->onDelete('cascade')
    ->onUpdate('cascade');
```

#### Eloquent Scopes
Important filters must be implemented as model scopes to ensure consistency across the application:
- **Booking**: `pending()`, `confirmed()`, `inProgress()`, `completed()`, `cancelled()`, `noShow()`.
- **Shop**: `active()`, `pending()`, `online()`.
- **Active Flag**: Models with `is_active` or `is_flagged` (e.g., `Barber`, `Service`, `Area`, `Coupon`, `User`, `Review`) must implement `scopeActive()`.

#### Public Identifiers (UUIDs)
All models with a `uuid` column must use the `App\Models\HasPublicUuid` trait. This trait:
- Automatically generates a version 4 UUID on the `creating` event.
- Sets the `getRouteKeyName()` to `uuid` for implicit route model binding.
- Prevents manual UUID generation within model `booted()` methods.

#### Naming Conventions
- Table names: plural, snake_case (e.g., `barbershop_owners`, `booking_codes`)
- Column names: snake_case (e.g., `created_at`, `phone_verified_at`)
- Foreign keys: singular model name + `_id` (e.g., `user_id`, `shop_id`)

#### JSON Columns
JSON columns are **only used for structured data**, never for translations (since we're Arabic-only):
- ✅ `opening_hours` (structured schedule data)
- ✅ `meta` (flexible metadata storage)
- ✅ `whatsapp_messages.data` (API payload)
- ❌ `name`, `description`, `bio` (these are plain `string` or `text`)

---

### 2.2 Complete Table Definitions

#### **users**
```php
Schema::create('users', function (Blueprint $table) {
    $table->increments('id');
    $table->uuid('uuid')->unique();
    $table->string('name');
    $table->string('email')->unique()->nullable();
    $table->string('phone', 20)->unique();
    $table->date('birthday')->nullable();
    $table->timestamp('email_verified_at')->nullable();
    $table->timestamp('phone_verified_at')->nullable();
    $table->timestamp('last_otp_sent_at')->nullable();
    $table->unsignedTinyInteger('otp_request_count')->default(0);
    $table->string('password');
    $table->tinyInteger('role')->default(1)->comment('1 => client, 2 => barber_owner, 3 => super_admin');
    $table->unsignedTinyInteger('no_show_count')->default(0);
    $table->boolean('is_blocked')->default(false);
    $table->rememberToken();
    $table->timestamps();
    $table->softDeletes();
    
    $table->index(['phone', 'is_blocked']);
});
```

**Backed Enum:**
```php
// app/Enums/UserRole.php
enum UserRole: int
{
    case Client = 1;
    case BarberOwner = 2;
    case SuperAdmin = 3;
    
    public function getLabel(): string
    {
        return match($this) {
            self::Client => 'عميل',
            self::BarberOwner => 'صاحب محل',
            self::SuperAdmin => 'مدير النظام',
        };
    }
}
```

---

#### **password_reset_tokens**
```php
Schema::create('password_reset_tokens', function (Blueprint $table) {
    $table->string('phone', 20)->primary();
    $table->string('token');
    $table->timestamp('created_at')->nullable();
});
```

---

#### **phone_verifications**
```php
Schema::create('phone_verifications', function (Blueprint $table) {
    $table->increments('id');
    $table->unsignedInteger('user_id')->nullable();
    $table->string('phone', 20)->index();
    $table->string('otp_code', 6);
    $table->tinyInteger('type')->default(1)->comment('1 => registration, 2 => phone_verification, 3 => password_reset');
    $table->timestamp('expires_at')->index();
    $table->timestamp('verified_at')->nullable();
    $table->unsignedTinyInteger('attempts')->default(0);
    $table->boolean('is_used')->default(false)->index();
    $table->string('ip_address', 45)->nullable();
    $table->string('user_agent', 255)->nullable();
    $table->timestamps();

    $table->foreign('user_id')->references('id')->on('users')
        ->onDelete('cascade')
        ->onUpdate('cascade');

    $table->index(['phone', 'type', 'is_used']);
    $table->index(['phone', 'expires_at']);
});
```

**Backed Enum:**
```php
// app/Enums/OtpType.php
enum OtpType: int
{
    case Registration = 1;
    case PhoneVerification = 2;
    case PasswordReset = 3;
}
```

---

#### **phone_change_history**
```php
Schema::create('phone_change_history', function (Blueprint $table) {
    $table->increments('id');
    $table->unsignedInteger('user_id');
    $table->string('old_phone', 20);
    $table->string('new_phone', 20);
    $table->string('ip_address', 45)->nullable();
    $table->string('user_agent', 255)->nullable();
    $table->timestamps();

    $table->foreign('user_id')->references('id')->on('users')
        ->onDelete('cascade')
        ->onUpdate('cascade');

    $table->index(['user_id', 'created_at']);
});
```

---

#### **areas**
```php
Schema::create('areas', function (Blueprint $table) {
    $table->increments('id');
    $table->string('name');
    $table->string('slug')->unique();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

---

#### **shops**
```php
Schema::create('shops', function (Blueprint $table) {
    $table->increments('id');
    $table->uuid('uuid')->unique();
    $table->unsignedInteger('owner_id');
    $table->string('name');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->string('phone', 20);
    $table->text('address');
    $table->unsignedInteger('area_id')->comment('Required for URL structure: domain.com/{area}/{shop}');
    $table->json('opening_hours')->comment('{"monday": {"open": "09:00", "close": "21:00"}, ...}');
    $table->decimal('average_rating', 3, 2)->default(0);
    $table->unsignedInteger('total_reviews')->default(0);
    $table->unsignedInteger('total_views')->default(0);
    $table->unsignedInteger('total_bookings')->default(0);
    $table->tinyInteger('status')->default(0)->comment('0 => pending, 1 => active, 2 => suspended, 3 => rejected');
    $table->boolean('is_online')->default(true)->comment('Owner can toggle shop offline temporarily');
    $table->unsignedInteger('advance_booking_days')->default(7);
    $table->tinyInteger('barber_selection_mode')->default(1)->comment('1 => any_available, 2 => client_picks');
    $table->tinyInteger('payment_mode')->default(0)->comment('0 => no_payment, 1 => partial_deposit, 2 => full_payment');
    $table->decimal('deposit_percentage', 5, 2)->nullable()->comment('If partial deposit mode');
    $table->decimal('commission_rate', 5, 2)->default(10.00)->comment('Platform commission % set by admin');
    $table->text('rejection_reason')->nullable();
    $table->timestamp('approved_at')->nullable();
    $table->timestamp('rejected_at')->nullable();
    $table->timestamps();
    $table->softDeletes();

    $table->foreign('owner_id')->references('id')->on('users')
        ->onDelete('cascade')->onUpdate('cascade');
    $table->foreign('area_id')->references('id')->on('areas')
        ->onDelete('restrict')->onUpdate('cascade');

    $table->index(['status', 'is_online']);
    $table->index('slug');
});
```

**Backed Enums:**
```php
// app/Enums/ShopStatus.php
enum ShopStatus: int
{
    case Pending = 0;
    case Active = 1;
    case Suspended = 2;
    case Rejected = 3;
    
    public function getLabel(): string
    {
        return match($this) {
            self::Pending => 'قيد المراجعة',
            self::Active => 'نشط',
            self::Suspended => 'معلق',
            self::Rejected => 'مرفوض',
        };
    }
}

// app/Enums/BarberSelectionMode.php
enum BarberSelectionMode: int
{
    case AnyAvailable = 1;
    case ClientPicks = 2;
}

// app/Enums/PaymentMode.php
enum PaymentMode: int
{
    case NoPayment = 0;
    case PartialDeposit = 1;
    case FullPayment = 2;
}
```

---

#### **barbers**
```php
Schema::create('barbers', function (Blueprint $table) {
    $table->increments('id');
    $table->uuid('uuid')->unique();
    $table->unsignedInteger('shop_id');
    $table->unsignedInteger('user_id')->nullable()->comment('If barber has a user account');
    $table->string('name');
    $table->string('phone', 20)->nullable();
    $table->text('specialties')->nullable();
    $table->decimal('average_rating', 3, 2)->default(0);
    $table->unsignedInteger('total_reviews')->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->softDeletes();

    $table->foreign('shop_id')->references('id')->on('shops')
        ->onDelete('cascade')->onUpdate('cascade');
    $table->foreign('user_id')->references('id')->on('users')
        ->onDelete('set null')->onUpdate('cascade');

    $table->index(['shop_id', 'is_active']);
});
```

---

#### **barber_unavailability**
```php
Schema::create('barber_unavailability', function (Blueprint $table) {
    $table->increments('id');
    $table->unsignedInteger('barber_id');
    $table->date('unavailable_date');
    $table->timestamps();

    $table->foreign('barber_id')->references('id')->on('barbers')
        ->onDelete('cascade')->onUpdate('cascade');

    $table->unique(['barber_id', 'unavailable_date']);
});
```

---

#### **services**
```php
Schema::create('services', function (Blueprint $table) {
    $table->increments('id');
    $table->uuid('uuid')->unique();
    $table->unsignedInteger('shop_id');
    $table->string('name');
    $table->text('description')->nullable();
    $table->decimal('price', 8, 2);
    $table->unsignedInteger('duration_minutes');
    $table->boolean('is_active')->default(true);
    $table->unsignedInteger('sort_order')->default(0);
    $table->timestamps();
    $table->softDeletes();

    $table->foreign('shop_id')->references('id')->on('shops')
        ->onDelete('cascade')->onUpdate('cascade');

    $table->index(['shop_id', 'is_active']);
});
```

---

#### **bookings**
```php
Schema::create('bookings', function (Blueprint $table) {
    $table->increments('id');
    $table->uuid('uuid')->unique();
    $table->string('booking_code', 6)->unique()->comment('Short code like #AB12CD');
    $table->unsignedInteger('shop_id');
    $table->unsignedInteger('client_id');
    $table->unsignedInteger('barber_id')->nullable();
    $table->unsignedInteger('service_id');
    $table->unsignedInteger('coupon_id')->nullable();
    $table->dateTime('scheduled_at');
    $table->tinyInteger('status')->default(0)->comment('0 => pending, 1 => confirmed, 2 => in_progress, 3 => completed, 4 => cancelled, 5 => no_show');
    $table->decimal('service_price', 8, 2);
    $table->decimal('discount_amount', 8, 2)->default(0);
    $table->decimal('paid_amount', 8, 2)->default(0);
    $table->decimal('final_amount', 8, 2);
    $table->text('notes')->nullable();
    $table->boolean('policy_accepted')->default(false);
    $table->timestamp('confirmed_at')->nullable();
    $table->timestamp('arrived_at')->nullable();
    $table->timestamp('completed_at')->nullable();
    $table->timestamp('cancelled_at')->nullable();
    $table->tinyInteger('cancelled_by')->nullable()->comment('1 => client, 2 => shop');
    $table->timestamps();
    $table->softDeletes();

    $table->foreign('shop_id')->references('id')->on('shops')
        ->onDelete('cascade')->onUpdate('cascade');
    $table->foreign('client_id')->references('id')->on('users')
        ->onDelete('cascade')->onUpdate('cascade');
    $table->foreign('barber_id')->references('id')->on('barbers')
        ->onDelete('set null')->onUpdate('cascade');
    $table->foreign('service_id')->references('id')->on('services')
        ->onDelete('cascade')->onUpdate('cascade');
    $table->foreign('coupon_id')->references('id')->on('coupons')
        ->onDelete('set null')->onUpdate('cascade');

    $table->index(['shop_id', 'status', 'scheduled_at']);
    $table->index(['client_id', 'status']);
    $table->index('booking_code');
});
```

**Backed Enums:**
```php
// app/Enums/BookingStatus.php
enum BookingStatus: int
{
    case Pending = 0;
    case Confirmed = 1;
    case InProgress = 2;
    case Completed = 3;
    case Cancelled = 4;
    case NoShow = 5;
    
    public function getLabel(): string
    {
        return match($this) {
            self::Pending => 'قيد الانتظار',
            self::Confirmed => 'مؤكد',
            self::InProgress => 'جاري التنفيذ',
            self::Completed => 'مكتمل',
            self::Cancelled => 'ملغي',
            self::NoShow => 'لم يحضر',
        };
    }
}

// app/Enums/CancelledBy.php
enum CancelledBy: int
{
    case Client = 1;
    case Shop = 2;
}
```

**Automated Status Updates Command:**
A scheduled command `php artisan app:update-booking-statuses` runs periodically (e.g., every 5 minutes) to:
- Mark confirmed bookings as `in_progress` when the scheduled time arrives.
- Mark in-progress bookings as `completed` 1 hour after their scheduled time.
- Mark pending/unconfirmed bookings as `no_show` if a significant amount of time has passed without action.

---

#### **coupons**
> [!NOTE]
> **Manual Implementation by Developer**: Coupon generation and validation logic is manually implemented. Agents must not generate or override business logic for coupons.

```php
Schema::create('coupons', function (Blueprint $table) {
    $table->increments('id');
    $table->uuid('uuid')->unique();
    $table->unsignedInteger('shop_id');
    $table->string('code')->unique();
    $table->tinyInteger('discount_type')->default(1)->comment('1 => percentage, 2 => fixed');
    $table->decimal('discount_value', 8, 2);
    $table->dateTime('start_date')->nullable()->index();
    $table->dateTime('end_date')->nullable()->index();
    $table->boolean('is_active')->default(true)->index();
    $table->unsignedInteger('usage_limit')->nullable()->comment('Total uses allowed');
    $table->unsignedInteger('used_count')->default(0);
    $table->unsignedInteger('usage_limit_per_user')->nullable()->comment('Max uses per user');
    $table->decimal('minimum_amount', 8, 2)->nullable()->comment('Minimum booking amount to apply');
    $table->json('apply_to')->nullable()->comment('Categories or items coupon applies to');
    $table->json('except')->nullable()->comment('Categories or items coupon excludes');
    $table->timestamps();
    $table->softDeletes();

    $table->foreign('shop_id')->references('id')->on('shops')
        ->onDelete('cascade')->onUpdate('cascade');

    $table->index(['shop_id', 'is_active']);
    $table->index('code');
});
```

**Backed Enum:**
```php
// app/Enums/DiscountType.php
enum DiscountType: int
{
    case Percentage = 1;
    case Fixed = 2;
}
```

---

#### **coupon_usages**
```php
Schema::create('coupon_usages', function (Blueprint $table) {
    $table->increments('id');
    $table->unsignedInteger('coupon_id');
    $table->unsignedInteger('user_id');
    $table->unsignedInteger('usage_count')->default(0);
    $table->timestamps();

    $table->foreign('coupon_id')->references('id')->on('coupons')
        ->onDelete('cascade')->onUpdate('cascade');
    $table->foreign('user_id')->references('id')->on('users')
        ->onDelete('cascade')->onUpdate('cascade');

    $table->unique(['coupon_id', 'user_id']);
});
```

---

#### **refunds**
```php
Schema::create('refunds', function (Blueprint $table) {
    $table->increments('id');
    $table->uuid('uuid')->unique();
    $table->unsignedInteger('booking_id');
    $table->decimal('amount', 8, 2);
    $table->tinyInteger('reason')->comment('1 => client_cancel_early, 2 => shop_cancel, 3 => other');
    $table->tinyInteger('status')->default(0)->comment('0 => pending, 1 => processed, 2 => failed');
    $table->text('notes')->nullable();
    $table->text('error_message')->nullable();
    $table->timestamp('processed_at')->nullable();
    $table->timestamps();

    $table->foreign('booking_id')->references('id')->on('bookings')
        ->onDelete('cascade')->onUpdate('cascade');

    $table->index(['booking_id', 'status']);
});
```

**Backed Enums:**
```php
// app/Enums/RefundReason.php
enum RefundReason: int
{
    case ClientCancelEarly = 1;
    case ShopCancel = 2;
    case Other = 3;
}

// app/Enums/RefundStatus.php
enum RefundStatus: int
{
    case Pending = 0;
    case Processed = 1;
    case Failed = 2;
}
```

---

#### **images** (Polymorphic)
```php
Schema::create('images', function (Blueprint $table) {
    $table->increments('id');
    $table->morphs('imageable');
    $table->string('path');
    $table->string('disk')->default('public');
    $table->string('collection')->nullable()->comment('e.g., logo, banner, gallery, portfolio');
    $table->unsignedInteger('sort_order')->default(0);
    $table->json('meta')->nullable();
    $table->timestamps();

    $table->index(['imageable_type', 'imageable_id', 'collection']);
});
```

**Usage Examples:**
- Shop logo: `imageable_type = 'App\Models\Shop'`, `collection = 'logo'`
- Shop gallery: `imageable_type = 'App\Models\Shop'`, `collection = 'gallery'`
- Barber photo: `imageable_type = 'App\Models\Barber'`, `collection = 'photo'`
- Service photo: `imageable_type = 'App\Models\Service'`, `collection = 'photo'`

---

#### **reviews** (Polymorphic)
```php
Schema::create('reviews', function (Blueprint $table) {
    $table->increments('id');
    $table->uuid('uuid')->unique();
    $table->morphs('reviewable');
    $table->unsignedInteger('user_id');
    $table->unsignedInteger('booking_id');
    $table->decimal('rating', 3, 2);
    $table->text('comment')->nullable();
    $table->boolean('is_flagged')->default(false);
    $table->text('flag_reason')->nullable();
    $table->timestamps();
    $table->softDeletes();

    $table->foreign('user_id')->references('id')->on('users')
        ->onDelete('cascade')->onUpdate('cascade');
    $table->foreign('booking_id')->references('id')->on('bookings')
        ->onDelete('cascade')->onUpdate('cascade');

    $table->index(['reviewable_type', 'reviewable_id']);
    $table->unique(['booking_id', 'reviewable_type', 'reviewable_id']);
});
```

**Usage Examples:**
- Shop review: `reviewable_type = 'App\Models\Shop'`
- Barber review: `reviewable_type = 'App\Models\Barber'`

*Note on Review Form:* Post-booking, the client sees a single review form to rate the service. If they rate both the Shop and the Barber, two separate records are created in this `reviews` table—one where `reviewable_type` is `Shop`, and one where it is `Barber`. Ratings are calculated independently.

---

#### **views** (Polymorphic)
```php
Schema::create('views', function (Blueprint $table) {
    $table->increments('id');
    $table->morphs('viewable');
    $table->unsignedInteger('user_id')->nullable();
    $table->string('ip_address', 45)->nullable();
    $table->string('user_agent', 255)->nullable();
    $table->timestamp('viewed_at');
    $table->timestamps();

    $table->foreign('user_id')->references('id')->on('users')
        ->onDelete('cascade')->onUpdate('cascade');

    $table->index(['viewable_type', 'viewable_id']);
    $table->index('viewed_at');
});
```

---

#### **whatsapp_messages**
```php
Schema::create('whatsapp_messages', function (Blueprint $table) {
    $table->increments('id');
    $table->unsignedInteger('user_id')->nullable();
    $table->unsignedInteger('shop_id')->nullable();
    $table->string('phone', 20);
    $table->string('template');
    $table->tinyInteger('queue_type')->default(1)->comment('1 => instant, 2 => urgent, 3 => default');
    $table->json('data');
    $table->tinyInteger('status')->default(1)->comment('1 => queued, 2 => sent, 3 => failed');
    $table->text('error_message')->nullable();
    $table->unsignedInteger('attempts')->default(0);
    $table->timestamp('sent_at')->nullable();
    $table->timestamps();

    $table->foreign('user_id')->references('id')->on('users')
        ->onDelete('cascade')->onUpdate('cascade');
    $table->foreign('shop_id')->references('id')->on('shops')
        ->onDelete('cascade')->onUpdate('cascade');

    $table->index(['status', 'queue_type']);
    $table->index('phone');
});
```

**Backed Enums:**
```php
// app/Enums/WhatsAppQueueType.php
enum WhatsAppQueueType: int
{
    case Instant = 1;
    case Urgent = 2;
    case Default = 3;
}

// app/Enums/WhatsAppStatus.php
enum WhatsAppStatus: int
{
    case Queued = 1;
    case Sent = 2;
    case Failed = 3;
}
```

---

#### **settings**
```php
Schema::create('settings', function (Blueprint $table) {
    $table->increments('id');
    $table->string('key')->unique();
    $table->text('value')->nullable();
    $table->timestamps();

    $table->index('key');
});
```

**Seeded Values:**
```php
// Key-value pairs:
'theme_color' => '#ff2d55'  // Default accent color
'terms_content' => '<p>Terms of use content here...</p>'
'privacy_content' => '<p>Privacy policy content here...</p>'
```

---

### 2.3 Enum Convention

**Rule:** All enum-like columns in migrations **must** use `tinyInteger` (not MySQL ENUM type). Each such column **must** have a corresponding **backed PHP Enum class** (backed by `int`) in `app/Enums/`.

**Example:**
```php
// Migration
$table->tinyInteger('status')->default(0)
    ->comment('0 => pending, 1 => confirmed, 2 => in_progress, 3 => completed, 4 => cancelled, 5 => no_show');

// app/Enums/BookingStatus.php
namespace App\Enums;

enum BookingStatus: int
{
    case Pending = 0;
    case Confirmed = 1;
    case InProgress = 2;
    case Completed = 3;
    case Cancelled = 4;
    case NoShow = 5;

    public function getLabel(): string
    {
        return match($this) {
            self::Pending => 'قيد الانتظار',
            self::Confirmed => 'مؤكد',
            self::InProgress => 'جاري التنفيذ',
            self::Completed => 'مكتمل',
            self::Cancelled => 'ملغي',
            self::NoShow => 'لم يحضر',
        };
    }
    
    public function getColor(): string
    {
        return match($this) {
            self::Pending => 'warning',
            self::Confirmed => 'info',
            self::InProgress => 'primary',
            self::Completed => 'success',
            self::Cancelled => 'danger',
            self::NoShow => 'danger',
        };
    }
}
```

**Usage in Models:**
```php
use App\Enums\BookingStatus;

class Booking extends Model
{
    protected $casts = [
        'status' => BookingStatus::class,
    ];
}
```

---

### 2.4 Polymorphic Relationships
> [!NOTE]
> **Manual Implementation by Developer**: Logic for view count incrementing and review rating calculation services is manually implemented. Agents must not generate or override these services.


Three polymorphic tables are used:

#### **images**
- Can attach to: `Shop`, `Barber`, `Service`, `User`
- Collections: `logo`, `banner`, `gallery`, `portfolio`, `photo`

#### **reviews**
- Can attach to: `Shop`, `Barber`
- One review per booking per reviewable entity

#### **views**
- Can attach to: `Shop`
- Tracks unique views with IP and user tracking

---

## 3. Authentication & Security
> [!NOTE]
> **Manual Implementation by Developer**: Auth logic and Phone Verification OTP system are manually implemented by the Lead Developer. Agents must only prepare schema/models but must not generate or override auth/OTP business logic.


### 3.1 Phone-First Authentication Flow

**Primary Credential:** Phone number (not email)
- Phone is **required** and **unique**
- Email is **optional** for clients, **nullable** for everyone
- Password is **required** for all users

**Registration Flow (Client):**
1. User provides: phone, password, name
2. Account created immediately (no OTP required for registration)
3. User can log in with phone + password

**Registration Flow (Shop Owner):**
1. User provides: phone, password, name
2. Account created with `role = BarberOwner`
3. User completes shop details form
4. Shop status set to `pending`
5. Super admin reviews and approves/rejects
6. Shop owner receives WhatsApp notification

**Login Flow:**
- Phone + password (standard Laravel Auth)
- No OTP required for login

---

### 3.2 OTP System (WhatsApp-based)

**When OTP is used:**
1. **Booking confirmation** (before payment)
2. **Password reset**
3. **Phone number verification** (if implementing phone change feature later)

**OTP Generation:**
- 6-digit numeric code
- Stored in `phone_verifications` table
- Expires in **5 minutes**
- Maximum **3 verification attempts** per OTP
- Maximum **5 OTP requests per hour** per phone number

**OTP Flow:**
```php
// 1. Generate OTP
$otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

// 2. Store in database
PhoneVerification::create([
    'user_id' => $user?->id,
    'phone' => $phone,
    'otp_code' => Hash::make($otp), // Hashed for security
    'type' => OtpType::BookingConfirmation,
    'expires_at' => now()->addMinutes(5),
    'ip_address' => request()->ip(),
    'user_agent' => request()->userAgent(),
]);

// 3. Queue WhatsApp message
WhatsAppMessage::create([
    'phone' => $phone,
    'template' => 'otp_code',
    'queue_type' => WhatsAppQueueType::Instant,
    'data' => ['otp' => $otp],
]);
```

**OTP Verification:**
```php
$verification = PhoneVerification::where('phone', $phone)
    ->where('type', $type)
    ->where('is_used', false)
    ->where('expires_at', '>', now())
    ->where('attempts', '<', 3)
    ->first();

if (!$verification || !Hash::check($otp, $verification->otp_code)) {
    $verification?->increment('attempts');
    throw new InvalidOtpException();
}

$verification->update([
    'verified_at' => now(),
    'is_used' => true,
]);
```

---

### 3.3 Password Management

**Password Requirements:**
- Minimum 8 characters
- Stored using `bcrypt` (Laravel default)
- Password reset via WhatsApp OTP

**Password Reset Flow:**
1. User enters phone number
2. OTP sent via WhatsApp
3. User verifies OTP
4. User sets new password
5. Token stored in `password_reset_tokens` table

---

### 3.4 Rate Limiting & Abuse Prevention

**OTP Request Rate Limiting:**
- Maximum **5 OTP requests per hour** per phone number
- Tracked via `users.last_otp_sent_at` and `users.otp_request_count`
- Counter resets after 1 hour

**Login Rate Limiting:**
- Laravel's built-in throttle: **5 attempts per minute**
- Lockout duration: **1 minute**

**Booking Rate Limiting:**
- Maximum **3 pending bookings** per client at any time
- Maximum **10 bookings per day** per client

---

## 4. Design System (Liquid Glass)

### 4.1 Tailwind v4 Configuration

**Critical Change:** Tailwind v4 uses **CSS-first configuration** (no more `tailwind.config.js`).

**File:** `resources/css/app.css`
```css
@import "tailwindcss";

@theme {
  /* Typography */
  --font-sans: "Tajawal", ui-sans-serif, system-ui, sans-serif;
  
  /* Colors */
  --color-fadebook-dark: #0f172a;
  --color-fadebook-accent: #ff2d55; /* Default, overridden by JS */
  
  /* Spacing & Radius */
  --radius-card: 2rem;
  --radius-button: 1rem;
  
  /* Safe Areas */
  --safe-area-top: env(safe-area-inset-top);
  --safe-area-bottom: env(safe-area-inset-bottom);
  --safe-area-left: env(safe-area-inset-left);
  --safe-area-right: env(safe-area-inset-right);
}

/* Base Styles */
* {
  -webkit-tap-highlight-color: transparent;
}

body {
  @apply font-sans antialiased;
}
```

---

### 4.2 Color System & Theme Switcher

**Available Themes:**
```javascript
const themes = {
  classic: '#ff2d55',   // FadeBook Red (default)
  ocean: '#007AFF',     // iOS Blue
  mint: '#34C759',      // iOS Green
  sunset: '#FF9500',    // iOS Orange
  lavender: '#AF52DE'   // iOS Purple
};
```

**Theme Storage:**
- Stored in **localStorage** as `fadebook_theme`
- Applied via CSS custom property override

**Implementation:**
```javascript
// Get saved theme or default
const savedTheme = localStorage.getItem('fadebook_theme') || 'classic';
const accentColor = themes[savedTheme];

// Apply to document
document.documentElement.style.setProperty('--color-fadebook-accent', accentColor);

// On theme change
function changeTheme(themeName) {
  localStorage.setItem('fadebook_theme', themeName);
  document.documentElement.style.setProperty('--color-fadebook-accent', themes[themeName]);
}
```

**Settings Page UI:**
```blade
<div class="flex gap-3">
  @foreach(['classic', 'ocean', 'mint', 'sunset', 'lavender'] as $theme)
    <button 
      @click="changeTheme('{{ $theme }}')"
      class="w-12 h-12 rounded-full"
      :class="currentTheme === '{{ $theme }}' ? 'ring-2 ring-offset-2 ring-fadebook-accent' : ''"
      style="background-color: {{ $themes[$theme] }}">
    </button>
  @endforeach
</div>
```

---

### 4.3 Dark/Light Mode

**System Preference Detection:**
```javascript
// Check system preference
const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

// Get saved preference (overrides system)
const savedMode = localStorage.getItem('darkMode');

// Apply
if (savedMode === 'true' || (!savedMode && prefersDark)) {
  document.documentElement.classList.add('dark');
} else {
  document.documentElement.classList.remove('dark');
}
```

**Toggle Implementation:**
```javascript
function toggleDarkMode() {
  const isDark = document.documentElement.classList.toggle('dark');
  localStorage.setItem('darkMode', isDark);
}
```

**Meta Tags:**
```html
<meta name="theme-color" content="#f2f2f7" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#000000" media="(prefers-color-scheme: dark)">
```

---

### 4.4 Typography & RTL

**Primary Font:** Tajawal (Arabic-optimized)
```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
```

**RTL Setup:**
```html
<html lang="ar" dir="rtl">
```

**Text Direction Override (for English in Arabic context):**
```html
<input type="tel" dir="ltr" placeholder="01xxxxxxxxx">
```

**Font Weights:**
- Regular: 400
- Medium: 500
- Bold: 700
- Black: 900 (for headers)

---

### 4.5 Component Library

#### **Glass Card**
```blade
{{-- components/glass-card.blade.php --}}
<div {{ $attributes->merge([
  'class' => 'relative overflow-hidden bg-white/70 dark:bg-[#1c1c1e]/70 backdrop-blur-2xl border border-black/5 dark:border-white/10 rounded-[2rem] p-5 shadow-sm transition-all duration-300'
]) }}>
  {{ $slot }}
</div>
```

**Usage:**
```blade
<x-glass-card class="mb-4">
  <p>Card content here</p>
</x-glass-card>
```

---

#### **iOS Button**
```blade
{{-- components/ios-button.blade.php --}}
@props(['target' => null])

<button {{ $attributes->merge([
  'type' => 'submit',
  'class' => 'w-full mt-4 py-3.5 rounded-2xl bg-fadebook-accent text-white font-bold active:scale-95 transition-all shadow-lg shadow-fadebook-accent/20 disabled:opacity-50 flex justify-center items-center gap-2'
]) }}>
  @if($target)
    <span wire:loading.remove wire:target="{{ $target }}">{{ $slot }}</span>
    <span wire:loading wire:target="{{ $target }}" class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
    <span wire:loading wire:target="{{ $target }}">اتقل...</span>
  @else
    {{ $slot }}
  @endif
</button>
```

---

#### **iOS Select**
```blade
{{-- Exact same as Gymz implementation --}}
{{-- See document index 9 for full code --}}
```

---

#### **iOS Input**
```blade
{{-- components/ios-input.blade.php --}}
@props(['label' => '', 'id' => '', 'type' => 'text', 'dir' => 'auto', 'labelWidth' => 'w-16'])

<div class="relative border-b border-black/5 dark:border-white/10 last:border-0 flex items-center px-4">
  @if($label)
    <span class="text-gray-400 dark:text-white/40 text-sm font-medium {{ $labelWidth }}">{{ $label }}</span>
  @endif
  <input {{ $attributes->merge([
    'type' => $type,
    'id' => $id,
    'dir' => $dir,
    'class' => 'flex-1 bg-transparent border-0 focus:ring-0 text-gray-900 dark:text-white px-2 py-4 text-sm font-bold placeholder-gray-400'
  ]) }}>
</div>
```

---

#### **iOS Input Group**
```blade
{{-- components/ios-input-group.blade.php --}}
<div {{ $attributes->merge([
  'class' => 'bg-white/70 dark:bg-[#1c1c1e]/70 backdrop-blur-3xl border border-black/5 dark:border-white/10 rounded-3xl overflow-hidden shadow-sm'
]) }}>
  {{ $slot }}
</div>
```

**Usage:**
```blade
<x-ios-input-group>
  <x-ios-input label="الاسم" wire:model="name" />
  <x-ios-input label="الموبايل" type="tel" dir="ltr" wire:model="phone" />
</x-ios-input-group>
```

---

#### **iOS Textarea**
```blade
{{-- components/ios-textarea.blade.php --}}
<textarea {{ $attributes->merge([
  'class' => 'w-full rounded-2xl bg-black/5 dark:bg-white/10 border-0 text-gray-900 dark:text-white text-sm px-4 py-3 focus:bg-white dark:focus:bg-[#3a3a3c] focus:ring-2 focus:ring-fadebook-accent/50 transition-all resize-none placeholder-gray-400 dark:placeholder-white/30'
]) }}></textarea>
```

---

#### **iOS Toggle**
```blade
{{-- components/ios-toggle.blade.php --}}
@props(['label' => '', 'description' => ''])

<div class="flex items-center justify-between py-1">
  <div>
    <label class="text-sm font-medium text-gray-900 dark:text-white">{{ $label }}</label>
    @if($description)
      <p class="text-xs text-gray-500 dark:text-white/40">{{ $description }}</p>
    @endif
  </div>
  <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
    <input type="checkbox" {{ $attributes->merge(['class' => 'sr-only peer']) }}>
    <div class="w-11 h-6 bg-gray-300 dark:bg-white/10 rounded-full peer peer-checked:bg-fadebook-accent peer-focus:ring-2 peer-focus:ring-fadebook-accent/30 transition-colors after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all after:shadow-sm peer-checked:after:translate-x-full"></div>
  </label>
</div>
```

---

#### **Bottom Sheet Modal**
```blade
{{-- Standard pattern from Gymz --}}
<div x-data="{ open: @entangle('showModal').live }" 
     x-show="open"
     x-init="$watch('open', val => $dispatch(val ? 'hide-bottom-nav' : 'show-bottom-nav'))"
     style="display: none;"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-out duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 bg-black/40 backdrop-blur-md flex items-end justify-center"
     @click.self="open = false">

  <div x-show="open"
       x-transition:enter="transition ease-out duration-300"
       x-transition:enter-start="translate-y-full"
       x-transition:enter-end="translate-y-0"
       x-transition:leave="transition ease-out duration-300"
       x-transition:leave-start="translate-y-0"
       x-transition:leave-end="translate-y-full"
       class="bg-white/80 dark:bg-[#1c1c1e]/80 backdrop-blur-3xl border-t border-white/50 dark:border-white/10 p-6 rounded-t-[2rem] w-full max-w-md shadow-2xl pb-[calc(1.5rem+env(safe-area-inset-bottom))]"
       @click.stop>
    
    {{-- Drag Handle --}}
    <div class="flex justify-center mb-4">
      <div class="w-10 h-1 rounded-full bg-gray-300 dark:bg-white/20"></div>
    </div>
    
    {{ $slot }}
  </div>
</div>
```

---

#### **iOS Alert**
```blade
{{-- Exact same as Gymz implementation --}}
{{-- See document for full code --}}
```

---

#### **Toast Notification**
```blade
{{-- Exact same as Gymz implementation --}}
{{-- See document for full code --}}
```

---

#### **Bottom Navigation**
```blade
{{-- Exact same as Gymz implementation with FadeBook routes --}}
@php
  $navItems = [
    ['route' => 'home', 'label' => 'الرئيسية', 'icon' => '...'],
    ['route' => 'bookings', 'label' => 'حجوزاتي', 'icon' => '...'],
    ['route' => 'search', 'label' => 'البحث', 'icon' => '...'],
    ['route' => 'profile', 'label' => 'حسابي', 'icon' => '...'],
  ];
@endphp

{{-- See Gymz bottom-nav for full implementation --}}
```

---

### 4.6 Spacing, Shadows, Blur Effects

**Spacing Scale (Tailwind defaults + custom):**
```css
/* Card padding */
p-5      /* 1.25rem - Standard card padding */
p-6      /* 1.5rem - Larger cards */

/* Gaps */
gap-3    /* 0.75rem - Between elements */
gap-4    /* 1rem - Between sections */

/* Margins */
mb-4     /* 1rem - Between cards */
mb-6     /* 1.5rem - Between sections */
```

**Shadows:**
```css
/* Light shadow for cards */
shadow-sm

/* Modal/bottom sheet shadow */
shadow-2xl

/* Button shadow with accent glow */
shadow-lg shadow-fadebook-accent/20
```

**Blur Effects:**
```css
/* Card backdrop */
backdrop-blur-2xl

/* Modal/bottom sheet backdrop */
backdrop-blur-3xl

/* Overlay backdrop */
backdrop-blur-md
```

---

### 4.7 Safe Area Handling

**Bottom Nav Safe Area:**
```css
bottom: calc(2rem + env(safe-area-inset-bottom));
```

**Bottom Sheet Safe Area:**
```css
padding-bottom: calc(1.5rem + env(safe-area-inset-bottom));
```

**Full-Screen Layouts:**
```css
padding-top: env(safe-area-inset-top);
padding-bottom: env(safe-area-inset-bottom);
```

**Viewport Meta Tag:**
```html
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
```

---

## 5. File Structure & Naming Conventions

### 5.1 Laravel Folder Structure

```
app/
├── Console/
├── Enums/                    # All backed enum classes
│   ├── BookingStatus.php
│   ├── ShopStatus.php
│   ├── UserRole.php
│   └── ...
├── Exceptions/
├── Filament/                 # Filament admin panel
│   ├── Resources/
│   ├── Pages/
│   └── Widgets/
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── Requests/
├── Livewire/                 # Livewire components
│   ├── Auth/
│   ├── Booking/
│   ├── Shop/
│   └── Profile/
├── Models/
│   ├── User.php
│   ├── Shop.php
│   ├── Barber.php
│   ├── Service.php
│   ├── Booking.php
│   ├── Coupon.php
│   └── ...
├── Notifications/            # WhatsApp notifications
├── Policies/
├── Providers/
├── Services/                 # Business logic services
│   ├── BookingService.php
│   ├── WhatsAppService.php
│   ├── PaymentService.php
│   └── ...
└── View/
    └── Components/

resources/
├── css/
│   └── app.css              # Tailwind v4 config + custom CSS
├── js/
│   └── app.js
└── views/
    ├── components/          # Blade components
    │   ├── glass-card.blade.php
    │   ├── ios-button.blade.php
    │   ├── ios-select.blade.php
    │   └── ...
    ├── layouts/
    │   └── app.blade.php
    └── livewire/            # Livewire views
```

---

### 5.2 Livewire Components

**Naming:**
- PascalCase for class names: `CreateBooking`, `ShopDashboard`
- kebab-case for view files: `create-booking.blade.php`, `shop-dashboard.blade.php`

**Organization:**
```
app/Livewire/
├── Auth/
│   ├── Login.php
│   ├── Register.php
│   └── ForgotPassword.php
├── Booking/
│   ├── CreateBooking.php
│   ├── BookingList.php
│   └── BookingDetails.php
├── Shop/
│   ├── ShopDashboard.php
│   ├── ManageServices.php
│   └── ManageBarbers.php
└── Profile/
    ├── EditProfile.php
    └── BookingHistory.php
```

---

### 5.3 Blade Components

**Naming:**
- kebab-case: `glass-card`, `ios-button`, `bottom-nav`

**Location:**
- `resources/views/components/`

**Usage:**
```blade
<x-glass-card>...</x-glass-card>
<x-ios-button>حفظ</x-ios-button>
```

---

### 5.4 Enums Location

All enum classes in `app/Enums/`:
```
app/Enums/
├── BookingStatus.php
├── CancelledBy.php
├── DiscountType.php
├── OtpType.php
├── PaymentMode.php
├── RefundReason.php
├── RefundStatus.php
├── ShopStatus.php
├── UserRole.php
├── WhatsAppQueueType.php
└── WhatsAppStatus.php
```

---

## 6. Code Style & Standards

### 6.1 PSR-12 Compliance

All PHP code must follow **PSR-12** coding standard.

**Key Rules:**
- 4 spaces for indentation (no tabs)
- Opening braces on same line for classes/methods
- One blank line after namespace declarations
- `declare(strict_types=1);` at top of every PHP file

**Example:**
```php
<?php

declare(strict_types=1);

namespace App\Livewire\Booking;

use Livewire\Component;

class CreateBooking extends Component
{
    public function render()
    {
        return view('livewire.booking.create-booking');
    }
}
```

---

### 6.2 Strict Types

Every PHP file **must** declare strict types:
```php
<?php

declare(strict_types=1);
```

---

### 6.3 No Raw SQL Queries

**Rule:** Never use `DB::raw()` or raw SQL strings. Use Eloquent query builder.

**Bad:**
```php
DB::select('SELECT * FROM users WHERE phone = ?', [$phone]);
```

**Good:**
```php
User::where('phone', $phone)->first();
```

**Exception:** Complex queries that cannot be expressed in Eloquent may use Query Builder, but must be isolated in Repository classes.

---

### 6.4 Eloquent Conventions

**Model Properties:**
```php
#[Fillable([
    'uuid',
    'booking_code',
    'shop_id',
    'client_id',
    'barber_id',
    'service_id',
    'coupon_id',
    'scheduled_at',
    'status',
    'service_price',
    'discount_amount',
    'paid_amount',
    'final_amount',
    'notes',
    'policy_accepted',
])]
#[Hidden(['id'])]
class Booking extends Model
{
    protected $casts = [
        'scheduled_at' => 'datetime',
        'status' => BookingStatus::class,
        'policy_accepted' => 'boolean',
    ];
}
```

**Relationships:**
```php
// Always type-hint return types
public function shop(): BelongsTo
{
    return $this->belongsTo(Shop::class);
}

public function client(): BelongsTo
{
    return $this->belongsTo(User::class, 'client_id');
}
```

---

### 6.5 Comment Standards

**PHPDoc blocks required for:**
- All public methods
- All class properties
- Complex logic

**Example:**
```php
/**
 * Send OTP code via WhatsApp to the given phone number.
 *
 * @param string $phone The phone number in international format
 * @param OtpType $type The type of OTP being sent
 * @return PhoneVerification
 * @throws TooManyOtpRequestsException
 */
public function sendOtp(string $phone, OtpType $type): PhoneVerification
{
    // Implementation...
}
```

---

## 7. WhatsApp Integration

### 7.1 Message Queue System

**Architecture:**
- All WhatsApp messages are **queued** (never sent synchronously)
- Uses `whatsapp_messages` table as queue
- Three priority levels: instant, urgent, default

**Queue Worker:**
```bash
php artisan queue:work --queue=whatsapp_instant,whatsapp_urgent,whatsapp_default
```

---

### 7.2 Template Structure

**Message Templates (stored in config/whatsapp.php):**
```php
return [
    'templates' => [
        'otp_code' => 'كود التحقق الخاص بك هو: {{otp}}',
        'booking_confirmed' => 'تم تأكيد حجزك في {{shop_name}} يوم {{date}} الساعة {{time}}. كود الحجز: {{code}}',
        'booking_reminder' => 'تذكير: حجزك في {{shop_name}} بعد {{hours}} ساعات',
        // ... all other templates
    ],
];
```

**Sending a Message:**
```php
use App\Services\WhatsAppService;
use App\Enums\WhatsAppQueueType;

WhatsAppService::send(
    phone: $user->phone,
    template: 'booking_confirmed',
    data: [
        'shop_name' => $booking->shop->name,
        'date' => $booking->scheduled_at->format('Y-m-d'),
        'time' => $booking->scheduled_at->format('H:i'),
        'code' => $booking->booking_code,
    ],
    queueType: WhatsAppQueueType::Instant
);
```

---

### 7.3 Error Handling & Retry Logic

**Retry Policy:**
- Maximum **3 attempts** per message
- Retry delays: 1 minute, 5 minutes, 15 minutes
- After 3 failures → mark as `failed` and log error

**Implementation:**
```php
// In WhatsAppService
public function process(WhatsAppMessage $message): void
{
    try {
        $this->api->send($message->phone, $message->template, $message->data);
        
        $message->update([
            'status' => WhatsAppStatus::Sent,
            'sent_at' => now(),
        ]);
    } catch (\Exception $e) {
        $message->increment('attempts');
        
        if ($message->attempts >= 3) {
            $message->update([
                'status' => WhatsAppStatus::Failed,
                'error_message' => $e->getMessage(),
            ]);
        } else {
            // Re-queue with delay
            dispatch(new ProcessWhatsAppMessage($message))
                ->delay(now()->addMinutes($message->attempts * 5));
        }
    }
}
```

---

## 8. Payment Gateway Architecture

### 8.1 Gateway Abstraction Layer

**Interface:**
```php
// app/Contracts/PaymentGateway.php
interface PaymentGateway
{
    public function createPayment(array $data): PaymentResponse;
    public function processRefund(string $transactionId, float $amount): RefundResponse;
    public function verifyWebhook(array $payload): bool;
}
```

**Implementation (e.g., Fawry):**
```php
// app/Services/Payment/FawryGateway.php
class FawryGateway implements PaymentGateway
{
    public function createPayment(array $data): PaymentResponse
    {
        // Fawry-specific logic
    }
    
    public function processRefund(string $transactionId, float $amount): RefundResponse
    {
        // Fawry-specific refund logic
    }
}
```

**Service Provider Binding:**
```php
$this->app->bind(PaymentGateway::class, FawryGateway::class);
```

This allows swapping gateways without touching business logic.

---

### 8.2 Commission Split Logic

**At Payment Time:**
```php
$servicePrice = $booking->service->price;
$commissionRate = $booking->shop->commission_rate; // e.g., 10.00 (%)
$commissionAmount = ($servicePrice * $commissionRate) / 100;
$shopAmount = $servicePrice - $commissionAmount;

// Pass to gateway
$payment = app(PaymentGateway::class)->createPayment([
    'total_amount' => $servicePrice,
    'splits' => [
        ['account_id' => config('payment.platform_account'), 'amount' => $commissionAmount],
        ['account_id' => $shop->payment_account_id, 'amount' => $shopAmount],
    ],
]);
```

---

### 8.3 Refund Handling

**Refund Flow:**
1. Create refund record in `refunds` table with `status = pending`
2. Call gateway refund API
3. Update refund status based on response
4. Update booking status to `cancelled`

**Implementation:**
```php
$refund = Refund::create([
    'booking_id' => $booking->id,
    'amount' => $booking->paid_amount,
    'reason' => RefundReason::ClientCancelEarly,
    'status' => RefundStatus::Pending,
]);

try {
    $response = app(PaymentGateway::class)->processRefund(
        $booking->payment_transaction_id,
        $booking->paid_amount
    );
    
    $refund->update([
        'status' => RefundStatus::Processed,
        'processed_at' => now(),
    ]);
} catch (\Exception $e) {
    $refund->update([
        'status' => RefundStatus::Failed,
        'error_message' => $e->getMessage(),
    ]);
}
```

---

## 9. PWA Requirements

### 9.1 Manifest Configuration

**File:** `public/manifest.json`
```json
{
  "name": "FadeBook - احجز قصتك",
  "short_name": "FadeBook",
  "description": "منصة حجز صالونات الحلاقة في مصر",
  "start_url": "/",
  "display": "standalone",
  "background_color": "#ffffff",
  "theme_color": "#ff2d55",
  "orientation": "portrait",
  "dir": "rtl",
  "lang": "ar",
  "icons": [
    {
      "src": "/icons/icon-192x192.png",
      "sizes": "192x192",
      "type": "image/png",
      "purpose": "any maskable"
    },
    {
      "src": "/icons/icon-512x512.png",
      "sizes": "512x512",
      "type": "image/png",
      "purpose": "any maskable"
    }
  ]
}
```

---

### 9.2 Service Worker

**File:** `public/sw.js`
```javascript
const CACHE_NAME = 'fadebook-v1';
const urlsToCache = [
  '/',
  '/offline',
  '/css/app.css',
  '/js/app.js',
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => cache.addAll(urlsToCache))
  );
});

self.addEventListener('fetch', (event) => {
  event.respondWith(
    caches.match(event.request)
      .then((response) => response || fetch(event.request))
      .catch(() => caches.match('/offline'))
  );
});
```

---

### 9.3 Offline Fallback

**Route:**
```php
Route::get('/offline', function () {
    return view('offline');
});
```

**View (`resources/views/offline.blade.php`):**
```blade
<x-glass-card class="text-center">
  <svg class="w-16 h-16 mx-auto mb-4 text-gray-400">...</svg>
  <h3 class="font-bold text-lg mb-2">مفيش اتصال بالإنترنت</h3>
  <p class="text-sm text-gray-500">تأكد من الاتصال وحاول تاني</p>
</x-glass-card>
```

---

### 9.4 Install Prompts

**iOS Install Banner:**
```blade
@if(!$isPwaInstalled && $isIOS)
  <div class="fixed bottom-[calc(6rem+env(safe-area-inset-bottom))] left-0 right-0 mx-4">
    <x-glass-card class="text-center">
      <p class="text-sm mb-3">عايز تستخدم FadeBook زي الأبلكيشن؟</p>
      <p class="text-xs text-gray-500">اضغط على <svg class="inline w-4 h-4">...</svg> واختار "أضف للشاشة الرئيسية"</p>
    </x-glass-card>
  </div>
@endif
```

**Android Install Prompt:**
```javascript
let deferredPrompt;

window.addEventListener('beforeinstallprompt', (e) => {
  e.preventDefault();
  deferredPrompt = e;
  
  // Show custom install button
  showInstallPromotion();
});

function installApp() {
  if (deferredPrompt) {
    deferredPrompt.prompt();
    deferredPrompt.userChoice.then((choice) => {
      if (choice.outcome === 'accepted') {
        console.log('User accepted install');
      }
      deferredPrompt = null;
    });
  }
}
```

---

## 10. Testing Strategy

### 10.1 Pest Configuration

**Framework:** Pest PHP (Laravel's testing framework)

**File:** `tests/Pest.php`
```php
uses(
    Tests\TestCase::class,
    Illuminate\Foundation\Testing\RefreshDatabase::class,
)->in('Feature');
```

---

### 10.2 Unit Tests

**Scope:** Test individual methods in isolation

**Location:** `tests/Unit/`

**Example:**
```php
// tests/Unit/BookingCodeGeneratorTest.php
it('generates unique 6-character booking code', function () {
    $code = BookingCodeGenerator::generate();
    
    expect($code)
        ->toBeString()
        ->toHaveLength(6)
        ->toMatch('/^[A-Z0-9]{6}$/');
});
```

---

### 10.3 Feature Tests

**Scope:** Test user flows and interactions

**Location:** `tests/Feature/`

**Example:**
```php
// tests/Feature/BookingFlowTest.php
it('allows client to create a booking with OTP verification', function () {
    $user = User::factory()->create();
    $shop = Shop::factory()->create();
    $service = Service::factory()->for($shop)->create();
    
    // Create booking
    $response = actingAs($user)
        ->post('/bookings', [
            'shop_id' => $shop->id,
            'service_id' => $service->id,
            'scheduled_at' => now()->addDays(2),
        ]);
    
    $response->assertStatus(200);
    
    // Verify OTP sent
    assertDatabaseHas('phone_verifications', [
        'phone' => $user->phone,
        'type' => OtpType::BookingConfirmation,
    ]);
});
```

---

### 10.4 No E2E (for MVP)

End-to-end browser tests are **deferred** until post-MVP. Focus on unit and feature tests only.

---

## 11. Booking Code Generation

### 11.1 Format & Uniqueness

**Format:** 6 alphanumeric characters (uppercase)
- Example: `AB12CD`, `9XYZ42`
- Character set: `A-Z` and `0-9` (excluding ambiguous characters: `0`, `O`, `I`, `1`)

**Generation:**
```php
// app/Services/BookingCodeGenerator.php
class BookingCodeGenerator
{
    private const CHARSET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // 32 chars
    private const LENGTH = 6;
    
    public static function generate(): string
    {
        do {
            $code = '';
            for ($i = 0; $i < self::LENGTH; $i++) {
                $code .= self::CHARSET[random_int(0, strlen(self::CHARSET) - 1)];
            }
        } while (Booking::where('booking_code', $code)->exists());
        
        return $code;
    }
}
```

---

### 11.2 Collision Handling

**Strategy:** Loop until unique code is found

**Probability of collision:**
- Character set: 32 characters
- Code length: 6 characters
- Total combinations: 32^6 = **1,073,741,824**
- Even at 1 million bookings, collision probability is negligible

---

## 12. Commission System Rules

### 12.1 Per-Shop Rates

**Rule:** Each shop has an **individual commission rate** set by the super admin.

**Field:** `shops.commission_rate` (decimal, e.g., `10.00` = 10%)

**Default:** 10% (configurable in Filament)

**Range:** 0% - 50%

---

### 12.2 Calculation Logic

**Formula:**
```php
$commissionAmount = ($servicePrice * $shop->commission_rate) / 100;
$shopEarnings = $servicePrice - $commissionAmount;
```

**Example:**
- Service price: 100 EGP
- Commission rate: 15%
- Commission: 15 EGP (to platform)
- Shop earnings: 85 EGP

---

### 12.3 Reporting

**Shop Dashboard:**
- Displays **commission deducted** transparently
- Shows **net earnings** (after commission)

**Super Admin Dashboard:**
- Total platform revenue (all commissions)
- Per-shop commission breakdown
- Monthly commission report

---

## 13. No-Show Strike System

### 13.1 Rules

**Client No-Show Tracking:**
- Field: `users.no_show_count`
- After **1st no-show**: WhatsApp warning sent
- After **2nd no-show**: `users.is_blocked = true` → cannot create new bookings
- Super admin can manually unblock via Filament

**No-Show Definition:**
- Barber marks booking as `no_show` status
- Only applies if client doesn't arrive within **15 minutes** of scheduled time

---

### 13.2 Unblock Process

**Filament Action:**
```php
Tables\Actions\Action::make('unblock')
    ->label('إلغاء الحظر')
    ->action(fn (User $record) => $record->update([
        'is_blocked' => false,
        'no_show_count' => 0,
    ]))
    ->requiresConfirmation()
    ->visible(fn (User $record) => $record->is_blocked)
```

---

## 14. Cancellation & Refund Policy

### 14.1 Policy Rules

| Scenario | Refund | Strike |
|---|---|---|
| Client cancels **>2 hours** before | Full refund | No |
| Client cancels **<2 hours** before | No refund | No |
| Client **no-show** (unpaid) | N/A | Yes (+1 strike) |
| Client **no-show** (paid) | No refund | Yes (+1 strike) |
| **Shop cancels** | Full refund always | No |

---

### 14.2 Implementation

**Client Cancel (>2 hours before):**
```php
if ($booking->scheduled_at->diffInHours(now()) > 2) {
    // Full refund
    Refund::create([
        'booking_id' => $booking->id,
        'amount' => $booking->paid_amount,
        'reason' => RefundReason::ClientCancelEarly,
        'status' => RefundStatus::Pending,
    ]);
    
    $booking->update([
        'status' => BookingStatus::Cancelled,
        'cancelled_by' => CancelledBy::Client,
    ]);
}
```

**Client Cancel (<2 hours before):**
```php
// No refund
$booking->update([
    'status' => BookingStatus::Cancelled,
    'cancelled_by' => CancelledBy::Client,
]);
```

**Shop Cancel:**
```php
// Always full refund
Refund::create([
    'booking_id' => $booking->id,
    'amount' => $booking->paid_amount,
    'reason' => RefundReason::ShopCancel,
    'status' => RefundStatus::Pending,
]);

$booking->update([
    'status' => BookingStatus::Cancelled,
    'cancelled_by' => CancelledBy::Shop,
]);
```

---

## 15. Advance Booking Window

### 15.1 Configuration

**Field:** `shops.advance_booking_days`
- Default: 7 days
- Range: 1 - 90 days
- Configurable per shop by owner

---

### 15.2 Enforcement

**In Booking Calendar:**
```php
$maxDate = now()->addDays($shop->advance_booking_days);

// Only show dates up to $maxDate
$availableDates = collect()
    ->range(now()->startOfDay(), $maxDate)
    ->filter(fn ($date) => /* check if shop is open */);
```

---

## 16. Additional Rules

### 16.1 Waitlist System

**Deferred to Post-MVP**
- Table structure prepared but feature not implemented
- Will allow clients to join waitlist for fully booked slots

---

### 16.2 Review System

**Rules:**
- One review per booking
- Can review shop and/or barber
- Reviews can be flagged by shop owner
- Super admin can remove flagged reviews

**Uniqueness Constraint:**
```php
$table->unique(['booking_id', 'reviewable_type', 'reviewable_id']);
```

---

### 16.3 Opening Hours Format

**JSON Structure:**
```json
{
  "monday": {"open": "09:00", "close": "21:00"},
  "tuesday": {"open": "09:00", "close": "21:00"},
  "wednesday": {"open": "09:00", "close": "21:00"},
  "thursday": {"open": "09:00", "close": "21:00"},
  "friday": {"open": "14:00", "close": "23:00"},
  "saturday": {"open": "09:00", "close": "23:00"},
  "sunday": null
}
```

**Note:** `null` = shop is closed that day

---

## Conclusion

This constitution is **non-negotiable** and forms the foundation for all subsequent phases. Any deviation must be explicitly approved and documented.

---

**Constitution Version:** 1.0  
**Last Updated:** 2026-04-09

# Data Model: Authentication & Onboarding

## Enum Types (`app/Enums/`)

1. **UserRole** (`tinyInteger` backed by `int`)
   - `Client` (1)
   - `BarberOwner` (2)
   - `BarberStaff` (3)
   - `SuperAdmin` (4)

2. **ShopStatus** (`tinyInteger` backed by `int`)
   - `Pending` (1)
   - `Active` (2)
   - `Rejected` (3)
   - `Suspended` (4)

3. **OtpPurpose** (`tinyInteger` backed by `int`)
   - `PasswordReset` (1)
   - `BookingConfirm` (2)

## Entities (Models & Migrations)

### 1. `users`
Represents any person on the platform.
- `id` (BigAutoField, PK)
- `name` (String, max 255)
- `phone` (String, unique, exact 11 chars starting with '01')
- `password` (String, hashed)
- `role` (TinyInteger, mapped to `UserRole` enum)
- `status` (Boolean, default `true` for active)
- `no_show_strike_count` (Integer, default 0)
- `remember_token` (String, nullable)
- `created_at`, `updated_at` (Timestamps)
- `deleted_at` (SoftDeletes)

### 2. `areas`
Represents a geographic area/neighbourhood. Managed by the super admin.
- `id` (BigAutoField, PK)
- `name` (String, max 255)
- `slug` (String, unique)
- `is_active` (Boolean, default `true`)
- `created_at`, `updated_at` (Timestamps)
- `deleted_at` (SoftDeletes)

### 3. `shops`
Represents a barbershop.
- `id` (BigAutoField, PK)
- `owner_id` (ForeignKey to `users.id`, index, constrained)
- `area_id` (ForeignKey to `areas.id`, index, constrained)
- `name` (String, max 255)
- `address` (Text)
- `phone` (String, exact 11 chars)
- `logo_path` (String, nullable)
- `status` (TinyInteger, mapped to `ShopStatus` enum, default `Pending`)
- `rejection_reason` (String, nullable)
- `basic_services` (Json or Text, nullable)
- `barbers_count` (Integer)
- `created_at`, `updated_at` (Timestamps)
- `deleted_at` (SoftDeletes)

### 4. `shop_opening_hours`
Represents per-day opening hours for a shop.
- `id` (BigAutoField, PK)
- `shop_id` (ForeignKey to `shops.id`, index, constrained, cascade delete)
- `day_of_week` (TinyInteger, 0-6 where 0=Sunday, 6=Saturday)
- `is_closed` (Boolean, default `false`)
- `open_time` (Time, nullable)
- `close_time` (Time, nullable)
- `created_at`, `updated_at` (Timestamps)

### 5. `otp_codes`
Represents a one-time password for verification.
- `id` (BigAutoField, PK)
- `phone` (String, index)
- `code` (String, exact 6 digits)
- `purpose` (TinyInteger, mapped to `OtpPurpose` enum)
- `attempts` (Integer, default 0)
- `is_used` (Boolean, default `false`)
- `expires_at` (Timestamp)
- `created_at`, `updated_at` (Timestamps)

## Relationships

- **User -> Shops**: One-to-Many (`hasMany(Shop::class, 'owner_id')`). A shop owner can theoretically own multiple shops.
- **Area -> Shops**: One-to-Many (`hasMany(Shop::class)`).
- **Shop -> Owner**: BelongsTo (`belongsTo(User::class, 'owner_id')`).
- **Shop -> Area**: BelongsTo (`belongsTo(Area::class)`).
- **Shop -> OpeningHours**: One-to-Many (`hasMany(ShopOpeningHour::class)`).

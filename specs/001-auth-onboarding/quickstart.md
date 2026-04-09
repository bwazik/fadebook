# Quickstart: FadeBook Auth & Onboarding (Phase 1)

This quickstart assumes you are working within a Laravel 13 environment with Livewire 4, Alpine.js, Tailwind v4, and Pest 4 installed per the constitution.

## Key Files & Locations

- **Controllers / Livewire Components**:
  - `app/Livewire/Auth/Register.php` (Shared client & owner registration + role selection)
  - `app/Livewire/Auth/Login.php` (Shared login)
  - `app/Livewire/Auth/ForgotPassword.php` (Request OTP)
  - `app/Livewire/Auth/ResetPassword.php` (Verify OTP & set new password)
  - `app/Livewire/Shop/OnboardingWizard.php` (Multi-step shop details form)
- **Models**:
  - `app/Models/User.php`
  - `app/Models/Shop.php`
  - `app/Models/Area.php`
  - `app/Models/ShopOpeningHour.php`
  - `app/Models/OtpCode.php`
- **Enums** (`app/Enums/`):
  - `UserRole.php` (`Client = 1`, `BarberOwner = 2`, `BarberStaff = 3`, `SuperAdmin = 4`)
  - `ShopStatus.php` (`Pending = 1`, `Active = 2`, `Rejected = 3`, `Suspended = 4`)
  - `OtpPurpose.php` (`PasswordReset = 1`, `BookingConfirm = 2`)
- **Middleware**:
  - `app/Http/Middleware/RoleMiddleware.php` -> Checks `UserRole` and redirects to appropriate dashboard with a session toast on unauthorized access.
- **Views**:
  - `resources/views/livewire/auth/`
  - `resources/views/livewire/shop/`

## Testing (Pest)

Write tests matching the Acceptance Scenarios in the spec. Ensure factories exist and are used.

```bash
# Example commands
php artisan make:test Auth/RegistrationTest --pest
php artisan make:test Auth/LoginTest --pest
php artisan make:test Shop/OnboardingTest --pest
```

> Remember: No raw SQL, no MySQL enums, all enums must be Int-backed PHP classes. All dates must be timestamps. All soft deletes must be enabled. All migrations require foreign key `constrained()`.

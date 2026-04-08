<?php

use App\Contracts\WhatsAppNotificationChannel;
use App\Enums\OtpPurpose;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\ResetPassword;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

test('password reset request sends an otp over whatsapp', function () {
    User::factory()->create(['phone' => '01012345678']);

    $notifier = new class implements WhatsAppNotificationChannel
    {
        public array $otps = [];

        public function send(string $phone, string $message): bool
        {
            return true;
        }

        public function sendOtp(string $phone, string $code): bool
        {
            $this->otps[] = compact('phone', 'code');

            return true;
        }
    };

    app()->instance(WhatsAppNotificationChannel::class, $notifier);

    Livewire::test(ForgotPassword::class)
        ->set('phone', '01012345678')
        ->call('sendOtp')
        ->assertRedirect(route('password.reset'));

    expect(OtpCode::query()->count())->toBe(1);
    expect($notifier->otps)->toHaveCount(1);
});

test('user can reset password with the correct otp', function () {
    $user = User::factory()->create([
        'phone' => '01012345678',
        'password' => 'old-password',
    ]);

    session(['password_reset_phone' => '01012345678']);

    OtpCode::query()->create([
        'phone' => '01012345678',
        'code' => '123456',
        'purpose' => OtpPurpose::PasswordReset,
        'attempts' => 0,
        'is_used' => false,
        'expires_at' => now()->addMinutes(10),
    ]);

    Livewire::test(ResetPassword::class)
        ->set('phone', '01012345678')
        ->set('code', '123456')
        ->set('password', 'new-password')
        ->set('password_confirmation', 'new-password')
        ->call('resetPassword')
        ->assertRedirect(route('login'));

    expect(Hash::check('new-password', $user->fresh()->password))->toBeTrue();
});

test('otp is invalidated after five wrong attempts', function () {
    User::factory()->create(['phone' => '01012345678']);
    session(['password_reset_phone' => '01012345678']);

    OtpCode::query()->create([
        'phone' => '01012345678',
        'code' => '123456',
        'purpose' => OtpPurpose::PasswordReset,
        'attempts' => 0,
        'is_used' => false,
        'expires_at' => now()->addMinutes(10),
    ]);

    $component = Livewire::test(ResetPassword::class)
        ->set('phone', '01012345678')
        ->set('password', 'new-password')
        ->set('password_confirmation', 'new-password');

    foreach (range(1, 5) as $attempt) {
        $component->set('code', '000000')->call('resetPassword');
    }

    expect(OtpCode::query()->first()->fresh()->is_used)->toBeTrue();
});

test('expired otp requires a new request', function () {
    User::factory()->create(['phone' => '01012345678']);
    session(['password_reset_phone' => '01012345678']);

    OtpCode::factory()->expired()->create([
        'phone' => '01012345678',
        'code' => '123456',
    ]);

    Livewire::test(ResetPassword::class)
        ->set('phone', '01012345678')
        ->set('code', '123456')
        ->set('password', 'new-password')
        ->set('password_confirmation', 'new-password')
        ->call('resetPassword')
        ->assertSee('الكود انتهت صلاحيته. اطلب كود جديد.');
});

test('unknown phone still shows generic success without creating an otp', function () {
    $notifier = new class implements WhatsAppNotificationChannel
    {
        public int $sent = 0;

        public function send(string $phone, string $message): bool
        {
            return true;
        }

        public function sendOtp(string $phone, string $code): bool
        {
            $this->sent++;

            return true;
        }
    };

    app()->instance(WhatsAppNotificationChannel::class, $notifier);

    Livewire::test(ForgotPassword::class)
        ->set('phone', '01012345678')
        ->call('sendOtp')
        ->assertRedirect(route('password.reset'));

    expect(OtpCode::query()->count())->toBe(0);
    expect($notifier->sent)->toBe(0);
});

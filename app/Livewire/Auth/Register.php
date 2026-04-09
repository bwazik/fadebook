<?php

namespace App\Livewire\Auth;

use App\Enums\UserRole;
use App\Models\User;
use App\Support\EgyptianPhoneNumber;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Register extends Component
{
    public string $name = '';

    public string $phone = '';

    public string $password = '';

    public string $password_confirmation = '';

    public int $role = UserRole::Client->value;

    public function mount(): void
    {
        if (Auth::check()) {
            $this->redirectRoute(Auth::user()->homeRouteName(), navigate: true);
        }
    }

    public function register(): mixed
    {
        $validated = $this->validate();
        $phone = EgyptianPhoneNumber::normalize($validated['phone']);

        if ($phone === null) {
            $this->addError('phone', 'اكتب رقم موبايل مصري صح.');

            return null;
        }

        if (User::query()->where('phone', $phone)->exists()) {
            $this->addError('phone', 'رقم الموبايل ده مسجل قبل كده.');

            return null;
        }

        try {
            $user = User::query()->create([
                'name' => $validated['name'],
                'phone' => $phone,
                'password' => $validated['password'],
                'role' => UserRole::from($validated['role']),
                'status' => true,
                'no_show_strike_count' => 0,
            ]);
        } catch (QueryException $exception) {
            $this->addError('phone', 'رقم الموبايل ده مسجل قبل كده.');

            return null;
        }

        Auth::login($user);
        session()->regenerate();

        return $this->redirectRoute(
            $user->role === UserRole::BarberOwner ? 'onboarding' : 'marketplace',
            navigate: true,
        );
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', function (string $attribute, mixed $value, \Closure $fail): void {
                if (! EgyptianPhoneNumber::isValid((string) $value)) {
                    $fail('اكتب رقم موبايل مصري صح.');
                }
            }],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in([UserRole::Client->value, UserRole::BarberOwner->value])],
        ];
    }

    public function render()
    {
        return view('livewire.auth.register')
            ->layout('components.layouts.app', ['title' => 'سجّل حساب جديد']);
    }
}

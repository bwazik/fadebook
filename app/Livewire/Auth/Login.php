<?php

namespace App\Livewire\Auth;

use App\Support\EgyptianPhoneNumber;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class Login extends Component
{
    public string $phone = '';

    public string $password = '';

    public function mount(): void
    {
        if (Auth::check()) {
            $this->redirectRoute(Auth::user()->homeRouteName(), navigate: true);
        }
    }

    public function login(): mixed
    {
        $validated = $this->validate();
        $phone = EgyptianPhoneNumber::normalize($validated['phone']);

        if ($phone === null) {
            $this->addError('phone', 'اكتب رقم الموبايل بشكل صحيح.');

            return null;
        }

        $key = 'login:'.$phone;

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('phone', 'جرب تاني بعد دقيقة.');

            return null;
        }

        if (! Auth::attempt(['phone' => $phone, 'password' => $validated['password'], 'status' => true])) {
            RateLimiter::hit($key, 60);
            $this->addError('phone', 'بيانات الدخول غلط');

            return null;
        }

        RateLimiter::clear($key);
        session()->regenerate();

        return $this->redirectRoute(Auth::user()->homeRouteName(), navigate: true);
    }

    protected function rules(): array
    {
        return [
            'phone' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('components.layouts.app', ['title' => 'سجّل دخول']);
    }
}

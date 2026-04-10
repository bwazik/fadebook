<?php

namespace App\Livewire\Auth;

use App\Actions\Auth\LoginUser;
use App\Models\User;
use App\Rules\EgyptianPhone;
use App\Traits\WithRateLimiting;
use App\Traits\WithToast;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Login extends Component
{
    use WithRateLimiting, WithToast;

    public $phone;

    public $password;

    public function authenticate(LoginUser $loginUser)
    {
        if ($this->isRateLimited('login-attempt', 5, 60)) {
            return;
        }

        $validator = Validator::make([
            'phone' => $this->phone,
            'password' => $this->password,
        ], [
            'phone' => ['required', 'string', new EgyptianPhone],
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            $this->toastError($validator->errors()->first());

            return;
        }

        $loggedIn = $loginUser->execute($this->phone, $this->password);

        if ($loggedIn) {
            return redirect()->intended(route('home'));
        }

        // Check if user exists and is blocked to show specific message
        $user = User::where('phone', $this->phone)->first();
        if ($user && $user->is_blocked && Hash::check($this->password, $user->password)) {
            $this->toastError(__('messages.account_blocked'));

            return;
        }

        $this->toastError(__('messages.invalid_credentials'));
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}

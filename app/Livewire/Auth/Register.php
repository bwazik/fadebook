<?php

namespace App\Livewire\Auth;

use App\Actions\Auth\RegisterUser;
use App\Enums\ShopStatus;
use App\Enums\UserRole;
use App\Models\Area;
use App\Models\Shop;
use App\Rules\EgyptianPhone;
use App\Services\ReferralService;
use App\Traits\WithRateLimiting;
use App\Traits\WithToast;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Register extends Component
{
    use WithRateLimiting, WithToast;

    public $step = 1;

    public $ref;

    public $name;

    public $phone;

    public $password;

    public $password_confirmation;

    public $role = 'client';

    // Step 3 & 4 (Barber Owner Shop Info)
    public $shopName;

    public $areaId = 0;

    public $address;

    public $shopPhone;

    public $description;

    public $openingHours = [
        'saturday' => ['is_open' => true, 'open' => '09:00', 'close' => '21:00'],
        'sunday' => ['is_open' => true, 'open' => '09:00', 'close' => '21:00'],
        'monday' => ['is_open' => true, 'open' => '09:00', 'close' => '21:00'],
        'tuesday' => ['is_open' => true, 'open' => '09:00', 'close' => '21:00'],
        'wednesday' => ['is_open' => true, 'open' => '09:00', 'close' => '21:00'],
        'thursday' => ['is_open' => true, 'open' => '09:00', 'close' => '21:00'],
        'friday' => ['is_open' => true, 'open' => '14:00', 'close' => '23:00'],
    ];

    public function mount()
    {
        $this->ref = request()->query('ref');
    }

    public function nextStep()
    {
        if ($this->isRateLimited('register-next-step', 10, 60)) {
            return;
        }

        $validator = Validator::make([
            'name' => $this->name,
            'phone' => $this->phone,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
        ], [
            'name' => 'required|string|max:255',
            'phone' => ['required', 'string', 'unique:users,phone', new EgyptianPhone],
            'password' => 'required|string|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            $this->toastError($validator->errors()->first());

            return;
        }

        $this->step = 2;
    }

    public function previousStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function register(RegisterUser $registerUser)
    {
        if ($this->isRateLimited('register-submit', 5, 60)) {
            return;
        }

        // Final validation
        $data = [
            'name' => $this->name,
            'phone' => $this->phone,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
            'role' => $this->role,
        ];

        $rules = [
            'name' => 'required|string|max:255',
            'phone' => ['required', 'string', 'unique:users,phone', new EgyptianPhone],
            'password' => 'required|string|confirmed|min:8',
            'role' => 'required|string|in:client,barber_owner',
        ];

        if ($this->role === 'barber_owner') {
            $data = array_merge($data, [
                'shopName' => $this->shopName,
                'areaId' => $this->areaId,
                'address' => $this->address,
                'shopPhone' => $this->shopPhone,
            ]);
            $rules = array_merge($rules, [
                'shopName' => 'required|string|max:255',
                'areaId' => 'required|integer|exists:areas,id',
                'address' => 'required|string|max:500',
                'shopPhone' => ['required', 'string', new EgyptianPhone],
            ]);
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            $this->toastError($validator->errors()->first());

            return;
        }

        try {
            DB::beginTransaction();

            $user = $registerUser->execute(
                $this->name,
                $this->phone,
                $this->password,
                UserRole::Client
            );

            if ($this->ref) {
                app(ReferralService::class)->handleRegistration($user, $this->ref);
            }

            if ($this->role === 'barber_owner') {
                Shop::create([
                    'owner_id' => $user->id,
                    'name' => $this->shopName,
                    'phone' => $this->shopPhone,
                    'area_id' => $this->areaId,
                    'address' => $this->address,
                    'description' => $this->description,
                    'status' => ShopStatus::Pending,
                    'is_online' => true,
                    'advance_booking_days' => 30,
                    'opening_hours' => $this->openingHours,
                ]);

                // TODO: Notify super admin via WhatsApp about new shop application

                DB::commit();

                session(['verification_redirect' => route('onboarding.pending')]);

                return redirect()->route('onboarding.pending');
            }

            DB::commit();

            return redirect()->intended(route('phone.verification.show'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration failed: '.$e->getMessage());
            $this->toastError(__('messages.registration_failed'));
        }
    }

    public function render()
    {
        return view('livewire.auth.register', [
            'areasOptions' => Area::pluck('name', 'id')->toArray(),
        ]);
    }
}

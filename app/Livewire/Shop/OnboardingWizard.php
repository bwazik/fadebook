<?php

namespace App\Livewire\Shop;

use App\Enums\ShopStatus;
use App\Enums\UserRole;
use App\Models\Area;
use App\Models\Shop;
use App\Support\EgyptianPhoneNumber;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class OnboardingWizard extends Component
{
    use WithFileUploads;

    public int $step = 1;

    public string $shop_name = '';

    public string $address = '';

    public string $phone = '';

    public string $basic_services = '';

    public string|int $area_id = '';

    public int|string $barbers_count = '';

    public mixed $logo = null;

    public ?Shop $shop = null;

    public array $opening_hours = [];

    public function mount(): void
    {
        $user = Auth::user();

        if ($user === null) {
            $this->redirectRoute('login', navigate: true);

            return;
        }

        if ($user->role !== UserRole::BarberOwner) {
            $this->redirectRoute($user->homeRouteName(), navigate: true);

            return;
        }

        $this->opening_hours = $this->defaultOpeningHours();
        $this->shop = $user->shops()->with('openingHours')->latest('id')->first();

        if ($this->shop?->status === ShopStatus::Pending) {
            $this->redirectRoute('owner.pending', navigate: true);

            return;
        }

        if ($this->shop?->status === ShopStatus::Active) {
            $this->redirectRoute('owner.dashboard', navigate: true);

            return;
        }

        if ($this->shop !== null) {
            $this->fillFromShop($this->shop);
        }
    }

    public function nextStep(): void
    {
        $this->validateStep($this->step);

        if ($this->getErrorBag()->isNotEmpty()) {
            return;
        }

        $this->step = min($this->step + 1, 3);
    }

    public function previousStep(): void
    {
        $this->step = max($this->step - 1, 1);
    }

    public function save(): mixed
    {
        $this->validateStep(1);
        $this->validateStep(2);
        $this->validateStep(3);

        if ($this->getErrorBag()->isNotEmpty()) {
            return null;
        }

        $phone = EgyptianPhoneNumber::normalize($this->phone);

        if ($phone === null) {
            $this->addError('phone', 'اكتب رقم موبايل مصري صح.');

            return null;
        }

        $shop = DB::transaction(function () use ($phone): Shop {
            $user = Auth::user();
            $logoPath = $this->resolveLogoPath();

            $attributes = [
                'owner_id' => $user->id,
                'area_id' => (int) $this->area_id,
                'name' => $this->shop_name,
                'address' => $this->address,
                'phone' => $phone,
                'logo_path' => $logoPath,
                'status' => ShopStatus::Pending,
                'rejection_reason' => null,
                'basic_services' => $this->serviceList(),
                'barbers_count' => (int) $this->barbers_count,
            ];

            if ($this->shop !== null) {
                $this->shop->update($attributes);
                $shop = $this->shop->fresh();
            } else {
                $shop = Shop::query()->create($attributes);
            }

            $shop->openingHours()->delete();

            foreach ($this->opening_hours as $day => $hours) {
                $shop->openingHours()->create([
                    'day_of_week' => $day,
                    'is_closed' => (bool) $hours['is_closed'],
                    'open_time' => $hours['is_closed'] ? null : $hours['open_time'],
                    'close_time' => $hours['is_closed'] ? null : $hours['close_time'],
                ]);
            }

            return $shop;
        });

        $this->shop = $shop;

        return $this->redirectRoute('owner.pending', navigate: true);
    }

    protected function validateStep(int $step): void
    {
        if ($step === 1) {
            $this->validate([
                'shop_name' => ['required', 'string', 'max:255'],
                'address' => ['required', 'string'],
                'area_id' => ['required', Rule::exists('areas', 'id')->where(fn ($query) => $query->where('is_active', true))],
            ]);

            return;
        }

        if ($step === 2) {
            $this->validate([
                'phone' => ['required', 'string', function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! EgyptianPhoneNumber::isValid((string) $value)) {
                        $fail('اكتب رقم موبايل مصري صح.');
                    }
                }],
                'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            ]);

            return;
        }

        $this->validate([
            'basic_services' => ['required', 'string'],
            'barbers_count' => ['required', 'integer', 'min:1'],
            'opening_hours' => ['required', 'array', 'size:7'],
            'opening_hours.*.is_closed' => ['required', 'boolean'],
            'opening_hours.*.open_time' => ['nullable', 'date_format:H:i'],
            'opening_hours.*.close_time' => ['nullable', 'date_format:H:i'],
        ]);

        foreach ($this->opening_hours as $index => $hours) {
            if (! $hours['is_closed'] && ($hours['open_time'] === '' || $hours['close_time'] === '')) {
                $this->addError("opening_hours.$index.open_time", 'حدد مواعيد الشغل لكل يوم مفتوح.');
            }
        }
    }

    protected function resolveLogoPath(): ?string
    {
        if ($this->logo instanceof TemporaryUploadedFile) {
            return $this->logo->store('shops/logos', 'public');
        }

        return $this->shop?->logo_path;
    }

    protected function serviceList(): array
    {
        return collect(explode(',', $this->basic_services))
            ->map(fn (string $service): string => trim($service))
            ->filter()
            ->values()
            ->all();
    }

    protected function defaultOpeningHours(): array
    {
        return [
            ['label' => 'السبت', 'is_closed' => false, 'open_time' => '10:00', 'close_time' => '22:00'],
            ['label' => 'الأحد', 'is_closed' => false, 'open_time' => '10:00', 'close_time' => '22:00'],
            ['label' => 'الاثنين', 'is_closed' => false, 'open_time' => '10:00', 'close_time' => '22:00'],
            ['label' => 'الثلاثاء', 'is_closed' => false, 'open_time' => '10:00', 'close_time' => '22:00'],
            ['label' => 'الأربعاء', 'is_closed' => false, 'open_time' => '10:00', 'close_time' => '22:00'],
            ['label' => 'الخميس', 'is_closed' => false, 'open_time' => '10:00', 'close_time' => '22:00'],
            ['label' => 'الجمعة', 'is_closed' => true, 'open_time' => '', 'close_time' => ''],
        ];
    }

    protected function fillFromShop(Shop $shop): void
    {
        $this->shop_name = $shop->name;
        $this->address = $shop->address;
        $this->phone = $shop->phone;
        $this->area_id = $shop->area_id;
        $this->basic_services = implode(', ', $shop->basic_services ?? []);
        $this->barbers_count = $shop->barbers_count;

        foreach ($shop->openingHours->sortBy('day_of_week') as $openingHour) {
            $this->opening_hours[$openingHour->day_of_week] = [
                'label' => $this->opening_hours[$openingHour->day_of_week]['label'],
                'is_closed' => (bool) $openingHour->is_closed,
                'open_time' => $openingHour->open_time ? substr($openingHour->open_time, 0, 5) : '',
                'close_time' => $openingHour->close_time ? substr($openingHour->close_time, 0, 5) : '',
            ];
        }
    }

    public function render()
    {
        return view('livewire.shop.onboarding-wizard', [
            'areas' => Area::query()->where('is_active', true)->orderBy('name')->get(),
        ])->layout('components.layouts.app', ['title' => 'بيانات الصالون']);
    }
}

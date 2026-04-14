<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\BookingStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\Shop;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class GlobalSearch extends Component
{
    public string $query = '';

    public bool $show = false;

    public string $context = 'marketplace';

    public function mount(): void
    {
        $this->detectContext();
    }

    #[On('open-global-search')]
    public function open(): void
    {
        $this->detectContext();
        $this->show = true;
        // Reset query when opening
        $this->query = '';
    }

    public function detectContext(): void
    {
        $url = url()->previous();
        $path = parse_url($url, PHP_URL_PATH) ?? '/';

        if (str_starts_with($path, '/dashboard')) {
            $this->context = 'dashboard';
        } elseif (str_starts_with($path, '/bookings')) {
            $this->context = 'client';
        } else {
            $this->context = 'marketplace';
        }
    }

    #[Computed]
    public function results(): Collection
    {
        if (empty(trim($this->query))) {
            return collect();
        }

        return match ($this->context) {
            'dashboard' => $this->searchDashboard(),
            'client' => $this->searchClientBookings(),
            default => $this->searchShops(),
        };
    }

    #[Computed]
    public function suggestions(): Collection
    {
        $user = Auth::user();
        if (! $user) {
            return collect();
        }

        if ($this->context === 'dashboard' && $user->role === UserRole::BarberOwner) {
            // Suggest today's upcoming bookings for the shop owner
            return Booking::where('shop_id', $user->shop?->id)
                ->whereIn('status', [BookingStatus::Confirmed, BookingStatus::InProgress])
                ->whereDate('scheduled_at', now())
                ->with(['client', 'service'])
                ->orderBy('scheduled_at', 'asc')
                ->limit(3)
                ->get();
        }

        if ($this->context === 'client') {
            // Suggest the client's very next appointment
            return Booking::where('client_id', $user->id)
                ->whereIn('status', [BookingStatus::Confirmed, BookingStatus::Pending])
                ->where('scheduled_at', '>=', now())
                ->with(['shop', 'service'])
                ->orderBy('scheduled_at', 'asc')
                ->limit(3)
                ->get();
        }

        return collect();
    }

    private function searchShops(): Collection
    {
        return Shop::active()
            ->where('name', 'like', "%{$this->query}%")
            ->with(['images' => fn ($q) => $q->where('collection', 'logo')])
            ->limit(8)
            ->get()
            ->map(fn ($shop) => [
                'id' => $shop->id,
                'title' => $shop->name,
                'subtitle' => $shop->area?->name ?? 'محل حلاقة',
                'image' => $shop->getImage('logo')->first()?->path,
                'url' => route('shop.show', ['areaSlug' => $shop->area?->slug ?? 'eg', 'shopSlug' => $shop->slug]),
                'type' => 'shop',
            ]);
    }

    private function searchClientBookings(): Collection
    {
        return Booking::where('client_id', Auth::id())
            ->where(function ($q) {
                $q->where('booking_code', 'like', "%{$this->query}%")
                    ->orWhereHas('shop', fn ($sq) => $sq->where('name', 'like', "%{$this->query}%"));
            })
            ->with(['shop', 'service'])
            ->latest('scheduled_at')
            ->limit(8)
            ->get()
            ->map(fn ($booking) => [
                'id' => $booking->id,
                'title' => $booking->shop?->name ?? 'محل غير معروف',
                'subtitle' => ($booking->service?->name ?? 'خدمة حلاقة')." • {$booking->booking_code}",
                'image' => $booking->shop?->getImage('logo')->first()?->path,
                'url' => route('booking.show', $booking->uuid),
                'type' => 'booking',
            ]);
    }

    private function searchDashboard(): Collection
    {
        $shopId = Auth::user()->shop?->id;

        return Booking::where('shop_id', $shopId)
            ->where(function ($q) {
                $q->where('booking_code', 'like', "%{$this->query}%")
                    ->orWhereHas('client', fn ($cq) => $cq->where('name', 'like', "%{$this->query}%"))
                    ->orWhereHas('barber', fn ($bq) => $bq->where('name', 'like', "%{$this->query}%"));
            })
            ->with(['client', 'barber', 'service'])
            ->orderBy('scheduled_at', 'desc')
            ->limit(8)
            ->get()
            ->map(fn ($booking) => [
                'id' => $booking->id,
                'title' => $booking->client?->name ?? 'عميل غير معروف',
                'subtitle' => "#{$booking->booking_code} • ".($booking->barber?->name ?? 'أي حلاق'),
                'image' => $booking->barber?->getImage('avatar')->first()?->path,
                'url' => route('dashboard.reservations', ['search' => $booking->booking_code]), // Pass code to reservations search
                'type' => 'reservation',
            ]);
    }

    public function render()
    {
        return view('livewire.global-search');
    }
}

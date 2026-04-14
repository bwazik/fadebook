<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard;

use App\Enums\BookingStatus;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Financials extends Component
{
    public int $month;

    public int $year;

    public int $perPage = 10;

    public function loadMore(): void
    {
        $this->perPage += 10;
    }

    protected $queryString = [
        'month' => ['except' => ''],
        'year' => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->month = (int) (request('month') ?? now()->month);
        $this->year = (int) (request('year') ?? now()->year);
        $this->dispatch('show-bottom-nav');
    }

    public function updated($property): void
    {
        if (in_array($property, ['month', 'year'])) {
            $this->perPage = 10;
        }
    }

    #[Computed]
    public function dateRange(): array
    {
        $start = now()->setYear($this->year)->setMonth($this->month)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        return [$start, $end];
    }

    #[Computed]
    public function stats(): array
    {
        $shop = Auth::user()->shop;
        [$start, $end] = $this->dateRange;

        $bookings = $shop->bookings()
            ->whereBetween('completed_at', [$start, $end])
            ->where('status', BookingStatus::Completed)
            ->get();

        $gross = (float) $bookings->sum('final_amount');
        $commissionRate = (float) ($shop->commission_rate ?? 10.00); // Default placeholder commission
        $commission = $gross * ($commissionRate / 100);
        $net = $gross - $commission;

        return [
            'total_bookings' => $bookings->count(),
            'gross_earnings' => $gross,
            'commission_deducted' => $commission,
            'net_payout' => $net,
            'commission_rate' => $commissionRate,
        ];
    }

    #[Computed]
    public function transactions()
    {
        $shop = Auth::user()->shop;
        [$start, $end] = $this->dateRange;

        return $shop->bookings()
            ->with(['client', 'service'])
            ->whereBetween('completed_at', [$start, $end])
            ->where('status', BookingStatus::Completed)
            ->orderBy('completed_at', 'desc')
            ->limit($this->perPage)
            ->get();
    }

    #[Computed]
    public function hasMore(): bool
    {
        $shop = Auth::user()->shop;
        [$start, $end] = $this->dateRange;

        return $shop->bookings()
            ->whereBetween('completed_at', [$start, $end])
            ->where('status', BookingStatus::Completed)
            ->count() > $this->perPage;
    }

    #[Computed]
    public function monthOptions(): array
    {
        return [
            1 => __('messages.month_1'),
            2 => __('messages.month_2'),
            3 => __('messages.month_3'),
            4 => __('messages.month_4'),
            5 => __('messages.month_5'),
            6 => __('messages.month_6'),
            7 => __('messages.month_7'),
            8 => __('messages.month_8'),
            9 => __('messages.month_9'),
            10 => __('messages.month_10'),
            11 => __('messages.month_11'),
            12 => __('messages.month_12'),
        ];
    }

    #[Computed]
    public function yearOptions(): array
    {
        $years = [];
        for ($y = now()->year; $y >= 2024; $y--) {
            $years[$y] = (string) $y;
        }

        return $years;
    }

    public function render(): View
    {
        return view('livewire.dashboard.financials');
    }
}

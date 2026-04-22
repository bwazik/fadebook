<?php

namespace App\Filament\Widgets;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Shop;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        $completedBookings = Booking::completed();

        $totalEarnings = (float) $completedBookings->sum('commission_amount');
        $marketplaceVolume = (float) $completedBookings->sum('final_amount');

        // Completion Rate
        $totalEnded = Booking::whereIn('status', [
            BookingStatus::Completed,
            BookingStatus::Cancelled,
            BookingStatus::NoShow,
        ])->count();

        $completionRate = $totalEnded > 0
            ? round(($completedBookings->count() / $totalEnded) * 100, 1)
            : 0;

        return [
            Stat::make('أرباح المنصة (العمولات)', number_format($totalEarnings, 2).' ج.م')
                ->description('إجمالي عمولة المنصة من الحجوزات المكتملة')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart($this->getCommissionChartData()),

            Stat::make('حجم التداول (GMV)', number_format($marketplaceVolume, 2).' ج.م')
                ->description('إجمالي المبالغ المدفوعة في المنصة')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('primary')
                ->chart($this->getVolumeChartData()),

            Stat::make('معدل إتمام الحجز', $completionRate.'%')
                ->description('نسبة الحجوزات المكتملة من إجمالي الحجوزات المنتهية')
                ->descriptionIcon($completionRate > 80 ? 'heroicon-m-check-badge' : 'heroicon-m-exclamation-triangle')
                ->color($completionRate > 80 ? 'success' : 'warning'),

            Stat::make('المتاجر النشطة', Shop::active()->count())
                ->description('إجمالي صالونات الحلاقة المفعلة')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('info'),
        ];
    }

    protected function getCommissionChartData(): array
    {
        $data = Booking::completed()
            ->where('completed_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(completed_at) as date, SUM(commission_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total')
            ->toArray();

        return count($data) > 0 ? $data : [0, 0, 0, 0, 0, 0, 0];
    }

    protected function getVolumeChartData(): array
    {
        $data = Booking::completed()
            ->where('completed_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(completed_at) as date, SUM(final_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total')
            ->toArray();

        return count($data) > 0 ? $data : [0, 0, 0, 0, 0, 0, 0];
    }
}

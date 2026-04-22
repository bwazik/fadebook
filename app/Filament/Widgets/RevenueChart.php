<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;

class RevenueChart extends ChartWidget
{
    protected ?string $heading = 'إحصائيات الإيرادات والعمولات';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $activeMonths = [];
        $volume = [];
        $commissions = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $activeMonths[] = $month->translatedFormat('M Y');

            $volume[] = Booking::completed()
                ->whereMonth('completed_at', $month->month)
                ->whereYear('completed_at', $month->year)
                ->sum('final_amount');

            $commissions[] = Booking::completed()
                ->whereMonth('completed_at', $month->month)
                ->whereYear('completed_at', $month->year)
                ->sum('commission_amount');
        }

        return [
            'datasets' => [
                [
                    'label' => 'حجم المبيعات (GMV)',
                    'data' => $volume,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => 'start',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'عمولة المنصة',
                    'data' => $commissions,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => 'start',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $activeMonths,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

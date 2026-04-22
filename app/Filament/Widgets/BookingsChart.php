<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;

class BookingsChart extends ChartWidget
{
    protected ?string $heading = 'إحصائيات الحجوزات (١٢ شهر)';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $activeMonths = [];
        $counts = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $activeMonths[] = $month->translatedFormat('M Y');

            $counts[] = Booking::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'إجمالي الحجوزات',
                    'data' => $counts,
                    'borderColor' => '#ff9f0a',
                    'backgroundColor' => 'rgba(255, 159, 10, 0.1)',
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

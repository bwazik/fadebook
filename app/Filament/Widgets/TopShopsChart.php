<?php

namespace App\Filament\Widgets;

use App\Models\Shop;
use Filament\Widgets\ChartWidget;

class TopShopsChart extends ChartWidget
{
    protected ?string $heading = 'أعلى ٥ صالونات حلاقة (حسب عدد الحجوزات)';

    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $shops = Shop::withCount('bookings')
            ->orderBy('bookings_count', 'desc')
            ->take(5)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'عدد الحجوزات',
                    'data' => $shops->pluck('bookings_count')->toArray(),
                    'backgroundColor' => [
                        '#ff9f0a',
                        '#3b82f6',
                        '#10b981',
                        '#ef4444',
                        '#8b5cf6',
                    ],
                ],
            ],
            'labels' => $shops->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}

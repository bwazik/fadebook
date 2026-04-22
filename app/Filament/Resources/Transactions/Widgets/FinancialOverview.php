<?php

namespace App\Filament\Resources\Transactions\Widgets;

use App\Filament\Resources\Transactions\Pages\ManageTransactions;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinancialOverview extends StatsOverviewWidget
{
    use InteractsWithPageTable;

    protected function getTablePage(): string
    {
        return ManageTransactions::class;
    }

    protected function getStats(): array
    {
        $query = $this->getPageTableQuery();

        return [
            Stat::make('إجمالي الإيرادات', number_format($query->sum('final_amount'), 2).' '.__('messages.egp'))
                ->description('صافي المبلغ المدفوع من العملاء')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('عمولة المنصة', number_format($query->sum('commission_amount'), 2).' '.__('messages.egp'))
                ->description('إجمالي مستحقات التطبيق')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info'),

            Stat::make('إجمالي الخصومات', number_format($query->sum('discount_amount'), 2).' '.__('messages.egp'))
                ->description('المبالغ المخصومة عبر الكوبونات')
                ->descriptionIcon('heroicon-m-receipt-percent')
                ->color('danger'),

            Stat::make('عدد الحجوزات', $query->count())
                ->description('إجمالي الحجوزات المكتملة')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('gray'),
        ];
    }
}

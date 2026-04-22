<?php

declare(strict_types=1);

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionResource;
use App\Filament\Resources\Transactions\Widgets\FinancialOverview;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ManageRecords;

class ManageTransactions extends ManageRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = TransactionResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            FinancialOverview::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}

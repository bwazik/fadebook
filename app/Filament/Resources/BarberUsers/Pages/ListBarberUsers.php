<?php

namespace App\Filament\Resources\BarberUsers\Pages;

use App\Filament\Resources\BarberUsers\BarberUserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBarberUsers extends ListRecords
{
    protected static string $resource = BarberUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

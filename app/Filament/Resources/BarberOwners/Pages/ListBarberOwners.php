<?php

namespace App\Filament\Resources\BarberOwners\Pages;

use App\Filament\Resources\BarberOwners\BarberOwnerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBarberOwners extends ListRecords
{
    protected static string $resource = BarberOwnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\BarberOwners\Pages;

use App\Filament\Resources\BarberOwners\BarberOwnerResource;
use App\Traits\HandlesImageCollections;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateBarberOwner extends CreateRecord
{
    use HandlesImageCollections;

    protected static string $resource = BarberOwnerResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->extractImageFields($data, ['avatar']);
    }

    protected function handleRecordCreation(array $data): Model
    {
        $record = static::getModel()::create($data);
        $this->saveImageCollections($record, ['avatar'], true);

        return $record;
    }
}

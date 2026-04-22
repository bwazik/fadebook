<?php

namespace App\Filament\Resources\Barbers\Pages;

use App\Filament\Resources\Barbers\BarberResource;
use App\Traits\HandlesImageCollections;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateBarber extends CreateRecord
{
    use HandlesImageCollections;

    protected static string $resource = BarberResource::class;

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

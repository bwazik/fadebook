<?php

namespace App\Filament\Resources\BarberUsers\Pages;

use App\Filament\Resources\BarberUsers\BarberUserResource;
use App\Traits\HandlesImageCollections;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateBarberUser extends CreateRecord
{
    use HandlesImageCollections;

    protected static string $resource = BarberUserResource::class;

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

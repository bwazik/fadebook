<?php

namespace App\Filament\Resources\SuperAdmins\Pages;

use App\Filament\Resources\SuperAdmins\SuperAdminResource;
use App\Traits\HandlesImageCollections;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateSuperAdmin extends CreateRecord
{
    use HandlesImageCollections;

    protected static string $resource = SuperAdminResource::class;

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

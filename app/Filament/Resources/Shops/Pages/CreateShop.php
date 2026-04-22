<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shops\Pages;

use App\Filament\Resources\Shops\ShopResource;
use App\Traits\HandlesImageCollections;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateShop extends CreateRecord
{
    use HandlesImageCollections;

    protected static string $resource = ShopResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Extract image fields from form data
        return $this->extractImageFields($data, ['logo', 'banner', 'gallery']);
    }

    protected function handleRecordCreation(array $data): Model
    {
        // First, create the store record
        $record = static::getModel()::create($data);

        // Handle logo (single image)
        $this->saveImageCollections($record, ['logo'], true);

        // Handle banner (single image)
        $this->saveImageCollections($record, ['banner'], true);

        // Handle gallery (multiple images)
        $this->saveImageCollections($record, ['gallery'], false);

        return $record;
    }
}

<?php

namespace App\Filament\Resources\Shops\Pages;

use App\Filament\Resources\Shops\ShopResource;
use App\Traits\HandlesImageCollections;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditShop extends EditRecord
{
    use HandlesImageCollections;

    protected static string $resource = ShopResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load existing images from database
        $logoData = $this->loadExistingImages($this->record, ['logo'], true);
        $bannerData = $this->loadExistingImages($this->record, ['banner'], true);
        $multiImageData = $this->loadExistingImages($this->record, ['gallery'], false);

        return array_merge($data, $logoData, $bannerData, $multiImageData);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Extract image fields from form data
        return $this->extractImageFields($data, ['logo', 'banner', 'gallery']);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // First, update the record normally
        $record->update($data);

        // Handle logo (single image) - only update if changed
        if ($this->hasImageCollectionChanged('logo')) {
            $this->saveImageCollections($record, ['logo'], true);
        }

        // Handle banner (single image) - only update if changed
        if ($this->hasImageCollectionChanged('banner')) {
            $this->saveImageCollections($record, ['banner'], true);
        }

        // Handle gallery (multiple images)
        $this->saveImageCollections($record, ['gallery'], false);

        return $record;
    }
}

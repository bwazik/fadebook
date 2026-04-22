<?php

namespace App\Filament\Resources\BarberOwners\Pages;

use App\Filament\Resources\BarberOwners\BarberOwnerResource;
use App\Traits\HandlesImageCollections;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditBarberOwner extends EditRecord
{
    use HandlesImageCollections;

    protected static string $resource = BarberOwnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $avatarData = $this->loadExistingImages($this->record, ['avatar'], true);

        return array_merge($data, $avatarData);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->extractImageFields($data, ['avatar']);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        if ($this->hasImageCollectionChanged('avatar')) {
            $this->saveImageCollections($record, ['avatar'], true);
        }

        return $record;
    }
}

<?php

namespace App\Filament\Resources\Barbers\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UnavailabilitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'unavailabilities';

    protected static ?string $title = 'مواعيد عدم التوفر';

    protected static ?string $modelLabel = 'موعد';

    protected static ?string $pluralModelLabel = 'مواعيد';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('unavailable_date')
                    ->label('التاريخ')
                    ->required()
                    ->native(false)
                    ->displayFormat('Y-m-d'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('unavailable_date')
            ->columns([
                TextColumn::make('unavailable_date')
                    ->label('التاريخ')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()->label('إضافة موعد')->modal(),
            ])
            ->recordActions([
                EditAction::make()->label('تعديل')->modal(),
                DeleteAction::make()->label('حذف'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('حذف'),
                ]),
            ]);
    }
}

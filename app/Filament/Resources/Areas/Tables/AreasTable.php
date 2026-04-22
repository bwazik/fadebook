<?php

declare(strict_types=1);

namespace App\Filament\Resources\Areas\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class AreasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('المنطقة')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label('الرابط')
                    ->badge(),

                IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make()->label('تعديل'),
                DeleteAction::make()->label('حذف'),
            ]);
    }
}

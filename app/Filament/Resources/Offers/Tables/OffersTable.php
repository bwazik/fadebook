<?php

namespace App\Filament\Resources\Offers\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OffersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('shop.name')
                    ->label('المحل')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('النوع')
                    ->badge(),
                IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
                TextColumn::make('start_date')
                    ->label('من')
                    ->since()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('إلى')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Coupons\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CouponsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('الكود')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('shop.name')
                    ->label('المحل')
                    ->placeholder('عام')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('discount_type')
                    ->label('النوع')
                    ->badge(),
                TextColumn::make('discount_value')
                    ->label('القيمة')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('used_count')
                    ->label('مرات الاستخدام')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}

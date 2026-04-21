<?php

declare(strict_types=1);

namespace App\Filament\Resources\Transactions\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('booking_code')
                    ->label('كود الحجز')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('shop.name')
                    ->label('المحل')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('total_price')
                    ->label('المبلغ الإجمالي')
                    ->money('EGP')
                    ->sortable(),

                TextColumn::make('shop.commission_percentage')
                    ->label('نسبة العموله')
                    ->suffix('%')
                    ->sortable(),

                TextColumn::make('commission_value')
                    ->label('قيمة العموله')
                    ->money('EGP')
                    ->state(function ($record) {
                        return ($record->total_price * ($record->shop->commission_percentage ?? 0)) / 100;
                    }),

                TextColumn::make('updated_at')
                    ->label('تاريخ الإتمام')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }
}

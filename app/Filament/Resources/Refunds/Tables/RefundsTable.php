<?php

declare(strict_types=1);

namespace App\Filament\Resources\Refunds\Tables;

use App\Enums\RefundReason;
use App\Enums\RefundStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RefundsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('booking.booking_code')
                    ->label('كود الحجز')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('المبلغ')
                    ->money('EGP')
                    ->sortable(),

                TextColumn::make('reason')
                    ->label('السبب')
                    ->formatStateUsing(fn ($state) => $state instanceof RefundReason ? $state->getLabel() : $state),

                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn ($state) => $state instanceof RefundStatus ? $state->getColor() : 'gray')
                    ->formatStateUsing(fn ($state) => $state instanceof RefundStatus ? $state->getLabel() : $state),

                TextColumn::make('processed_at')
                    ->label('تاريخ المعالجة')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('تاريخ الطلب')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(RefundStatus::class),
            ])
            ->recordActions([
                ViewAction::make()->label('عرض'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}

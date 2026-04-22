<?php

declare(strict_types=1);

namespace App\Filament\Resources\Referrals\Tables;

use App\Enums\ReferralStatus;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ReferralsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('referrer.name')
                    ->label('المحيل')
                    ->searchable(),

                TextColumn::make('referrer.phone')
                    ->label('رقم المحيل')
                    ->searchable(),

                TextColumn::make('invitee.name')
                    ->label('المدعو')
                    ->searchable(),

                TextColumn::make('invitee.phone')
                    ->label('رقم المدعو')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (ReferralStatus $state): string => $state->getLabel())
                    ->color(fn (ReferralStatus $state): string => $state->getColor()),

                TextColumn::make('coupon.code')
                    ->label('كود الكوبون')
                    ->placeholder('—')
                    ->copyable(),

                TextColumn::make('rewarded_at')
                    ->label('تاريخ المكافأة')
                    ->since()
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('تاريخ الإحالة')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(ReferralStatus::class)
                    ->attribute('status'),
            ]);
    }
}

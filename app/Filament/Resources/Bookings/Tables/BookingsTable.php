<?php

declare(strict_types=1);

namespace App\Filament\Resources\Bookings\Tables;

use App\Enums\BookingStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('booking_code')
                    ->label('كود الحجز')
                    ->searchable()
                    ->copyable()
                    ->weight('bold'),

                TextColumn::make('client.name')
                    ->label('العميل')
                    ->searchable(),

                TextColumn::make('shop.name')
                    ->label('المحل')
                    ->searchable(),

                TextColumn::make('service.name')
                    ->label('الخدمة'),

                TextColumn::make('scheduled_at')
                    ->label('موعد الحجز')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (BookingStatus $state): string => $state->getLabel())
                    ->color(fn (BookingStatus $state): string => $state->getColor()),

                TextColumn::make('final_amount')
                    ->label('المبلغ المدفوع')
                    ->money('egp')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(BookingStatus::class)
                    ->attribute('status'),

                SelectFilter::make('shop')
                    ->label('المحل')
                    ->relationship('shop', 'name'),

                Filter::make('scheduled_at')
                    ->label('تاريخ الحجز')
                    ->form([
                        DatePicker::make('from')->label('من'),
                        DatePicker::make('until')->label('إلى'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn (Builder $q) => $q->whereDate('scheduled_at', '>=', $data['from']))
                            ->when($data['until'], fn (Builder $q) => $q->whereDate('scheduled_at', '<=', $data['until']));
                    }),
            ]);
    }
}

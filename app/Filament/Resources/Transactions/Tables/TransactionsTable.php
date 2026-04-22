<?php

declare(strict_types=1);

namespace App\Filament\Resources\Transactions\Tables;

use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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

                TextColumn::make('client.name')
                    ->label('العميل')
                    ->searchable(),

                TextColumn::make('service_price')
                    ->label('سعر الخدمة')
                    ->money('EGP')
                    ->sortable(),

                TextColumn::make('discount_amount')
                    ->label('الخصم')
                    ->money('EGP')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('final_amount')
                    ->label('المبلغ الإجمالي')
                    ->money('EGP')
                    ->sortable(),

                TextColumn::make('commission_amount')
                    ->label('عمولة المنصة')
                    ->money('EGP')
                    ->sortable()
                    ->color('success'),

                TextColumn::make('completed_at')
                    ->label('تاريخ الإتمام')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->headerActions([])
            ->filters([
                SelectFilter::make('shop')
                    ->label('المحل')
                    ->relationship('shop', 'name'),

                Filter::make('completed_at')
                    ->schema([
                        DatePicker::make('from')->label('من'),
                        DatePicker::make('until')->label('إلى'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('completed_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('completed_at', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}

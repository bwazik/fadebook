<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shops\Tables;

use App\Enums\ShopStatus;
use App\Models\Shop;
use App\Services\WhatsAppService;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ShopsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('اسم المحل')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('owner.name')
                    ->label('صاحب المحل')
                    ->searchable(),

                TextColumn::make('area.name')
                    ->label('المنطقة')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (ShopStatus $state): string => $state->getLabel())
                    ->color(fn (ShopStatus $state): string => $state->getColor()),

                TextColumn::make('commission_rate')
                    ->label('نسبة العمولة %')
                    ->sortable(),

                TextColumn::make('total_bookings')
                    ->label('إجمالي الحجوزات')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('تاريخ التسجيل')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(ShopStatus::class)
                    ->attribute('status'),

                SelectFilter::make('area')
                    ->label('المنطقة')
                    ->relationship('area', 'name'),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('موافقة')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Shop $record): bool => $record->status === ShopStatus::Pending)
                    ->requiresConfirmation()
                    ->modalHeading('تأكيد الموافقة على المحل')
                    ->modalDescription('هل أنت متأكد إنك عايز توافق على المحل ده؟ هيتبعتله إشعار على الواتساب.')
                    ->action(function (Shop $record): void {
                        $record->update([
                            'status' => ShopStatus::Active,
                            'approved_at' => now(),
                        ]);

                        try {
                            app(WhatsAppService::class)->sendMessage(
                                $record->owner->phone,
                                'shop_approved',
                                ['shop_name' => $record->name],
                                'instant',
                                $record->owner->id,
                                $record->id
                            );
                        } catch (Exception) {
                            // notification still shown
                        }

                        Notification::make()
                            ->title('تمت الموافقة على المحل بنجاح')
                            ->success()
                            ->send();
                    }),

                Action::make('reject')
                    ->label('رفض')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Shop $record): bool => $record->status === ShopStatus::Pending)
                    ->schema([
                        Textarea::make('rejection_reason')
                            ->label('سبب الرفض')
                            ->required()
                            ->rows(3),
                    ])
                    ->modalHeading('رفض المحل')
                    ->action(function (Shop $record, array $data): void {
                        $record->update([
                            'status' => ShopStatus::Rejected,
                            'rejection_reason' => $data['rejection_reason'],
                            'rejected_at' => now(),
                        ]);

                        try {
                            app(WhatsAppService::class)->sendMessage(
                                $record->owner->phone,
                                'shop_rejected',
                                [
                                    'shop_name' => $record->name,
                                    'rejection_reason' => $data['rejection_reason'],
                                ],
                                'instant',
                                $record->owner->id,
                                $record->id
                            );
                        } catch (Exception) {
                            // notification still shown
                        }

                        Notification::make()
                            ->title('تم رفض المحل')
                            ->warning()
                            ->send();
                    }),

                Action::make('suspend')
                    ->label('تعليق')
                    ->icon('heroicon-o-pause-circle')
                    ->color('warning')
                    ->visible(fn (Shop $record): bool => $record->status === ShopStatus::Active)
                    ->requiresConfirmation()
                    ->modalHeading('تعليق المحل')
                    ->action(function (Shop $record): void {
                        $record->update(['status' => ShopStatus::Suspended]);

                        Notification::make()
                            ->title('تم تعليق المحل')
                            ->warning()
                            ->send();
                    }),

                Action::make('reactivate')
                    ->label('تفعيل')
                    ->icon('heroicon-o-play-circle')
                    ->color('success')
                    ->visible(fn (Shop $record): bool => $record->status === ShopStatus::Suspended)
                    ->requiresConfirmation()
                    ->modalHeading('تفعيل المحل')
                    ->action(function (Shop $record): void {
                        $record->update(['status' => ShopStatus::Active]);

                        Notification::make()
                            ->title('تم تفعيل المحل')
                            ->success()
                            ->send();
                    }),

                EditAction::make()->label('تعديل'),
            ]);
    }
}

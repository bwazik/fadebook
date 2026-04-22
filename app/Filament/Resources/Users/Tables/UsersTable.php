<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Tables;

use App\Enums\UserRole;
use App\Models\User;
use App\Services\WhatsAppService;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label('رقم الموبايل')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('role')
                    ->label('الدور')
                    ->badge()
                    ->formatStateUsing(fn (UserRole $state): string => $state->getLabel())
                    ->color(fn (UserRole $state): string => match ($state) {
                        UserRole::SuperAdmin => 'danger',
                        UserRole::BarberOwner => 'warning',
                        UserRole::Client => 'info',
                        UserRole::Barber => 'success',
                    }),

                TextColumn::make('no_show_count')
                    ->label('مرات الغياب')
                    ->sortable(),

                IconColumn::make('is_blocked')
                    ->label('محظور؟')
                    ->boolean()
                    ->trueColor('danger')
                    ->falseColor('success'),

                TextColumn::make('created_at')
                    ->label('تاريخ التسجيل')
                    ->formatStateUsing(fn ($state) => $state?->translatedFormat('l، j F Y - h:i A'))
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('الدور')
                    ->options(UserRole::class)
                    ->attribute('role'),

                TernaryFilter::make('is_blocked')
                    ->label('الحظر')
                    ->trueLabel('محظور')
                    ->falseLabel('غير محظور'),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('تعديل')
                    ->icon('heroicon-o-pencil-square'),
                DeleteAction::make()
                    ->label('حذف')
                    ->icon('heroicon-o-trash'),
                Action::make('ban')
                    ->label('حظر')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->visible(fn (User $record): bool => ! $record->is_blocked && $record->role !== UserRole::SuperAdmin)
                    ->requiresConfirmation()
                    ->modalHeading('حظر المستخدم')
                    ->modalDescription('هل أنت متأكد إنك عايز تحظر المستخدم ده؟')
                    ->action(function (User $record): void {
                        $record->update(['is_blocked' => true]);

                        Notification::make()
                            ->title('تم حظر المستخدم')
                            ->warning()
                            ->send();
                    }),

                Action::make('unblock')
                    ->label('رفع الحظر')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (User $record): bool => $record->is_blocked)
                    ->requiresConfirmation()
                    ->modalHeading('رفع الحظر')
                    ->action(function (User $record): void {
                        $record->update([
                            'is_blocked' => false,
                            'no_show_count' => 0,
                        ]);

                        Notification::make()
                            ->title('تم رفع الحظر عن المستخدم')
                            ->success()
                            ->send();
                    }),

                Action::make('send_whatsapp')
                    ->label('إرسال واتساب')
                    ->icon('heroicon-o-chat-bubble-left')
                    ->color('info')
                    ->schema([
                        Textarea::make('message')
                            ->label('الرسالة')
                            ->required()
                            ->rows(4),
                    ])
                    ->modalHeading('إرسال رسالة واتساب')
                    ->action(function (User $record, array $data): void {
                        try {
                            app(WhatsAppService::class)->sendMessage(
                                phone: $record->phone,
                                template: 'free_text',
                                data: ['message' => $data['message']],
                                priority: 'instant',
                                userId: $record->id
                            );
                        } catch (Exception $e) {
                            Notification::make()
                                ->title('فشل إرسال الرسالة')
                                ->danger()
                                ->body($e->getMessage())
                                ->send();

                            return;
                        }

                        Notification::make()
                            ->title('تم إرسال الرسالة')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}

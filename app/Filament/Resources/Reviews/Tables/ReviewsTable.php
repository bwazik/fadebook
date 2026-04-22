<?php

declare(strict_types=1);

namespace App\Filament\Resources\Reviews\Tables;

use App\Models\Review;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('reviewable.name')
                    ->label('المقيَّم')
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('اسم العميل')
                    ->searchable(),

                TextColumn::make('rating')
                    ->label('التقييم')
                    ->suffix(' ⭐')
                    ->sortable(),

                TextColumn::make('comment')
                    ->label('التعليق')
                    ->limit(60)
                    ->wrap(),

                IconColumn::make('is_flagged')
                    ->label('مُبلَّغ عنه؟')
                    ->boolean()
                    ->trueColor('danger')
                    ->falseColor('gray'),

                TextColumn::make('created_at')
                    ->label('تاريخ المراجعة')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_flagged')
                    ->label('التبليغ')
                    ->trueLabel('مُبلَّغ عنه')
                    ->falseLabel('غير مُبلَّغ'),
            ])
            ->recordActions([
                Action::make('mark_reviewed')
                    ->label('تمت المراجعة')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (Review $record): bool => $record->is_flagged)
                    ->action(function (Review $record): void {
                        $record->update(['is_flagged' => false]);

                        Notification::make()
                            ->title('تم تمييز المراجعة كمراجَعة')
                            ->success()
                            ->send();
                    }),

                DeleteAction::make()->label('حذف'),
            ]);
    }
}

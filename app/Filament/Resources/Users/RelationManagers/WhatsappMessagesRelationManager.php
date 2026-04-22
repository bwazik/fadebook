<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Enums\WhatsAppQueueType;
use App\Enums\WhatsAppStatus;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WhatsAppMessagesRelationManager extends RelationManager
{
    protected static string $relationship = 'whatsappMessages';

    protected static ?string $title = 'رسائل الواتساب';

    protected static ?string $modelLabel = 'رسالة';

    protected static ?string $pluralModelLabel = 'سجل رسائل الواتساب';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('phone')
            ->columns([
                TextColumn::make('phone')
                    ->label('رقم الهاتف')
                    ->searchable(),
                TextColumn::make('template')
                    ->label('القالب')
                    ->searchable(),
                TextColumn::make('queue_type')
                    ->label('الأولوية')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof WhatsAppQueueType ? $state->getLabel() : $state)
                    ->color(fn ($state): string => match ($state?->name ?? $state) {
                        'Instant', 1 => 'danger',
                        'Urgent', 2 => 'warning',
                        'Default', 3 => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof WhatsAppStatus ? $state->getLabel() : $state)
                    ->color(fn ($state): string => $state instanceof WhatsAppStatus ? $state->getColor() : 'gray'),
                TextColumn::make('sent_at')
                    ->label('تاريخ الإرسال')
                    ->formatStateUsing(fn ($state) => $state?->translatedFormat('l، j F Y - h:i A'))
                    ->sortable()
                    ->placeholder('قيد الانتظار'),
                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->formatStateUsing(fn ($state) => $state?->translatedFormat('l، j F Y - h:i A'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->defaultSort('created_at', 'desc');
    }
}

<?php

namespace App\Filament\Resources\Users\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WhatsAppMessagesRelationManager extends RelationManager
{
    protected static string $relationship = 'whatsappMessages';

    protected static ?string $title = 'رسائل واتساب';

    protected static ?string $modelLabel = 'رسالة';

    protected static ?string $pluralModelLabel = 'الرسائل';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('template')
            ->columns([
                TextColumn::make('template')
                    ->label('القالب'),
                TextColumn::make('phone')
                    ->label('الرقم'),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge(),
                TextColumn::make('sent_at')
                    ->label('تاريخ الإرسال')
                    ->dateTime(),
            ])
            ->filters([
                //
            ]);
    }
}

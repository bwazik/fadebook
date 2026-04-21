<?php

namespace App\Filament\Resources\Users\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PhoneChangeHistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'phoneChangeHistories';

    protected static ?string $title = 'سجل تغيير الهاتف';

    protected static ?string $modelLabel = 'سجل';

    protected static ?string $pluralModelLabel = 'سجلات التغيير';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('new_phone')
            ->columns([
                TextColumn::make('old_phone')
                    ->label('الرقم القديم'),
                TextColumn::make('new_phone')
                    ->label('الرقم الجديد'),
                TextColumn::make('ip_address')
                    ->label('العنوان IP'),
                TextColumn::make('created_at')
                    ->label('التاريخ')
                    ->dateTime(),
            ])
            ->filters([
                //
            ]);
    }
}

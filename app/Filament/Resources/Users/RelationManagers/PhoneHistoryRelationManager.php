<?php

namespace App\Filament\Resources\Users\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PhoneHistoryRelationManager extends RelationManager
{
    protected static string $relationship = 'phoneChangeHistory';

    protected static ?string $title = 'سجل تغيير الهاتف';

    protected static ?string $modelLabel = 'تغيير';

    protected static ?string $pluralModelLabel = 'سجل تغيير الهاتف';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('old_phone')
            ->columns([
                TextColumn::make('old_phone')
                    ->label('الرقم القديم')
                    ->searchable(),
                TextColumn::make('new_phone')
                    ->label('الرقم الجديد')
                    ->searchable(),
                TextColumn::make('ip_address')
                    ->label('العنوان IP'),
                TextColumn::make('created_at')
                    ->label('تاريخ التغيير')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                //
            ]);
    }
}

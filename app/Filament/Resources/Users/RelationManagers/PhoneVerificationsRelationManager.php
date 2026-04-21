<?php

namespace App\Filament\Resources\Users\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PhoneVerificationsRelationManager extends RelationManager
{
    protected static string $relationship = 'phoneVerifications';

    protected static ?string $title = 'توثيق الهاتف';

    protected static ?string $modelLabel = 'توثيق';

    protected static ?string $pluralModelLabel = 'سجل التوثيق';

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
                    ->label('الرقم'),
                TextColumn::make('otp_code')
                    ->label('الكود')
                    ->badge(),
                IconColumn::make('is_used')
                    ->label('استخدم')
                    ->boolean(),
                TextColumn::make('verified_at')
                    ->label('تاريخ التوثيق')
                    ->dateTime(),
                TextColumn::make('created_at')
                    ->label('تاريخ الطلب')
                    ->dateTime(),
            ])
            ->filters([
                //
            ]);
    }
}

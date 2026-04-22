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

    protected static ?string $title = 'توثيقات الهاتف';

    protected static ?string $modelLabel = 'توثيق';

    protected static ?string $pluralModelLabel = 'توثيقات الهاتف';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('otp_code')
            ->columns([
                TextColumn::make('phone')
                    ->label('رقم الهاتف')
                    ->searchable(),
                TextColumn::make('otp_code')
                    ->label('رمز التحقق'),
                IconColumn::make('is_used')
                    ->label('تم الاستخدام')
                    ->boolean(),
                TextColumn::make('expires_at')
                    ->label('تاريخ الانتهاء')
                    ->since()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('تاريخ الإرسال')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                //
            ]);
    }
}

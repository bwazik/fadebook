<?php

namespace App\Filament\Resources\Shops\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ViewsRelationManager extends RelationManager
{
    protected static string $relationship = 'views';

    protected static ?string $title = 'المشاهدات';

    protected static ?string $modelLabel = 'مشاهدة';

    protected static ?string $pluralModelLabel = 'سجل المشاهدات';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('ip_address')
            ->columns([
                TextColumn::make('user.name')
                    ->label('المستخدم')
                    ->placeholder('زائر')
                    ->searchable(),
                TextColumn::make('ip_address')
                    ->label('العنوان IP'),
                TextColumn::make('created_at')
                    ->label('تاريخ المشاهدة')
                    ->formatStateUsing(fn ($state) => $state?->translatedFormat('l، j F Y - h:i A'))
                    ->sortable(),
            ])
            ->filters([
                //
            ]);
    }
}

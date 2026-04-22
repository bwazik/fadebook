<?php

namespace App\Filament\Resources\Coupons\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsagesRelationManager extends RelationManager
{
    protected static string $relationship = 'usages';

    protected static ?string $title = 'سجل الاستخدام';

    protected static ?string $modelLabel = 'استخدام';

    protected static ?string $pluralModelLabel = 'سجل الاستخدام';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user.name')
            ->columns([
                TextColumn::make('user.name')
                    ->label('المستخدم')
                    ->searchable(),
                TextColumn::make('usage_count')
                    ->label('عدد المرات')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('أول استخدام')
                    ->formatStateUsing(fn ($state) => $state?->translatedFormat('l، j F Y - h:i A'))
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('آخر استخدام')
                    ->formatStateUsing(fn ($state) => $state?->translatedFormat('l، j F Y - h:i A'))
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Read-only usually
            ])
            ->recordActions([
                // Read-only usually
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('حذف'),
                ]),
            ]);
    }
}

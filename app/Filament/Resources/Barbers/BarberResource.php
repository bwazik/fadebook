<?php

namespace App\Filament\Resources\Barbers;

use App\Filament\Resources\Barbers\Pages\CreateBarber;
use App\Filament\Resources\Barbers\Pages\EditBarber;
use App\Filament\Resources\Barbers\Pages\ListBarbers;
use App\Filament\Resources\Barbers\RelationManagers\UnavailabilitiesRelationManager;
use App\Filament\Resources\Barbers\Schemas\BarberForm;
use App\Filament\Resources\Barbers\Tables\BarbersTable;
use App\Filament\Resources\Shops\RelationManagers\ImagesRelationManager;
use App\Models\Barber;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class BarberResource extends Resource
{
    protected static ?string $model = Barber::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedScissors;

    protected static ?string $navigationLabel = 'الحلاقين';

    protected static string|UnitEnum|null $navigationGroup = 'المستخدمين';

    protected static ?string $modelLabel = 'حلاق';

    protected static ?string $pluralModelLabel = 'الحلاقين';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return BarberForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BarbersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            UnavailabilitiesRelationManager::class,
            ImagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBarbers::route('/'),
            'create' => CreateBarber::route('/create'),
            'edit' => EditBarber::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}

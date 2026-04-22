<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shops;

use App\Filament\Resources\Shops\Pages\CreateShop;
use App\Filament\Resources\Shops\Pages\EditShop;
use App\Filament\Resources\Shops\Pages\ListShops;
use App\Filament\Resources\Shops\Pages\ViewShop;
use App\Filament\Resources\Shops\RelationManagers\BarbersRelationManager;
use App\Filament\Resources\Shops\RelationManagers\CategoriesRelationManager;
use App\Filament\Resources\Shops\RelationManagers\PaymentMethodsRelationManager;
use App\Filament\Resources\Shops\RelationManagers\ServicesRelationManager;
use App\Filament\Resources\Shops\RelationManagers\ViewsRelationManager;
use App\Filament\Resources\Shops\Schemas\ShopForm;
use App\Filament\Resources\Shops\Tables\ShopsTable;
use App\Filament\Resources\Users\RelationManagers\WhatsAppMessagesRelationManager;
use App\Models\Shop;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ShopResource extends Resource
{
    protected static ?string $model = Shop::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static string|UnitEnum|null $navigationGroup = 'المحلات';

    protected static ?string $navigationLabel = 'المحلات';

    protected static ?string $modelLabel = 'محل';

    protected static ?string $pluralModelLabel = 'المحلات';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return ShopForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ShopsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            CategoriesRelationManager::class,
            BarbersRelationManager::class,
            ServicesRelationManager::class,
            PaymentMethodsRelationManager::class,
            ViewsRelationManager::class,
            WhatsAppMessagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListShops::route('/'),
            'create' => CreateShop::route('/create'),
            'view' => ViewShop::route('/{record}'),
            'edit' => EditShop::route('/{record}/edit'),
        ];
    }
}

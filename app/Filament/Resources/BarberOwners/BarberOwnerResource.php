<?php

namespace App\Filament\Resources\BarberOwners;

use App\Enums\UserRole;
use App\Filament\Resources\BarberOwners\Pages\CreateBarberOwner;
use App\Filament\Resources\BarberOwners\Pages\EditBarberOwner;
use App\Filament\Resources\BarberOwners\Pages\ListBarberOwners;
use App\Filament\Resources\Users\Forms\UserForm;
use App\Filament\Resources\Users\RelationManagers\PhoneHistoryRelationManager;
use App\Filament\Resources\Users\RelationManagers\PhoneVerificationsRelationManager;
use App\Filament\Resources\Users\RelationManagers\WhatsAppMessagesRelationManager;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class BarberOwnerResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static string|UnitEnum|null $navigationGroup = 'المستخدمين';

    protected static ?string $navigationLabel = 'أصحاب المحلات';

    protected static ?string $modelLabel = 'صاحب محل';

    protected static ?string $pluralModelLabel = 'أصحاب المحلات';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('role', UserRole::BarberOwner);
    }

    public static function getRelations(): array
    {
        return [
            PhoneVerificationsRelationManager::class,
            PhoneHistoryRelationManager::class,
            WhatsAppMessagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBarberOwners::route('/'),
            'create' => CreateBarberOwner::route('/create'),
            'edit' => EditBarberOwner::route('/{record}/edit'),
        ];
    }
}

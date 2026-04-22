<?php

namespace App\Filament\Resources\BarberUsers;

use App\Enums\UserRole;
use App\Filament\Resources\BarberUsers\Pages\CreateBarberUser;
use App\Filament\Resources\BarberUsers\Pages\EditBarberUser;
use App\Filament\Resources\BarberUsers\Pages\ListBarberUsers;
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

class BarberUserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedScissors;

    protected static string|UnitEnum|null $navigationGroup = 'المستخدمين';

    protected static ?string $navigationLabel = 'حسابات الحلاقين';

    protected static ?string $modelLabel = 'حساب حلاق';

    protected static ?string $pluralModelLabel = 'حسابات الحلاقين';

    protected static ?int $navigationSort = 2;

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
            ->where('role', UserRole::Barber);
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
            'index' => ListBarberUsers::route('/'),
            'create' => CreateBarberUser::route('/create'),
            'edit' => EditBarberUser::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources\SuperAdmins;

use App\Enums\UserRole;
use App\Filament\Resources\SuperAdmins\Pages\CreateSuperAdmin;
use App\Filament\Resources\SuperAdmins\Pages\EditSuperAdmin;
use App\Filament\Resources\SuperAdmins\Pages\ListSuperAdmins;
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

class SuperAdminResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static string|UnitEnum|null $navigationGroup = 'المستخدمين';

    protected static ?string $navigationLabel = 'مديرو النظام';

    protected static ?string $modelLabel = 'مدير نظام';

    protected static ?string $pluralModelLabel = 'مديرو النظام';

    protected static ?int $navigationSort = 4;

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
            ->where('role', UserRole::SuperAdmin);
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
            'index' => ListSuperAdmins::route('/'),
            'create' => CreateSuperAdmin::route('/create'),
            'edit' => EditSuperAdmin::route('/{record}/edit'),
        ];
    }
}

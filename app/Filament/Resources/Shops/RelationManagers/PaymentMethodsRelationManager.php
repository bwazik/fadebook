<?php

namespace App\Filament\Resources\Shops\RelationManagers;

use App\Enums\PaymentMethodType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentMethodsRelationManager extends RelationManager
{
    protected static string $relationship = 'paymentMethods';

    protected static ?string $title = 'طرق الدفع';

    protected static ?string $modelLabel = 'طريقة دفع';

    protected static ?string $pluralModelLabel = 'طرق الدفع';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->label('النوع')
                    ->options(PaymentMethodType::class)
                    ->required()
                    ->native(false),
                TextInput::make('account_name')
                    ->label('اسم الحساب')
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone_number')
                    ->label('رقم الهاتف / الحساب')
                    ->tel()
                    ->required()
                    ->maxLength(255),
                TextInput::make('pay_link')
                    ->label('رابط الدفع المباشر')
                    ->url()
                    ->maxLength(255),
                Toggle::make('is_active')
                    ->label('نشط')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('account_name')
            ->columns([
                TextColumn::make('type')
                    ->label('النوع')
                    ->badge(),
                TextColumn::make('account_name')
                    ->label('الحساب')
                    ->searchable(),
                TextColumn::make('phone_number')
                    ->label('الرقم')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()->label('إضافة طريقة دفع')->modal(),
            ])
            ->recordActions([
                EditAction::make()->label('تعديل')->modal(),
                DeleteAction::make()->label('حذف'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('حذف'),
                ]),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Offers\Schemas;

use App\Enums\OfferType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class OfferForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('shop_id')
                    ->label('المحل')
                    ->relationship('shop', 'name')
                    ->required()
                    ->native(false),
                Select::make('type')
                    ->label('النوع')
                    ->options(OfferType::class)
                    ->required()
                    ->native(false),
                TextInput::make('title')
                    ->label('العنوان')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('الوصف')
                    ->maxLength(1000),
                Select::make('coupon_id')
                    ->label('الكوبون المرتبط')
                    ->relationship('coupon', 'code')
                    ->searchable()
                    ->native(false),
                DateTimePicker::make('start_date')
                    ->label('تاريخ البدء')
                    ->native(false),
                DateTimePicker::make('end_date')
                    ->label('تاريخ الانتهاء')
                    ->native(false),
                Toggle::make('is_active')
                    ->label('نشط')
                    ->default(true),
            ]);
    }
}

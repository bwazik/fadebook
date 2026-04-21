<?php

namespace App\Filament\Resources\Coupons\Schemas;

use App\Enums\DiscountType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('shop_id')
                    ->label('المحل')
                    ->relationship('shop', 'name')
                    ->searchable()
                    ->native(false),
                TextInput::make('code')
                    ->label('الكود')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignorable: fn ($record) => $record),
                Select::make('discount_type')
                    ->label('نوع الخصم')
                    ->options(DiscountType::class)
                    ->required()
                    ->native(false),
                TextInput::make('discount_value')
                    ->label('قيمة الخصم')
                    ->numeric()
                    ->required(),
                DateTimePicker::make('start_date')
                    ->label('تاريخ البدء')
                    ->native(false),
                DateTimePicker::make('end_date')
                    ->label('تاريخ الانتهاء')
                    ->native(false),
                TextInput::make('usage_limit')
                    ->label('حد الاستخدام الإجمالي')
                    ->numeric(),
                TextInput::make('usage_limit_per_user')
                    ->label('حد الاستخدام لكل مستخدم')
                    ->numeric()
                    ->default(1),
                TextInput::make('minimum_amount')
                    ->label('الحد الأدنى للطلب')
                    ->numeric(),
                Toggle::make('is_active')
                    ->label('نشط')
                    ->default(true),
            ]);
    }
}

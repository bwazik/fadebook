<?php

namespace App\Filament\Resources\Barbers\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BarberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('avatar')
                    ->label('الصورة الشخصية')
                    ->image()
                    ->directory('barbers/avatars')
                    ->maxSize(3072)
                    ->columnSpanFull(),
                Select::make('shop_id')
                    ->label('المحل')
                    ->relationship('shop', 'name')
                    ->required()
                    ->native(false),
                Select::make('user_id')
                    ->label('المستخدم المرتبط')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->native(false),
                TextInput::make('name')
                    ->label('الاسم')
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label('رقم الهاتف')
                    ->tel()
                    ->maxLength(20),
                Select::make('days_off')
                    ->label('أيام العطلة')
                    ->multiple()
                    ->options([
                        'sunday' => 'الأحد',
                        'monday' => 'الاثنين',
                        'tuesday' => 'الثلاثاء',
                        'wednesday' => 'الأربعاء',
                        'thursday' => 'الخميس',
                        'friday' => 'الجمعة',
                        'saturday' => 'السبت',
                    ])
                    ->native(false),
                Toggle::make('is_active')
                    ->label('نشط')
                    ->default(true),
                TextInput::make('sort_order')
                    ->label('الترتيب')
                    ->numeric()
                    ->default(0),
            ]);
    }
}

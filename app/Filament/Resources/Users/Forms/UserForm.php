<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Forms;

use App\Enums\UserRole;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('avatar')
                    ->label('الصورة الشخصية')
                    ->image()
                    ->directory('avatars')
                    ->maxSize(3072)
                    ->columnSpanFull(),

                TextInput::make('name')
                    ->label('الاسم')
                    ->required()
                    ->maxLength(255),

                TextInput::make('phone')
                    ->label('رقم الموبايل')
                    ->required()
                    ->tel()
                    ->unique(ignoreRecord: true),

                Select::make('role')
                    ->label('الدور')
                    ->options(UserRole::class)
                    ->required()
                    ->enum(UserRole::class),

                Toggle::make('is_blocked')
                    ->label('محظور')
                    ->onColor('danger'),

                Toggle::make('whatsapp_notifications')
                    ->label('تنبيهات واتساب')
                    ->default(true),
            ]);
    }
}

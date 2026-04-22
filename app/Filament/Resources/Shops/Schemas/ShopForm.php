<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shops\Schemas;

use App\Enums\BarberSelectionMode;
use App\Enums\PaymentMode;
use App\Enums\ShopStatus;
use App\Enums\UserRole;
use App\Models\Area;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ShopForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Shop Details')
                    ->tabs([
                        Tabs\Tab::make('البيانات الأساسية')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('اسم المحل')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn (string $operation, $state, $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                                        TextInput::make('slug')
                                            ->label('الرابط (Slug)')
                                            ->required()
                                            ->unique(ignoreRecord: true),
                                        Select::make('owner_id')
                                            ->label('صاحب المحل')
                                            ->relationship('owner', 'name', fn ($query) => $query->where('role', UserRole::BarberOwner))
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                        Select::make('area_id')
                                            ->label('المنطقة')
                                            ->options(Area::all()->pluck('name', 'id'))
                                            ->searchable()
                                            ->required(),
                                    ])->columns(2),

                                Section::make('بيانات الاتصال')
                                    ->schema([
                                        TextInput::make('phone')
                                            ->label('رقم الهاتف')
                                            ->tel()
                                            ->required(),
                                        TextInput::make('whatsapp')
                                            ->label('رقم الواتساب')
                                            ->tel()
                                            ->required(),
                                        TextInput::make('address')
                                            ->label('العنوان بالتفصيل')
                                            ->required()
                                            ->columnSpanFull(),
                                    ])->columns(2),
                            ]),

                        Tabs\Tab::make('الإعدادات والعمولة')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        Select::make('status')
                                            ->label('حالة المحل')
                                            ->options(ShopStatus::class)
                                            ->required()
                                            ->native(false),
                                        TextInput::make('commission_percentage')
                                            ->label('نسبة العمولة (%)')
                                            ->numeric()
                                            ->default(10)
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->required(),
                                        Select::make('barber_selection_mode')
                                            ->label('طريقة اختيار الحلاق')
                                            ->options(BarberSelectionMode::class)
                                            ->required()
                                            ->native(false),
                                        Select::make('payment_mode')
                                            ->label('نظام الدفع')
                                            ->options(PaymentMode::class)
                                            ->required()
                                            ->native(false),
                                    ])->columns(2),

                                Textarea::make('description')
                                    ->label('وصف المحل')
                                    ->rows(5)
                                    ->columnSpanFull(),
                            ]),

                        Tabs\Tab::make('الصور والوسائط')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                FileUpload::make('logo')
                                    ->label('اللوجو')
                                    ->image()
                                    ->directory('shops/logos')
                                    ->maxSize(3072),
                                FileUpload::make('banner')
                                    ->label('غلاف المحل')
                                    ->image()
                                    ->directory('shops/banners')
                                    ->maxSize(3072),
                                FileUpload::make('gallery')
                                    ->label('معرض الصور')
                                    ->image()
                                    ->multiple()
                                    ->directory('shops/gallery')
                                    ->maxSize(3072),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }
}

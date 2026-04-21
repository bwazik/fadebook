<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\DiscountType;
use App\Models\Setting;
use App\Services\SettingsService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Artisan;
use UnitEnum;

class SettingsPage extends Page implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string|UnitEnum|null $navigationGroup = 'الإعدادات';

    protected static ?string $navigationLabel = 'إعدادات المنصة';

    protected static ?string $title = 'إعدادات المنصة';

    protected string $view = 'filament.pages.settings-page';

    public ?array $data = [];

    public function mount(): void
    {
        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $this->form->fill($settings);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make('Settings')
                    ->tabs([
                        Tabs\Tab::make('المحتوى')
                            ->schema([
                                Section::make('سياسات المنصة')
                                    ->schema([
                                        RichEditor::make('terms_content')
                                            ->label('شروط الاستخدام')
                                            ->required(),
                                        RichEditor::make('privacy_content')
                                            ->label('سياسة الخصوصية')
                                            ->required(),
                                        RichEditor::make('contact_developer_content')
                                            ->label('محتوى تواصل مع المطور')
                                            ->required(),
                                    ]),
                            ]),
                        Tabs\Tab::make('عام')
                            ->schema([
                                Section::make('معلومات ورسوم')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('developer_whatsapp')
                                                    ->label('رقم واتساب المطور')
                                                    ->tel()
                                                    ->required(),
                                                TextInput::make('platform_whatsapp_number')
                                                    ->label('رقم واتساب المنصة')
                                                    ->tel()
                                                    ->required(),
                                                TextInput::make('default_commission_rate')
                                                    ->label('نسبة العمولة الافتراضية (%)')
                                                    ->numeric()
                                                    ->required(),
                                            ]),
                                        Toggle::make('fcm_enabled')
                                            ->label('تفعيل إشعارات FCM (Firebase)')
                                            ->default(false),
                                    ]),

                                Section::make('إعدادات الـ OTP')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('otp_expiry_minutes')
                                                    ->label('صلاحية الـ OTP (دقائق)')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('max_otp_attempts')
                                                    ->label('أقصى محاولات OTP')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('max_otp_requests_per_hour')
                                                    ->label('أقصى طلبات OTP في الساعة')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('otp_resend_cooldown_seconds')
                                                    ->label('وقت انتظار إعادة الإرسال (ثواني)')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('otp_cleanup_hours')
                                                    ->label('تنظيف الأكواد القديمة كل (ساعات)')
                                                    ->numeric()
                                                    ->required(),
                                            ]),
                                    ]),

                                Section::make('إعدادات الحجز')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('max_pending_bookings_per_client')
                                                    ->label('أقصى حجوزات معلقة لكل عميل')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('no_show_grace_period_minutes')
                                                    ->label('فترة السماح للغياب (دقائق)')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('cancellation_window_hours')
                                                    ->label('نافذة الإلغاء (ساعات)')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('max_cancellation_limit')
                                                    ->label('أقصى حد للإلغاء المسموح')
                                                    ->numeric()
                                                    ->required(),
                                            ]),
                                    ]),
                            ]),
                        Tabs\Tab::make('الإحالات')
                            ->schema([
                                Section::make('نظام المكافآت')
                                    ->schema([
                                        Toggle::make('referral_reward_enabled')
                                            ->label('تفعيل مكافآت الإحالة')
                                            ->live(),
                                        Toggle::make('referral_reward_unlimited_mode')
                                            ->label('وضع مكافآت غير محدود')
                                            ->visible(fn ($get) => $get('referral_reward_enabled')),
                                        Grid::make(2)
                                            ->schema([
                                                Select::make('referral_reward_discount_type')
                                                    ->label('نوع الخصم')
                                                    ->options(DiscountType::class)
                                                    ->required()
                                                    ->visible(fn ($get) => $get('referral_reward_enabled')),
                                                TextInput::make('referral_reward_discount_value')
                                                    ->label('قيمة المكافأة')
                                                    ->numeric()
                                                    ->visible(fn ($get) => $get('referral_reward_enabled')),
                                                TextInput::make('referral_reward_coupon_expiry_days')
                                                    ->label('صلاحية الكوبون (أيام)')
                                                    ->numeric()
                                                    ->visible(fn ($get) => $get('referral_reward_enabled')),
                                            ]),
                                    ]),
                            ]),
                        Tabs\Tab::make('الحماية')
                            ->schema([
                                Section::make('تسجيل الدخول والتسجيل')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('rate_limit_login-attempt_attempts')
                                                    ->label('أقصى محاولات دخول')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('rate_limit_login-attempt_seconds')
                                                    ->label('مدة محاولات الدخول (ثواني)')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('rate_limit_register-next-step_attempts')
                                                    ->label('أقصى محاولات الانتقال في التسجيل')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('rate_limit_register-next-step_seconds')
                                                    ->label('مدة الانتقال في التسجيل (ثواني)')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('rate_limit_register-submit_attempts')
                                                    ->label('أقصى محاولات إرسال التسجيل')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('rate_limit_register-submit_seconds')
                                                    ->label('مدة إرسال التسجيل (ثواني)')
                                                    ->numeric()
                                                    ->required(),
                                            ]),
                                    ]),

                                Section::make('توثيق الموبايل والـ OTP')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('rate_limit_verify-otp_attempts')
                                                    ->label('أقصى محاولات توثيق OTP')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('rate_limit_verify-otp_seconds')
                                                    ->label('مدة توثيق OTP (ثواني)')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('rate_limit_resend-otp_attempts')
                                                    ->label('أقصى محاولات إعادة إرسال OTP')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('rate_limit_resend-otp_seconds')
                                                    ->label('مدة إعادة إرسال OTP (ثواني)')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('rate_limit_change-phone-send_attempts')
                                                    ->label('أقصى محاولات إرسال تغيير الموبايل')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('rate_limit_change-phone-send_seconds')
                                                    ->label('مدة إرسال تغيير الموبايل (ثواني)')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('rate_limit_change-phone-verify_attempts')
                                                    ->label('أقصى محاولات توثيق تغيير الموبايل')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('rate_limit_change-phone-verify_seconds')
                                                    ->label('مدة توثيق تغيير الموبايل (ثواني)')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('rate_limit_change-phone-resend_attempts')
                                                    ->label('أقصى محاولات إعادة إرسال تغيير الموبايل')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('rate_limit_change-phone-resend_seconds')
                                                    ->label('مدة إعادة تغيير الموبايل (ثواني)')
                                                    ->numeric()
                                                    ->required(),
                                            ]),
                                    ]),

                                Section::make('استعادة كلمة السر وتغييرها')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('rate_limit_forgot-password-send_attempts')
                                                    ->label('أقصى محاولات طلب نسيان كلمة السر')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('rate_limit_forgot-password-send_seconds')
                                                    ->label('مدة طلب النسيان (ثواني)')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('rate_limit_forgot-password-verify_attempts')
                                                    ->label('أقصى محاولات توثيق نسيان كلمة السر')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('rate_limit_forgot-password-verify_seconds')
                                                    ->label('مدة توثيق النسيان (ثواني)')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('rate_limit_forgot-password-resend_attempts')
                                                    ->label('أقصى محاولات إعادة إرسال كود النسيان')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('rate_limit_forgot-password-resend_seconds')
                                                    ->label('مدة إعادة إرسال كود النسيان (ثواني)')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('rate_limit_forgot-password-reset_attempts')
                                                    ->label('أقصى محاولات إعادة تعيين كلمة السر')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('rate_limit_forgot-password-reset_seconds')
                                                    ->label('مدة إعادة التعيين (ثواني)')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('rate_limit_change-password-send_attempts')
                                                    ->label('أقصى محاولات طلب تغيير كلمة السر')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('rate_limit_change-password-send_seconds')
                                                    ->label('مدة طلب تغيير كلمة السر (ثواني)')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('rate_limit_change-password-verify_attempts')
                                                    ->label('أقصى محاولات توثيق تغيير كلمة السر')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('rate_limit_change-password-verify_seconds')
                                                    ->label('مدة توثيق تغيير كلمة السر (ثواني)')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('rate_limit_change-password-resend_attempts')
                                                    ->label('أقصى محاولات إعادة إرسال كود التغيير')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('rate_limit_change-password-resend_seconds')
                                                    ->label('مدة إعادة إرسال كود التغيير (ثواني)')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('rate_limit_change-password-update_attempts')
                                                    ->label('أقصى محاولات تحديث كلمة السر')
                                                    ->numeric()
                                                    ->required(),
                                                TextInput::make('rate_limit_change-password-update_seconds')
                                                    ->label('مدة تحديث كلمة السر (ثواني)')
                                                    ->numeric()
                                                    ->required(),
                                            ]),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('حفظ التعديلات')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            $formattedValue = match (true) {
                $value instanceof BackedEnum => (string) $value->value,
                $value instanceof UnitEnum => $value->name,
                is_bool($value) => $value ? 'true' : 'false',
                default => (string) $value,
            };

            app(SettingsService::class)->set($key, $formattedValue);
        }

        Notification::make()
            ->title('تم حفظ الإعدادات بنجاح')
            ->success()
            ->send();

        Artisan::call('cache:clear');
    }
}

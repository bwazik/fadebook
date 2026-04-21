<?php

namespace App\Notifications\User;

use App\Enums\DiscountType;
use App\Models\Referral;
use App\Notifications\Channels\WhatsAppChannel;
use App\Traits\NotificationDataStructure;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReferralRewardIssuedNotification extends Notification
{
    use NotificationDataStructure, Queueable;

    public function __construct(public Referral $referral)
    {
        $this->referral->loadMissing(['shop', 'coupon']);
    }

    public function via($notifiable): array
    {
        return ['database', WhatsAppChannel::class];
    }

    public function toDatabase($notifiable): array
    {
        return $this->getStandardData();
    }

    protected function getEntityId()
    {
        return $this->referral->id;
    }

    protected function getNotificationType(): string
    {
        return 'referral_reward_issued';
    }

    protected function getEntityType(): string
    {
        return 'referral';
    }

    protected function getTitle(): string
    {
        return 'مبروك! ربحت مكافأة';
    }

    protected function getShortMessage(): string
    {
        return 'تم إصدار مكافأة دعوة أصدقاء لك بنجاح. استمتع بخصم على حجزك القادم!';
    }

    protected function getMessage(): string
    {
        return $this->getShortMessage();
    }

    protected function getIcon(): string
    {
        return 'heroicon-o-gift';
    }

    protected function getIconBg(): string
    {
        return 'bg-pink-500';
    }

    protected function getActionUrl(): string
    {
        return route('profile.referral');
    }

    protected function getActionText(): string
    {
        return 'عرض المكافآت';
    }

    protected function getCustomData(): array
    {
        return [
            'referral_id' => $this->referral->id,
            'coupon_code' => $this->referral->coupon?->code,
        ];
    }

    public function getWhatsAppData(): array
    {
        $coupon = $this->referral->coupon;
        $discountLabel = '';

        if ($coupon) {
            $isFixed = $coupon->discount_type === DiscountType::Fixed;
            $showPrices = $this->referral->shop?->show_service_prices ?? true;

            if ($isFixed && ! $showPrices) {
                $discountLabel = __('messages.offers_discount_hidden');
            } else {
                $suffix = $coupon->discount_type === DiscountType::Percentage ? '%' : ' '.__('messages.egp');
                $discountLabel = number_format((float) $coupon->discount_value, 0).$suffix;
            }
        }

        return [
            'discount_amount' => $discountLabel,
            'coupon_code' => $coupon?->code,
        ];
    }

    public function getRelatedShopId(): ?int
    {
        return $this->referral->shop_id;
    }
}

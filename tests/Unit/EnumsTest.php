<?php

declare(strict_types=1);

use App\Enums\BarberSelectionMode;
use App\Enums\BookingStatus;
use App\Enums\CancelledBy;
use App\Enums\DiscountType;
use App\Enums\OtpType;
use App\Enums\PaymentMode;
use App\Enums\RefundReason;
use App\Enums\RefundStatus;
use App\Enums\ShopStatus;
use App\Enums\UserRole;
use App\Enums\WhatsAppQueueType;
use App\Enums\WhatsAppStatus;

it('every UserRole case returns a non-empty label', function () {
    foreach (UserRole::cases() as $case) {
        expect($case->getLabel())->toBeString()->not->toBeEmpty();
    }
});

it('every ShopStatus case returns a non-empty label', function () {
    foreach (ShopStatus::cases() as $case) {
        expect($case->getLabel())->toBeString()->not->toBeEmpty();
    }
});

it('every BookingStatus case returns a non-empty label', function () {
    foreach (BookingStatus::cases() as $case) {
        expect($case->getLabel())->toBeString()->not->toBeEmpty();
    }
});

it('every CancelledBy case returns a non-empty label', function () {
    foreach (CancelledBy::cases() as $case) {
        expect($case->getLabel())->toBeString()->not->toBeEmpty();
    }
});

it('every OtpType case returns a non-empty label', function () {
    foreach (OtpType::cases() as $case) {
        expect($case->getLabel())->toBeString()->not->toBeEmpty();
    }
});

it('every PaymentMode case returns a non-empty label', function () {
    foreach (PaymentMode::cases() as $case) {
        expect($case->getLabel())->toBeString()->not->toBeEmpty();
    }
});

it('every BarberSelectionMode case returns a non-empty label', function () {
    foreach (BarberSelectionMode::cases() as $case) {
        expect($case->getLabel())->toBeString()->not->toBeEmpty();
    }
});

it('every RefundReason case returns a non-empty label', function () {
    foreach (RefundReason::cases() as $case) {
        expect($case->getLabel())->toBeString()->not->toBeEmpty();
    }
});

it('every RefundStatus case returns a non-empty label', function () {
    foreach (RefundStatus::cases() as $case) {
        expect($case->getLabel())->toBeString()->not->toBeEmpty();
    }
});

it('every DiscountType case returns a non-empty label', function () {
    foreach (DiscountType::cases() as $case) {
        expect($case->getLabel())->toBeString()->not->toBeEmpty();
    }
});

it('every WhatsAppQueueType case returns a non-empty label', function () {
    foreach (WhatsAppQueueType::cases() as $case) {
        expect($case->getLabel())->toBeString()->not->toBeEmpty();
    }
});

it('every WhatsAppStatus case returns a non-empty label', function () {
    foreach (WhatsAppStatus::cases() as $case) {
        expect($case->getLabel())->toBeString()->not->toBeEmpty();
    }
});

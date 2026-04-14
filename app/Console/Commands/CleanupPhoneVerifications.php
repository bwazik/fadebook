<?php

namespace App\Console\Commands;

use App\Models\PhoneVerification;
use App\Services\SettingsService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('phone-verifications:cleanup')]
#[Description('Soft delete expired or used phone verifications.')]
class CleanupPhoneVerifications extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(SettingsService $settingsService): int
    {
        $hours = (int) $settingsService->get('otp_cleanup_hours', 24);

        $count = PhoneVerification::olderThanHours($hours)
            ->where(function ($query) {
                $query->used()->orWhere->expired();
            })
            ->delete();

        $this->info("Successfully soft-deleted {$count} old phone verifications (older than {$hours} hours).");

        return Command::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use App\Models\PhoneVerification;
use App\Services\SettingsService;
use Illuminate\Console\Command;

class CleanupPhoneVerifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phone-verifications:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Soft delete expired or used phone verifications.';

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

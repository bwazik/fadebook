<?php

namespace App\Jobs;

use App\Models\Shop;
use App\Services\ViewService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TrackShopView implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Shop $shop,
        public ?int $userId,
        public string $ip,
        public ?string $userAgent
    ) {}

    /**
     * Execute the job.
     */
    public function handle(ViewService $viewService): void
    {
        $viewService->trackViewRaw(
            $this->shop,
            $this->userId,
            $this->ip,
            $this->userAgent
        );
    }
}

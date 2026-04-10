<?php

namespace App\Console\Commands;

use App\Enums\WhatsAppQueueType;
use App\Enums\WhatsAppStatus;
use App\Models\WhatsAppMessage;
use App\Services\WhatsappService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RequeueFailedWhatsappMessages extends Command
{
    protected $signature = 'whatsapp:requeue-failed
                            {--from= : Start of ID range}
                            {--to=   : End of ID range}
                            {--all   : Requeue all failed messages}
                            {--priority= : Filter by priority (instant|urgent|default)}
                            {--force : Skip confirmation prompt (for CI/automation)}';

    protected $description = 'Requeue failed WhatsApp messages with correct priority routing';

    public function __construct(protected WhatsappService $whatsappService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $from = $this->option('from');
        $to = $this->option('to');
        $all = $this->option('all');
        $priorityFilter = $this->option('priority');

        // Validate priority filter
        if ($priorityFilter && ! in_array($priorityFilter, ['instant', 'urgent', 'default'])) {
            $this->error('Invalid priority. Use: instant, urgent, or default');

            return self::FAILURE;
        }

        // Build base query
        $query = WhatsAppMessage::where('status', WhatsAppStatus::Failed);

        if ($all) {
            $this->info('Requeuing ALL failed messages...');
        } elseif ($from && $to) {
            $query->whereBetween('id', [$from, $to]);
            $this->info("Requeuing failed messages from ID {$from} to {$to}...");
        } else {
            $this->error('Specify --from and --to, or use --all');

            return self::FAILURE;
        }

        $failedMessages = $query->orderBy('id')->get();

        if ($failedMessages->isEmpty()) {
            $this->warn('No failed messages found.');

            return self::SUCCESS;
        }

        $this->info("Found {$failedMessages->count()} failed message(s).");

        // Preview table (first 10)
        $this->table(
            ['ID', 'Phone', 'Template', 'Queue', 'Error'],
            $failedMessages->take(10)->map(fn ($m) => [
                $m->id,
                $m->phone,
                $m->template,
                $this->resolvePriorityLabel($m),
                mb_strimwidth($m->error_message ?? 'Unknown', 0, 45, '…'),
            ])
        );

        if ($failedMessages->count() > 10) {
            $this->line('... and '.($failedMessages->count() - 10).' more.');
        }

        // Priority breakdown
        $stats = ['instant' => 0, 'urgent' => 0, 'default' => 0];
        foreach ($failedMessages as $message) {
            $stats[$this->resolvePriorityLabel($message)]++;
        }

        $this->newLine();
        $this->line("  🚀 Instant: {$stats['instant']}  ⚡ Urgent: {$stats['urgent']}  📨 Default: {$stats['default']}");
        $this->newLine();

        // Confirm unless --force or running non-interactively
        if (! $this->option('force') && $this->input->isInteractive()) {
            if (! $this->confirm('Requeue these messages?', true)) {
                $this->info('Cancelled.');

                return self::SUCCESS;
            }
        }

        $bar = $this->output->createProgressBar($failedMessages->count());
        $bar->start();

        $requeued = 0;
        $skipped = 0;

        foreach ($failedMessages as $message) {
            try {
                $priority = $this->resolvePriorityLabel($message);

                // Apply optional priority filter
                if ($priorityFilter && $priority !== $priorityFilter) {
                    $skipped++;
                    $bar->advance();

                    continue;
                }

                // Send first — delete only on success to preserve audit trail
                $this->whatsappService->sendMessage(
                    phone: $message->phone,
                    template: $message->template,
                    data: $message->data ?? [],
                    priority: $priority,
                    userId: $message->user_id,
                    shopId: $message->shop_id,
                );

                $message->delete();
                $requeued++;
            } catch (\Throwable $e) {
                $this->newLine();
                $this->error("Failed to requeue message #{$message->id}: {$e->getMessage()}");

                Log::channel('whatsapp')->error('Requeue failed', [
                    'message_id' => $message->id,
                    'error' => $e->getMessage(),
                ]);

                $skipped++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Requeued: {$requeued}");

        if ($skipped > 0) {
            $this->warn("⚠️  Skipped: {$skipped}");
        }

        return self::SUCCESS;
    }

    /**
     * Resolve the string priority label for a message based on its queue_type or template.
     *
     * @return string Priority: 'instant', 'urgent', or 'default'
     */
    protected function resolvePriorityLabel(WhatsAppMessage $message): string
    {
        if ($message->queue_type instanceof WhatsAppQueueType) {
            return match ($message->queue_type) {
                WhatsAppQueueType::Instant => 'instant',
                WhatsAppQueueType::Urgent => 'urgent',
                WhatsAppQueueType::Default => 'default',
            };
        }

        // Fallback for legacy rows without queue_type
        return $message->template === 'otp_verification' ? 'instant' : 'default';
    }
}

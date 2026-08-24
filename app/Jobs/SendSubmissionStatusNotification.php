<?php

namespace App\Jobs;

use App\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Notifications\SubmissionStatusNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Throwable;

class SendSubmissionStatusNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public string $submissionId,
        public string $status,
        public ?string $reason = null,
        public ?string $eventId = null,
        public ?int $deliveryId = null,
    ) {}

    public function backoff(): array
    {
        return [10, 60, 300];
    }

    public function handle(): void
    {
        $submission = Submission::with(['applicant', 'song', 'files', 'links', 'period'])->findOrFail($this->submissionId);
        $eventKey = $this->eventId ?: $this->status;
        $key = hash('sha256', "status:{$submission->id}:{$eventKey}");
        $delivery = $this->deliveryId
            ? DB::table('notification_deliveries')->where('id', $this->deliveryId)->first()
            : DB::table('notification_deliveries')->where('idempotency_key', $key)->first();
        if ($delivery) {
            $key = $delivery->idempotency_key;
        }
        if ($delivery?->status === 'sent') {
            return;
        }
        DB::table('notification_deliveries')->insertOrIgnore([
            'submission_id' => $submission->id, 'channel' => 'mail', 'template' => 'submission_status_'.$this->status,
            'recipient_hash' => hash_hmac('sha256', strtolower($submission->applicant->email), config('app.key')),
            'status' => 'queued', 'attempts' => 0, 'idempotency_key' => $key, 'updated_at' => now(), 'created_at' => now(),
        ]);
        DB::table('notification_deliveries')->where('idempotency_key', $key)->update(['status' => 'sending', 'attempts' => DB::raw('attempts + 1'), 'updated_at' => now()]);

        try {
            Notification::route('mail', $submission->applicant->email)->notifyNow(
                new SubmissionStatusNotification(
                    $submission,
                    SubmissionStatus::from($this->status),
                    $this->reason,
                ),
            );
            DB::table('notification_deliveries')->where('idempotency_key', $key)->update(['status' => 'sent', 'sent_at' => now(), 'last_error' => null, 'updated_at' => now()]);
        } catch (Throwable $exception) {
            DB::table('notification_deliveries')->where('idempotency_key', $key)->update([
                'status' => 'failed',
                'last_error' => mb_substr($exception->getMessage(), 0, 2000),
                'updated_at' => now(),
            ]);

            throw $exception;
        }
    }

    public function failed(Throwable $exception): void
    {
        $eventKey = $this->eventId ?: $this->status;
        $key = hash('sha256', "status:{$this->submissionId}:{$eventKey}");
        if ($this->deliveryId) {
            $key = DB::table('notification_deliveries')->where('id', $this->deliveryId)->value('idempotency_key') ?: $key;
        }
        DB::table('notification_deliveries')->where('idempotency_key', $key)->update(['status' => 'failed', 'last_error' => mb_substr($exception->getMessage(), 0, 2000), 'updated_at' => now()]);
    }
}

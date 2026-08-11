<?php

namespace App\Services\Submission;

use App\Enums\SubmissionStatus;
use App\Jobs\SendSubmissionStatusNotification;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class SubmissionStateMachine
{
    private const TRANSITIONS = [
        'draft' => ['submitted'],
        'submitted' => ['administrative_review', 'withdrawn'],
        'administrative_review' => ['revision_requested', 'eligible', 'withdrawn', 'disqualified'],
        'revision_requested' => ['administrative_review'],
        'eligible' => ['curation', 'withdrawn'],
        'curation' => ['shortlisted', 'withdrawn', 'disqualified'],
        'shortlisted' => ['selected', 'not_selected', 'withdrawn'],
    ];

    public function transition(Submission $submission, SubmissionStatus $to, ?User $actor, ?string $reason = null): Submission
    {
        $from = $submission->status;
        if (! in_array($to->value, self::TRANSITIONS[$from->value] ?? [], true)) {
            throw ValidationException::withMessages(['status' => "Transisi {$from->value} ke {$to->value} tidak diizinkan."]);
        }

        return DB::transaction(function () use ($submission, $from, $to, $actor, $reason) {
            $submission->update(['status' => $to, 'submitted_at' => $to === SubmissionStatus::Submitted ? now() : $submission->submitted_at]);
            $submission->statusHistories()->create([
                'from_status' => $from->value,
                'to_status' => $to->value,
                'actor_id' => $actor?->id,
                'reason' => $reason,
            ]);

            DB::afterCommit(function () use ($submission, $to): void {
                try {
                    SendSubmissionStatusNotification::dispatch($submission->id, $to->value);
                } catch (\Throwable $exception) {
                    report($exception);
                }
            });

            return $submission->refresh();
        });
    }
}

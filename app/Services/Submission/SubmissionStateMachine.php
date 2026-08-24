<?php

namespace App\Services\Submission;

use App\Enums\SubmissionStatus;
use App\Jobs\SendSubmissionStatusNotification;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

final class SubmissionStateMachine
{
    private const TRANSITIONS = [
        'draft' => ['submitted'],
        'submitted' => ['administrative_review', 'selected', 'not_selected', 'withdrawn'],
        'administrative_review' => ['revision_requested', 'eligible', 'selected', 'not_selected', 'withdrawn', 'disqualified'],
        'revision_requested' => ['administrative_review'],
        'eligible' => ['curation', 'selected', 'not_selected', 'withdrawn'],
        'curation' => ['shortlisted', 'selected', 'not_selected', 'withdrawn', 'disqualified'],
        'shortlisted' => ['selected', 'not_selected', 'withdrawn'],
        'selected' => ['administrative_review', 'not_selected'],
        'not_selected' => ['administrative_review', 'selected'],
        'disqualified' => ['administrative_review', 'selected'],
    ];

    public function transition(Submission $submission, SubmissionStatus $to, ?User $actor, ?string $reason = null): Submission
    {
        $rawStatus = $submission->getRawOriginal('status') ?: $submission->status;
        $from = $rawStatus instanceof SubmissionStatus
            ? $rawStatus
            : SubmissionStatus::tryFrom((string) $rawStatus);

        // Older/partially migrated databases may return an empty status for a
        // newly created submission. New submissions always begin as draft.
        if (! $from && ! $submission->submitted_at) {
            $from = SubmissionStatus::Draft;
            $submission->setAttribute('status', $from);
        }

        if (! $from || ! in_array($to->value, self::TRANSITIONS[$from->value] ?? [], true)) {
            $fromLabel = $from?->value ?: 'tidak diketahui';
            throw ValidationException::withMessages(['status' => "Transisi {$fromLabel} ke {$to->value} tidak diizinkan."]);
        }

        return DB::transaction(function () use ($submission, $from, $to, $actor, $reason) {
            $submission->update([
                'status' => $to,
                'submitted_at' => $to === SubmissionStatus::Submitted ? now() : $submission->submitted_at,
            ]);
            $history = $submission->statusHistories()->create([
                'from_status' => $from->value,
                'to_status' => $to->value,
                'actor_id' => $actor?->id,
                'reason' => $reason,
            ]);

            DB::afterCommit(function () use ($submission, $to, $actor, $reason, $history): void {
                if ($to === SubmissionStatus::NotSelected) {
                    $submission->files()->where('type', 'video')->whereNull('trashed_at')->get()->each(function ($file) use ($actor, $reason): void {
                        $disk = Storage::disk($file->disk);
                        $originalPath = $file->path;
                        $trashPath = "trash/rejected/{$file->submission_id}/{$file->id}-".basename($originalPath);

                        if ($disk->exists($originalPath)) {
                            $disk->move($originalPath, $trashPath);
                        }

                        $file->update([
                            'original_path' => $originalPath,
                            'path' => $trashPath,
                            'scan_status' => 'trashed',
                            'trashed_at' => now(),
                            'trashed_by' => $actor?->id,
                            'trash_reason' => $reason,
                        ]);
                    });
                }

                try {
                    // Status emails are sent immediately after the database
                    // commit. Shared-hosting installations therefore do not
                    // need a persistent queue worker or scheduled task.
                    SendSubmissionStatusNotification::dispatchSync(
                        $submission->id,
                        $to->value,
                        $reason,
                        (string) $history->id,
                    );
                } catch (\Throwable $exception) {
                    report($exception);
                }
            });

            return $submission->refresh();
        });
    }
}

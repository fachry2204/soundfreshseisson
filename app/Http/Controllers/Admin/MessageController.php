<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SubmissionStatus;
use App\Http\Controllers\Controller;
use App\Jobs\SendSubmissionStatusNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class MessageController extends Controller
{
    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()->is_active && in_array($request->user()->role, ['super_admin', 'admin', 'program_admin', 'administrative_reviewer', 'viewer'], true), 403);
    }

    public function index(Request $request): Response
    {
        $this->authorizeAdmin($request);
        $filters = $request->validate([
            'status' => ['nullable', Rule::in(['sent', 'pending', 'failed'])],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $base = DB::table('notification_deliveries as deliveries')
            ->leftJoin('submissions', 'submissions.id', '=', 'deliveries.submission_id')
            ->leftJoin('applicants', 'applicants.id', '=', 'submissions.applicant_id')
            ->leftJoin('songs', 'songs.submission_id', '=', 'submissions.id');

        $counts = (clone $base)
            ->selectRaw("SUM(CASE WHEN deliveries.status = 'sent' THEN 1 ELSE 0 END) as sent")
            ->selectRaw("SUM(CASE WHEN deliveries.status IN ('queued', 'sending') THEN 1 ELSE 0 END) as pending")
            ->selectRaw("SUM(CASE WHEN deliveries.status = 'failed' THEN 1 ELSE 0 END) as failed")
            ->first();

        $query = $base->select([
            'deliveries.*',
            'submissions.registration_number',
            'applicants.full_name',
            'applicants.email',
            'songs.title as song_title',
            'songs.artist_name',
        ]);

        if (($filters['status'] ?? null) === 'pending') {
            $query->whereIn('deliveries.status', ['queued', 'sending']);
        } elseif (! empty($filters['status'])) {
            $query->where('deliveries.status', $filters['status']);
        }

        if ($search = trim($filters['search'] ?? '')) {
            $query->where(function ($inner) use ($search): void {
                $inner->where('applicants.full_name', 'like', "%{$search}%")
                    ->orWhere('applicants.email', 'like', "%{$search}%")
                    ->orWhere('submissions.registration_number', 'like', "%{$search}%")
                    ->orWhere('songs.title', 'like', "%{$search}%");
            });
        }

        $messages = $query->latest('deliveries.updated_at')->paginate(20)->withQueryString();
        $messages->through(fn ($message) => [
            ...((array) $message),
            'display_status' => in_array($message->status, ['queued', 'sending'], true) ? 'pending' : $message->status,
            'subject' => $this->subjectFor($message->template, $message->registration_number),
        ]);

        return Inertia::render('Admin/Messages/Index', [
            'messages' => $messages,
            'counts' => [
                'sent' => (int) ($counts->sent ?? 0),
                'pending' => (int) ($counts->pending ?? 0),
                'failed' => (int) ($counts->failed ?? 0),
            ],
            'filters' => $filters,
        ]);
    }

    public function retry(Request $request, int $delivery): RedirectResponse
    {
        $this->authorizeAdmin($request);
        abort_if($request->user()->role === 'viewer', 403);

        $record = DB::table('notification_deliveries')->where('id', $delivery)->first();
        abort_unless($record, 404);
        abort_unless($record->status === 'failed', 422, 'Hanya email gagal yang dapat dikirim ulang.');
        abort_unless($record->submission_id, 422, 'Data pendaftaran untuk email ini tidak tersedia.');

        $statusValue = str_starts_with($record->template, 'submission_status_')
            ? substr($record->template, strlen('submission_status_'))
            : null;
        $status = $statusValue ? SubmissionStatus::tryFrom($statusValue) : null;
        abort_unless($status, 422, 'Jenis email tidak mendukung pengiriman ulang otomatis.');

        $reason = DB::table('status_histories')
            ->where('submission_id', $record->submission_id)
            ->where('to_status', $status->value)
            ->latest('id')
            ->value('reason');

        try {
            SendSubmissionStatusNotification::dispatchSync(
                $record->submission_id,
                $status->value,
                $reason,
                null,
                $record->id,
            );
        } catch (Throwable $exception) {
            report($exception);

            return back()->with('error', 'Email masih gagal dikirim. Periksa informasi kegagalan dan konfigurasi SMTP.');
        }

        return back()->with('success', 'Email berhasil dikirim ulang.');
    }

    private function subjectFor(string $template, ?string $registrationNumber): string
    {
        $status = str_starts_with($template, 'submission_status_')
            ? SubmissionStatus::tryFrom(substr($template, strlen('submission_status_')))
            : null;
        $label = match ($status) {
            SubmissionStatus::Submitted => 'Lagu berhasil disubmit',
            SubmissionStatus::AdministrativeReview => 'Status Di Review',
            SubmissionStatus::Selected => 'Status Diterima',
            SubmissionStatus::NotSelected => 'Status Ditolak',
            default => $status ? 'Status '.$status->publicLabel() : str($template)->headline()->toString(),
        };

        return $label.($registrationNumber ? ' — '.$registrationNumber : '');
    }
}

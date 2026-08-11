<?php

namespace App\Http\Controllers\Public;

use App\Enums\SubmissionStatus;
use App\Http\Controllers\Controller;
use App\Jobs\ScanSubmissionFile;
use App\Models\Submission;
use App\Notifications\ApplicantMagicLinkNotification;
use App\Services\Submission\SubmissionStateMachine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ApplicantPortalController extends Controller
{
    public function requestLink(Request $request): RedirectResponse
    {
        $data = $request->validate(['registration_number' => 'required|string|max:30', 'email' => 'required|email|max:190']);
        $submission = Submission::with('applicant')->where('registration_number', strtoupper($data['registration_number']))->first();
        if ($submission && hash_equals(strtolower($submission->applicant->email), strtolower($data['email']))) {
            Notification::route('mail', $submission->applicant->email)->notify(new ApplicantMagicLinkNotification($submission));
        }

        return back()->with('success', 'Jika datanya cocok, tautan akses akan dikirim ke email terdaftar.');
    }

    public function show(Submission $submission): Response
    {
        $submission->load(['song', 'statusHistories' => fn ($query) => $query->latest(), 'revisionRequests' => fn ($query) => $query->latest()]);

        return Inertia::render('Applicant/Portal', [
            'submission' => [
                'registration_number' => $submission->registration_number,
                'submitted_at' => $submission->submitted_at,
                'status' => $submission->status->publicLabel(),
                'song' => $submission->song?->only(['title', 'genre', 'language']),
                'timeline' => $submission->statusHistories->map(fn ($history) => ['label' => SubmissionStatus::from($history->to_status)->publicLabel(), 'date' => $history->created_at]),
                'revisions' => $submission->revisionRequests->map->only(['id', 'fields', 'message', 'deadline_at', 'completed_at']),
            ],
            'revisionUrl' => URL::temporarySignedRoute('applicant.revision', now()->addMinutes(30), ['submission' => $submission->id]),
        ]);
    }

    public function revise(Request $request, Submission $submission, SubmissionStateMachine $machine): RedirectResponse
    {
        abort_unless($submission->status === SubmissionStatus::RevisionRequested, 422, 'Submission tidak sedang menunggu revisi.');
        $revision = $submission->revisionRequests()->whereNull('completed_at')->latest()->firstOrFail();
        abort_if($revision->deadline_at->isPast(), 422, 'Batas waktu revisi sudah lewat.');
        $data = $request->validate([
            'demo_url' => 'nullable|url:https|max:2000|required_without_all:video_url,file',
            'video_url' => 'nullable|url:https|max:2000|required_without_all:demo_url,file',
            'file' => 'nullable|file|max:10240|mimetypes:image/jpeg,image/png,application/pdf,audio/mpeg,audio/wav,audio/x-m4a,video/mp4,video/quicktime|required_without_all:demo_url,video_url',
            'note' => 'required|string|max:2000',
        ]);
        foreach (['demo_url' => 'demo_revision', 'video_url' => 'video_revision'] as $field => $type) {
            if (! empty($data[$field])) {
                abort_unless(collect($revision->fields)->contains(fn ($allowed) => str_contains($type, $allowed) || in_array($allowed, ['demo', 'video'], true)), 403);
                DB::table('submission_links')->insert(['submission_id' => $submission->id, 'type' => $type, 'url' => $data[$field], 'created_at' => now(), 'updated_at' => now()]);
            }
        }
        if ($file = $request->file('file')) {
            abort_unless(collect($revision->fields)->intersect(['dokumen', 'document', 'file', 'ktp', 'demo', 'video'])->isNotEmpty(), 403);
            $mime = $file->getMimeType();
            $path = $file->storeAs('submissions/'.$submission->id.'/revisions', Str::uuid().'.'.$file->guessExtension(), 'local');
            $storedFile = $submission->files()->create(['type' => 'revision', 'disk' => 'local', 'path' => $path, 'original_name' => Str::limit(basename($file->getClientOriginalName()), 200), 'mime' => $mime, 'size' => $file->getSize(), 'checksum' => hash_file('sha256', Storage::disk('local')->path($path)), 'scan_status' => 'pending']);
            ScanSubmissionFile::dispatch($storedFile->id)->afterCommit();
        }
        $revision->update(['submitted_payload' => collect($data)->except('file')->all(), 'completed_at' => now()]);
        $machine->transition($submission, SubmissionStatus::AdministrativeReview, null, 'Revisi dikirim pendaftar');

        return back()->with('success', 'Revisi berhasil dikirim dan akan diperiksa ulang.');
    }
}

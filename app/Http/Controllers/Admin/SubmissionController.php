<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SubmissionStatus;
use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\User;
use App\Services\AuditService;
use App\Services\Submission\SubmissionStateMachine;
use App\Services\Storage\GoogleDriveVideoStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class SubmissionController extends Controller
{
    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()->is_active && in_array($request->user()->role, ['super_admin', 'admin', 'program_admin', 'administrative_reviewer', 'viewer'], true), 403);
    }

    public function index(Request $request): Response
    {
        $this->authorizeAdmin($request);
        $query = Submission::with(['applicant:id,full_name,stage_name,city', 'song:id,submission_id,title,artist_name'])->where('status', '!=', 'draft')->latest('submitted_at');
        $statusMap = ['pending' => ['submitted'], 'review' => ['administrative_review', 'eligible', 'curation', 'shortlisted', 'revision_requested'], 'accepted' => ['selected'], 'rejected' => ['not_selected', 'disqualified']];
        $query->when($request->string('status')->toString(), fn ($q, $status) => $q->whereIn('status', $statusMap[$status] ?? [$status]));
        $query->when($request->string('search')->toString(), fn ($q, $search) => $q->where(fn ($inner) => $inner->where('registration_number', 'like', "%{$search}%")->orWhereHas('applicant', fn ($applicant) => $applicant->where('full_name', 'like', "%{$search}%")->orWhere('stage_name', 'like', "%{$search}%"))->orWhereHas('song', fn ($song) => $song->where('title', 'like', "%{$search}%")->orWhere('artist_name', 'like', "%{$search}%"))));

        return Inertia::render('Admin/Submissions/Index', ['submissions' => $query->paginate(20)->withQueryString(), 'filters' => $request->only(['status', 'search'])]);
    }

    public function show(Request $request, Submission $submission, AuditService $audit): Response
    {
        $this->authorizeAdmin($request);
        $submission->load(['applicant', 'song', 'files', 'links', 'consents', 'statusHistories.actor:id,name', 'revisionRequests']);
        $submission->applicant?->makeVisible('nik');
        $audit->record('submission.viewed', $submission, $request);

        return Inertia::render('Admin/Submissions/Show', ['submission' => $submission, 'statuses' => [
            ['value' => 'submitted', 'label' => 'Pending'],
            ['value' => 'administrative_review', 'label' => 'Di Review'],
            ['value' => 'selected', 'label' => 'Diterima'],
            ['value' => 'not_selected', 'label' => 'Ditolak'],
        ]]);
    }

    public function status(Request $request, Submission $submission, SubmissionStateMachine $machine, AuditService $audit): RedirectResponse
    {
        $this->authorizeAdmin($request);
        abort_if($request->user()->role === 'viewer', 403);
        $isRejected = in_array($request->string('status')->toString(), [
            SubmissionStatus::NotSelected->value,
            SubmissionStatus::Disqualified->value,
        ], true);
        $data = $request->validate([
            'status' => ['required', Rule::enum(SubmissionStatus::class)],
            'reason' => [
                $isRejected ? 'required' : 'nullable',
                'string',
                ...($isRejected ? ['min:10'] : []),
                'max:2000',
            ],
        ], [
            'reason.required' => 'Alasan wajib diisi ketika status pendaftaran diubah menjadi Ditolak.',
            'reason.min' => 'Alasan penolakan harus ditulis dengan jelas, minimal 10 karakter.',
        ]);
        $from = $submission->status->value;
        $machine->transition($submission, SubmissionStatus::from($data['status']), $request->user(), $data['reason'] ?? null);
        $audit->record('submission.status_changed', $submission, $request, ['from' => $from, 'to' => $data['status']]);

        return back()->with('success', 'Status berhasil diperbarui.');
    }

    public function updateDetails(Request $request, Submission $submission, AuditService $audit): RedirectResponse
    {
        $this->authorizeAdmin($request);
        abort_if($request->user()->role === 'viewer', 403);

        $genres = ['Alternative/Indie', 'Latin', 'Classical', 'Country', 'Blues', 'Electronic', 'Folk', 'Hip Hop/Rap', 'Jazz', 'New Age', 'Pop', 'R&B/Soul', 'Reggae', 'Rock', 'World', 'Childhood', 'Devotional/Inspirational', 'Dance', 'Soundtrack'];
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'nik' => ['required', 'digits:16'],
            'birth_place' => ['required', 'string', 'max:100'],
            'birth_date' => ['required', 'date', 'before:today'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'whatsapp' => ['required', 'regex:/^(?:\+?62|0)8[1-9][0-9]{6,11}$/'],
            'province' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'district' => ['required', 'string', 'max:100'],
            'village' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'digits:5'],
            'address' => ['required', 'string', 'max:1000'],
            'title' => ['required', 'string', 'min:2', 'max:200'],
            'artist_name' => ['required', 'string', 'min:2', 'max:150'],
            'artist_social_url' => ['required', 'url:http,https', 'max:2000'],
            'artist_spotify_url' => ['nullable', 'url:http,https', 'max:2000'],
            'songwriters' => ['required', 'array', 'min:1', 'max:20'],
            'songwriters.*.name' => ['required', 'string', 'min:2', 'max:150'],
            'songwriters.*.role' => ['required', Rule::in(['composer', 'author', 'composer_author'])],
            'genre' => ['required', Rule::in($genres)],
            'language' => ['required', 'string', 'max:80'],
            'creation_year' => ['required', 'integer', 'min:1900', 'max:'.date('Y')],
            'story' => ['required', 'string', 'min:10', 'max:5000'],
            'lyrics' => ['nullable', 'string', 'max:20000'],
            'video_url' => ['nullable', 'url:http,https', 'max:2000'],
        ]);

        $before = $submission->loadMissing(['applicant', 'song', 'links'])->snapshot;
        DB::transaction(function () use ($submission, $data): void {
            $nikHash = hash_hmac('sha256', $data['nik'], config('app.key'));
            $submission->applicant->update([
                'full_name' => $data['full_name'], 'nik' => $data['nik'], 'nik_blind_index' => $nikHash,
                'birth_place' => $data['birth_place'], 'birth_date' => $data['birth_date'],
                'email' => strtolower($data['email']), 'whatsapp' => preg_replace('/^0/', '62', preg_replace('/\D/', '', $data['whatsapp'])),
                'province' => $data['province'], 'city' => $data['city'], 'district' => $data['district'],
                'village' => $data['village'], 'postal_code' => $data['postal_code'], 'address' => $data['address'],
            ]);
            $submission->song->update([
                'title' => $data['title'], 'artist_name' => $data['artist_name'],
                'artist_social_url' => $data['artist_social_url'], 'artist_spotify_url' => $data['artist_spotify_url'] ?: null,
                'songwriters' => $data['songwriters'], 'has_cowriters' => count($data['songwriters']) > 1,
                'genre' => $data['genre'], 'language' => $data['language'], 'creation_year' => $data['creation_year'],
                'story' => $data['story'], 'lyrics' => $data['lyrics'] ?: null,
            ]);
            if (! empty($data['video_url'])) {
                $submission->links()->updateOrCreate(['type' => 'video'], ['url' => $data['video_url']]);
            } else {
                $submission->links()->where('type', 'video')->delete();
            }
            $submission->update(['snapshot' => [...($submission->snapshot ?? []), ...collect($data)->except('nik')->all()]]);
        });

        $audit->record('submission.details_updated', $submission, $request, ['previous_snapshot' => $before]);

        return back()->with('success', 'Data pendaftaran berhasil diperbarui.');
    }

    public function destroy(Request $request, Submission $submission, AuditService $audit, GoogleDriveVideoStorage $drive): RedirectResponse
    {
        $this->authorizeAdmin($request);
        abort_unless(in_array($request->user()->role, ['super_admin', 'admin'], true), 403);

        $files = $submission->files()->get(['disk', 'path']);
        $applicant = $submission->applicant;
        $registrationNumber = $submission->registration_number;

        DB::transaction(function () use ($submission, $applicant, $audit, $request, $registrationNumber): void {
            $audit->record('submission.deleted', $submission, $request, [
                'registration_number' => $registrationNumber,
            ]);
            $submission->delete();

            if ($applicant && ! $applicant->submissions()->exists()) {
                $applicant->delete();
            }
        });

        foreach ($files as $file) {
            $drive->delete($file);
        }

        return to_route('admin.submissions.index')->with('success', 'Pendaftaran berhasil dihapus permanen.');
    }

    public function requestRevision(Request $request, Submission $submission, SubmissionStateMachine $machine, AuditService $audit): RedirectResponse
    {
        $this->authorizeAdmin($request);
        abort_if($request->user()->role === 'viewer', 403);
        $data = $request->validate(['fields' => 'required|array|min:1', 'fields.*' => 'string|max:100', 'message' => 'required|string|max:2000', 'deadline_at' => 'required|date|after:now']);
        $submission->revisionRequests()->create($data + ['requested_by' => $request->user()->id]);
        $machine->transition($submission, SubmissionStatus::RevisionRequested, $request->user(), $data['message']);
        $audit->record('revision.requested', $submission, $request, ['fields' => $data['fields']]);

        return back()->with('success', 'Permintaan revisi dibuat.');
    }
}

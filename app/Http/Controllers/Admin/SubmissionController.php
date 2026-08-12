<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SubmissionStatus;
use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\User;
use App\Services\AuditService;
use App\Services\Submission\SubmissionStateMachine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $data = $request->validate(['status' => ['required', Rule::enum(SubmissionStatus::class)], 'reason' => 'required|string|max:2000']);
        $from = $submission->status->value;
        $machine->transition($submission, SubmissionStatus::from($data['status']), $request->user(), $data['reason']);
        $audit->record('submission.status_changed', $submission, $request, ['from' => $from, 'to' => $data['status']]);

        return back()->with('success', 'Status berhasil diperbarui.');
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

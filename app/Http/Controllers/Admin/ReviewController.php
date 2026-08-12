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
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ReviewController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless(in_array($request->user()->role, ['super_admin', 'admin', 'program_admin', 'curator'], true), 403);
        $assignments = DB::table('review_assignments')->join('submissions', 'submissions.id', '=', 'review_assignments.submission_id')->join('songs', 'songs.submission_id', '=', 'submissions.id')->where('reviewer_id', $request->user()->id)->select('review_assignments.*', 'submissions.registration_number', 'songs.title', 'songs.genre')->latest('assigned_at')->get();

        return Inertia::render('Admin/Reviews/Index', ['assignments' => $assignments]);
    }

    public function assign(Request $request, Submission $submission, AuditService $audit): RedirectResponse
    {
        abort_unless(in_array($request->user()->role, ['super_admin', 'admin', 'program_admin'], true), 403);
        $data = $request->validate(['reviewer_id' => 'required|exists:users,id']);
        abort_unless(User::whereKey($data['reviewer_id'])->where('role', 'curator')->where('is_active', true)->exists(), 422, 'Reviewer tidak valid.');
        DB::table('review_assignments')->updateOrInsert(['submission_id' => $submission->id, 'reviewer_id' => $data['reviewer_id']], ['status' => 'assigned', 'assigned_at' => now()]);
        $audit->record('review.assigned', $submission, $request, ['reviewer_id' => $data['reviewer_id']]);

        return back()->with('success', 'Reviewer ditugaskan.');
    }

    public function edit(Request $request, int $assignment): Response
    {
        $record = DB::table('review_assignments')->where('id', $assignment)->firstOrFail();
        abort_unless($record->reviewer_id === $request->user()->id || in_array($request->user()->role, ['super_admin', 'admin', 'program_admin'], true), 403);
        $submission = Submission::with('song')->findOrFail($record->submission_id);
        $criteria = DB::table('review_criteria')->where('program_period_id', $submission->program_period_id)->orderBy('sort_order')->get();
        $scores = DB::table('review_scores')->where('review_assignment_id', $assignment)->get()->keyBy('review_criteria_id');

        return Inertia::render('Admin/Reviews/Edit', ['assignment' => $record, 'submission' => $submission, 'criteria' => $criteria, 'scores' => $scores]);
    }

    public function score(Request $request, int $assignment): RedirectResponse
    {
        $record = DB::table('review_assignments')->where('id', $assignment)->firstOrFail();
        abort_unless($record->reviewer_id === $request->user()->id, 403);
        $submission = Submission::findOrFail($record->submission_id);
        $data = $request->validate(['scores' => 'required|array|min:1', 'scores.*.criterion_id' => 'required|integer', 'scores.*.score' => 'required|integer|min:1|max:10', 'scores.*.comment' => 'nullable|string|max:2000', 'final' => 'required|boolean']);
        $validCriteria = DB::table('review_criteria')->where('program_period_id', $submission->program_period_id)->pluck('id')->all();
        abort_unless(collect($data['scores'])->pluck('criterion_id')->diff($validCriteria)->isEmpty(), 422, 'Kriteria tidak valid.');
        DB::transaction(function () use ($data, $assignment) {
            foreach ($data['scores'] as $score) {
                DB::table('review_scores')->updateOrInsert(['review_assignment_id' => $assignment, 'review_criteria_id' => $score['criterion_id']], ['score' => $score['score'], 'comment' => $score['comment'] ?? null, 'is_final' => $data['final'], 'created_at' => now(), 'updated_at' => now()]);
            }
            DB::table('review_assignments')->where('id', $assignment)->update(['status' => $data['final'] ? 'completed' : 'draft']);
        });

        return back()->with('success', $data['final'] ? 'Penilaian difinalkan.' : 'Draft penilaian disimpan.');
    }

    public function decide(Request $request, Submission $submission, SubmissionStateMachine $machine, AuditService $audit): RedirectResponse
    {
        abort_unless(in_array($request->user()->role, ['super_admin', 'admin', 'program_admin'], true), 403);
        $data = $request->validate(['decision' => 'required|in:selected,not_selected', 'reason' => 'required|string|min:20|max:5000']);
        DB::table('review_decisions')->insert(['submission_id' => $submission->id, 'decided_by' => $request->user()->id, 'decision' => $data['decision'], 'reason' => $data['reason'], 'created_at' => now(), 'updated_at' => now()]);
        $machine->transition($submission, SubmissionStatus::from($data['decision']), $request->user(), $data['reason']);
        $audit->record('review.decided', $submission, $request, ['decision' => $data['decision']]);

        return back()->with('success', 'Keputusan final disimpan.');
    }
}

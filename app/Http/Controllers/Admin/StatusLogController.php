<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class StatusLogController extends Controller
{
    public function __invoke(Request $request): Response
    {
        abort_unless($request->user()->is_active && in_array($request->user()->role, ['super_admin', 'admin', 'program_admin', 'administrative_reviewer', 'viewer'], true), 403);

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['submitted', 'administrative_review', 'selected', 'not_selected'])],
        ]);

        $latestHistory = DB::table('status_histories')
            ->selectRaw('MAX(id) as id, submission_id')
            ->groupBy('submission_id');

        $query = DB::table('submissions')
            ->join('applicants', 'applicants.id', '=', 'submissions.applicant_id')
            ->leftJoin('songs', 'songs.submission_id', '=', 'submissions.id')
            ->leftJoinSub($latestHistory, 'latest_history', fn ($join) => $join->on('latest_history.submission_id', '=', 'submissions.id'))
            ->leftJoin('status_histories', 'status_histories.id', '=', 'latest_history.id')
            ->leftJoin('users', 'users.id', '=', 'status_histories.actor_id')
            ->where('submissions.status', '!=', 'draft')
            ->select([
                'submissions.id',
                'submissions.registration_number',
                'submissions.status',
                'submissions.submitted_at',
                'applicants.full_name',
                'applicants.email',
                'songs.title as song_title',
                'songs.artist_name',
                'status_histories.reason',
                'status_histories.created_at as status_updated_at',
                'users.name as updated_by_name',
                'users.username as updated_by_username',
            ]);

        if ($search = trim($filters['search'] ?? '')) {
            $query->where(function ($inner) use ($search): void {
                $inner->where('submissions.registration_number', 'like', "%{$search}%")
                    ->orWhere('applicants.full_name', 'like', "%{$search}%")
                    ->orWhere('applicants.email', 'like', "%{$search}%")
                    ->orWhere('songs.title', 'like', "%{$search}%")
                    ->orWhere('users.name', 'like', "%{$search}%");
            });
        }

        if ($status = $filters['status'] ?? null) {
            $query->where('submissions.status', $status);
        }

        $counts = DB::table('submissions')->where('status', '!=', 'draft')
            ->selectRaw("SUM(CASE WHEN status = 'submitted' THEN 1 ELSE 0 END) as pending")
            ->selectRaw("SUM(CASE WHEN status IN ('administrative_review','revision_requested','eligible','curation','shortlisted') THEN 1 ELSE 0 END) as review")
            ->selectRaw("SUM(CASE WHEN status = 'selected' THEN 1 ELSE 0 END) as accepted")
            ->selectRaw("SUM(CASE WHEN status IN ('not_selected','disqualified') THEN 1 ELSE 0 END) as rejected")
            ->first();

        return Inertia::render('Admin/StatusLogs/Index', [
            'logs' => $query->orderByDesc(DB::raw('COALESCE(status_histories.created_at, submissions.submitted_at)'))->paginate(20)->withQueryString(),
            'counts' => [
                'pending' => (int) ($counts->pending ?? 0),
                'review' => (int) ($counts->review ?? 0),
                'accepted' => (int) ($counts->accepted ?? 0),
                'rejected' => (int) ($counts->rejected ?? 0),
            ],
            'filters' => $filters,
        ]);
    }
}

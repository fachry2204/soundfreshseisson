<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubmissionFile;
use App\Services\AuditService;
use App\Services\Storage\GoogleDriveVideoStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TrashController extends Controller
{
    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()->is_active && in_array($request->user()->role, ['super_admin', 'admin', 'program_admin', 'administrative_reviewer', 'viewer'], true), 403);
    }

    public function index(Request $request): Response
    {
        $this->authorizeAdmin($request);
        $search = trim($request->string('search')->toString());
        $query = SubmissionFile::query()
            ->with(['submission.applicant:id,full_name,email', 'submission.song:id,submission_id,title,artist_name'])
            ->whereNotNull('trashed_at')
            ->latest('trashed_at');

        if ($search !== '') {
            $query->where(function ($inner) use ($search): void {
                $inner->where('original_name', 'like', "%{$search}%")
                    ->orWhereHas('submission', function ($submission) use ($search): void {
                        $submission->where('registration_number', 'like', "%{$search}%")
                            ->orWhereHas('applicant', fn ($applicant) => $applicant->where('full_name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                            ->orWhereHas('song', fn ($song) => $song->where('title', 'like', "%{$search}%")->orWhere('artist_name', 'like', "%{$search}%"));
                    });
            });
        }

        return Inertia::render('Admin/Trash/Index', [
            'files' => $query->paginate(20)->withQueryString(),
            'filters' => ['search' => $search],
            'total' => SubmissionFile::whereNotNull('trashed_at')->count(),
        ]);
    }

    public function destroy(Request $request, SubmissionFile $file, AuditService $audit, GoogleDriveVideoStorage $drive): RedirectResponse
    {
        $this->authorizeAdmin($request);
        abort_unless(in_array($request->user()->role, ['super_admin', 'admin'], true), 403);
        abort_unless($file->trashed_at, 422, 'File ini tidak berada di Data Terhapus.');

        $audit->record('submission_file.permanently_deleted', $file, $request, [
            'submission_id' => $file->submission_id,
            'original_name' => $file->original_name,
        ]);
        $drive->delete($file);
        $file->delete();

        return back()->with('success', 'File video berhasil dihapus permanen.');
    }
}

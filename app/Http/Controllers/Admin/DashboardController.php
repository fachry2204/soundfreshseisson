<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke()
    {
        abort_unless(auth()->user()->is_active && in_array(auth()->user()->role, ['super_admin', 'admin', 'program_admin', 'viewer'], true), 403);

        return Inertia::render('Admin/Dashboard', ['counts' => Submission::query()->selectRaw('status, count(*) total')->groupBy('status')->pluck('total', 'status'), 'latest' => Submission::with(['applicant:id,full_name', 'song:id,submission_id,title'])->latest()->limit(10)->get()]);
    }
}

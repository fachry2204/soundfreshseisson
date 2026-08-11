<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramPeriod;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PeriodController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless(in_array($request->user()->role, ['super_admin', 'program_admin', 'content_editor'], true), 403);

        return Inertia::render('Admin/Content/Periods', ['periods' => ProgramPeriod::latest('opens_at')->get()]);
    }

    public function update(Request $request, ProgramPeriod $period, AuditService $audit): RedirectResponse
    {
        abort_unless(in_array($request->user()->role, ['super_admin', 'program_admin'], true), 403);
        $data = $request->validate(['name' => 'required|string|max:150', 'opens_at' => 'required|date', 'closes_at' => 'required|date|after:opens_at', 'status' => ['required', Rule::in(['coming_soon', 'open', 'closed'])], 'quota' => 'nullable|integer|min:1']);
        $period->update($data);
        $audit->record('period.updated', $period, $request, ['status' => $data['status']]);

        return back()->with('success', 'Periode diperbarui.');
    }
}

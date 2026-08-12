<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentSection;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContentSectionController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless(in_array($request->user()->role, ['super_admin', 'admin', 'content_editor'], true), 403);

        return Inertia::render('Admin/Content/Sections', ['sections' => ContentSection::orderBy('key')->get()]);
    }

    public function update(Request $request, ContentSection $section, AuditService $audit): RedirectResponse
    {
        abort_unless(in_array($request->user()->role, ['super_admin', 'admin', 'content_editor'], true), 403);
        $data = $request->validate(['title' => 'required|string|max:250', 'body' => 'required|string|max:50000', 'is_published' => 'required|boolean']);
        $section->update(['content' => ['title' => $data['title'], 'body' => $data['body']], 'is_published' => $data['is_published'], 'revision' => $section->revision + 1]);
        $audit->record('content.updated', $section, $request, ['key' => $section->key, 'revision' => $section->revision]);

        return back()->with('success', 'Konten diperbarui.');
    }
}

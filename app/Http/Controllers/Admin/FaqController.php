<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FaqController extends Controller
{
    private function authorizeEditor(Request $request): void
    {
        abort_unless($request->user()->is_active && in_array($request->user()->role, ['super_admin', 'admin', 'content_editor'], true), 403);
    }

    public function index(Request $request): Response
    {
        $this->authorizeEditor($request);

        return Inertia::render('Admin/Content/Faqs', ['faqs' => Faq::orderBy('sort_order')->get()]);
    }

    public function store(Request $request, AuditService $audit): RedirectResponse
    {
        $this->authorizeEditor($request);
        $data = $request->validate(['question' => 'required|string|max:500', 'answer' => 'required|string|max:5000', 'sort_order' => 'required|integer|min:0|max:1000', 'is_active' => 'boolean']);
        $faq = Faq::create($data);
        $audit->record('faq.created', $faq, $request);

        return back()->with('success', 'FAQ ditambahkan.');
    }

    public function update(Request $request, Faq $faq, AuditService $audit): RedirectResponse
    {
        $this->authorizeEditor($request);
        $data = $request->validate(['question' => 'required|string|max:500', 'answer' => 'required|string|max:5000', 'sort_order' => 'required|integer|min:0|max:1000', 'is_active' => 'required|boolean']);
        $faq->update($data);
        $audit->record('faq.updated', $faq, $request);

        return back()->with('success', 'FAQ diperbarui.');
    }
}

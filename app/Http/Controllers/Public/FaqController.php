<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Inertia\Inertia;
use Inertia\Response;

class FaqController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Public/Faq', [
            'faqs' => Faq::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(['id', 'question', 'answer']),
        ]);
    }
}

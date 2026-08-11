<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ContentSection;
use Inertia\Inertia;
use Inertia\Response;

class LegalController extends Controller
{
    public function __invoke(string $type): Response
    {
        abort_unless(in_array($type, ['terms', 'privacy'], true), 404);
        $section = ContentSection::where('key', 'legal_'.$type)->where('is_published', true)->firstOrFail();

        return Inertia::render('Public/Legal', ['document' => $section->content, 'revision' => $section->revision, 'updatedAt' => $section->updated_at]);
    }
}

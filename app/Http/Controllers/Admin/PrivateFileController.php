<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubmissionFile;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PrivateFileController extends Controller
{
    public function __invoke(Request $request, SubmissionFile $file, AuditService $audit): StreamedResponse
    {
        abort_unless($request->user()->is_active && in_array($request->user()->role, ['super_admin', 'admin', 'program_admin', 'administrative_reviewer'], true), 403);
        abort_unless($file->scan_status === 'clean', 423, 'File masih dikarantina atau tidak aman.');
        abort_unless(Storage::disk($file->disk)->exists($file->path), 404);
        $audit->record('private_file.downloaded', $file, $request, ['type' => $file->type]);

        return Storage::disk($file->disk)->download($file->path, $file->original_name, ['Content-Type' => $file->mime, 'X-Content-Type-Options' => 'nosniff']);
    }
}

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
        // File lama dapat tetap berstatus pending saat worker antivirus tidak
        // tersedia di hosting. Admin tetap boleh mengambil file tersebut,
        // sementara hasil scan gagal atau terinfeksi tetap diblokir.
        abort_unless(in_array($file->scan_status, ['clean', 'pending'], true), 423, 'File tidak aman atau gagal dipindai.');
        abort_unless(Storage::disk($file->disk)->exists($file->path), 404);
        $audit->record('private_file.downloaded', $file, $request, [
            'type' => $file->type,
            'scan_status' => $file->scan_status,
        ]);

        $headers = ['Content-Type' => $file->mime, 'X-Content-Type-Options' => 'nosniff'];

        if ($request->boolean('view')) {
            return Storage::disk($file->disk)->response($file->path, $file->original_name, $headers);
        }

        return Storage::disk($file->disk)->download($file->path, $file->original_name, $headers);
    }
}

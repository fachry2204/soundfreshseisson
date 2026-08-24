<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubmissionFile;
use App\Services\AuditService;
use App\Services\Storage\GoogleDriveVideoStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class PrivateFileController extends Controller
{
    public function __invoke(Request $request, SubmissionFile $file, AuditService $audit, GoogleDriveVideoStorage $drive): Response
    {
        abort_unless($request->user()->is_active && in_array($request->user()->role, ['super_admin', 'admin', 'program_admin', 'administrative_reviewer'], true), 403);
        // File lama dapat tetap berstatus pending saat worker antivirus tidak
        // tersedia di hosting. Admin tetap boleh mengambil file tersebut,
        // sementara hasil scan gagal atau terinfeksi tetap diblokir.
        abort_unless(in_array($file->scan_status, ['clean', 'pending'], true), 423, 'File tidak aman atau gagal dipindai.');
        if ($file->disk !== 'gdrive') {
            abort_unless(Storage::disk($file->disk)->exists($file->path), 404);
        }
        if (! $request->boolean('view')) {
            $file->update([
                'downloaded_at' => now(),
                'downloaded_by' => $request->user()->id,
            ]);
        }
        $audit->record('private_file.downloaded', $file, $request, [
            'type' => $file->type,
            'scan_status' => $file->scan_status,
            'downloaded_at' => $file->downloaded_at?->toIso8601String(),
        ]);

        $headers = ['Content-Type' => $file->mime, 'X-Content-Type-Options' => 'nosniff'];

        if ($file->disk === 'gdrive') {
            try {
                if ($file->type === 'video') {
                    return redirect()->away($drive->downloadUrl($file));
                }

                $temporaryPath = $drive->downloadToTemporary($file);
                $response = $request->boolean('view')
                    ? response()->file($temporaryPath, $headers)
                    : response()->download($temporaryPath, $file->original_name, $headers);

                return $response->deleteFileAfterSend(true);
            } catch (\Throwable $exception) {
                report($exception);
                abort(502, 'File Google Drive belum dapat dibuka. Periksa koneksi rclone dan izin file.');
            }
        }

        if ($request->boolean('view')) {
            return Storage::disk($file->disk)->response($file->path, $file->original_name, $headers);
        }

        return Storage::disk($file->disk)->download($file->path, $file->original_name, $headers);
    }
}

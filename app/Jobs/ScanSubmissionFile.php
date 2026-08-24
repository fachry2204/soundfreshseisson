<?php

namespace App\Jobs;

use App\Models\SubmissionFile;
use App\Services\Files\ClamAvScanner;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ScanSubmissionFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public string $fileId) {}

    public function backoff(): array
    {
        return [30, 120, 600];
    }

    public function handle(ClamAvScanner $scanner): void
    {
        $file = SubmissionFile::findOrFail($this->fileId);
        // File dapat sudah dipindahkan ke Drive oleh proses pasca-response.
        // Scanner lokal tidak boleh mencoba membuka disk remote sebagai path lokal.
        if ($file->disk !== 'local') {
            return;
        }
        abort_unless(Storage::disk($file->disk)->exists($file->path), 404);
        $result = $scanner->scan(Storage::disk($file->disk)->path($file->path));
        $file->update(['scan_status' => $result]);
        if ($result === 'infected') {
            Storage::disk($file->disk)->move($file->path, 'quarantine/'.$file->id);
            $file->update(['path' => 'quarantine/'.$file->id]);
        }
    }
}

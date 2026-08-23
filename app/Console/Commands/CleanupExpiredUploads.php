<?php

namespace App\Console\Commands;

use App\Models\UploadSession;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupExpiredUploads extends Command
{
    protected $signature = 'uploads:cleanup';

    protected $description = 'Hapus chunk dan file sementara dari sesi upload kedaluwarsa';

    public function handle(): int
    {
        $count = 0;

        // Terminal sessions must never retain chunk parts, even when the
        // scheduler was not running when the upload originally completed.
        UploadSession::whereIn('status', ['completed', 'claimed', 'cancelled', 'failed'])->chunkById(100, function ($sessions) use (&$count) {
            foreach ($sessions as $session) {
                $disk = Storage::disk('local');
                $directory = "uploads/chunks/{$session->id}";
                if ($disk->directoryExists($directory)) {
                    $disk->delete($disk->allFiles($directory));
                    $disk->deleteDirectory($directory);
                    $count++;
                }

                // Claimed files already live under submissions/{id}. Any path
                // still recorded here is a legacy temporary duplicate.
                $temporaryFileIsDisposable = $session->claimed_by_submission_id !== null
                    || in_array($session->status, ['claimed', 'cancelled', 'failed'], true);
                if ($temporaryFileIsDisposable && $session->path) {
                    if ($disk->exists($session->path)) {
                        $disk->delete($session->path);
                        $count++;
                    }
                    $session->update(['path' => null, 'received_chunks' => []]);
                }
            }
        });

        UploadSession::whereNull('claimed_by_submission_id')->where('expires_at', '<', now())->whereNotIn('status', ['claimed', 'expired'])->chunkById(100, function ($sessions) use (&$count) {
            foreach ($sessions as $session) {
                Storage::disk('local')->deleteDirectory("uploads/chunks/{$session->id}");
                if ($session->path) {
                    Storage::disk('local')->delete($session->path);
                }
                $session->update(['status' => 'expired', 'path' => null]);
                $count++;
            }
        });
        $this->info("{$count} folder/file upload sementara dibersihkan.");

        return self::SUCCESS;
    }
}

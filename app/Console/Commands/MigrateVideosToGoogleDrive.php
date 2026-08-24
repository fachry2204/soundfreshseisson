<?php

namespace App\Console\Commands;

use App\Models\SubmissionFile;
use App\Services\Storage\GoogleDriveVideoStorage;
use Illuminate\Console\Command;

class MigrateVideosToGoogleDrive extends Command
{
    protected $signature = 'videos:migrate-to-drive {--id=} {--retry-failed}';
    protected $description = 'Pindahkan video submission lokal ke Google Drive melalui rclone';

    public function handle(GoogleDriveVideoStorage $storage): int
    {
        if (! $storage->enabled()) {
            $this->error('Google Drive belum diaktifkan di menu Setting.');
            return self::FAILURE;
        }
        $query = SubmissionFile::query()->where('type', 'video')->where('disk', 'local')->whereNull('trashed_at');
        if ($this->option('id')) $query->whereKey($this->option('id'));
        if (! $this->option('retry-failed')) $query->whereNot('storage_status', 'failed');
        $failed = 0;
        $query->with('submission:id,registration_number')->eachById(function (SubmissionFile $file) use ($storage, &$failed): void {
            try {
                $storage->transfer($file);
                $this->info("OK {$file->submission->registration_number}: {$file->original_name}");
            } catch (\Throwable $exception) {
                $failed++;
                $this->error("GAGAL {$file->id}: {$exception->getMessage()}");
            }
        });
        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }
}

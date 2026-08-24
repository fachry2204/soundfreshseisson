<?php

namespace App\Jobs;

use App\Models\SubmissionFile;
use App\Services\Storage\GoogleDriveVideoStorage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TransferSubmissionVideoToGoogleDrive implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 3600;

    public function __construct(public string $fileId) {}

    public function handle(GoogleDriveVideoStorage $storage): void
    {
        $file = SubmissionFile::find($this->fileId);
        if ($file) {
            $storage->transfer($file);
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Models\SubmissionFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class RenameSubmissionVideoFiles extends Command
{
    protected $signature = 'submissions:rename-video-files';

    protected $description = 'Ubah nama file video lama agar mengikuti nomor pendaftaran';

    public function handle(): int
    {
        $renamed = 0;

        SubmissionFile::query()
            ->with('submission:id,registration_number')
            ->where('type', 'video')
            ->chunkById(100, function ($files) use (&$renamed) {
                foreach ($files as $file) {
                    if (! $file->submission?->registration_number) {
                        continue;
                    }

                    $extension = match ($file->mime) {
                        'video/quicktime' => 'mov',
                        'video/webm' => 'webm',
                        default => 'mp4',
                    };
                    $finalName = $file->submission->registration_number.'-video.'.$extension;
                    $directory = str_replace('\\', '/', dirname($file->path));
                    $destination = $directory.'/'.$finalName;
                    $disk = Storage::disk($file->disk);

                    if ($file->path !== $destination && $disk->exists($file->path)) {
                        if ($disk->exists($destination)) {
                            $disk->delete($destination);
                        }
                        if (! $disk->move($file->path, $destination)) {
                            $this->error("Gagal mengganti nama {$file->path}.");
                            continue;
                        }
                    }

                    $file->update(['path' => $destination, 'original_name' => $finalName]);
                    $renamed++;
                }
            });

        $this->info("{$renamed} file video disesuaikan dengan nomor pendaftaran.");

        return self::SUCCESS;
    }
}

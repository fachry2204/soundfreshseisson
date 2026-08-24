<?php

namespace App\Services\Storage;

use App\Models\AppSetting;
use App\Models\SubmissionFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\Process\Process;

final class GoogleDriveVideoStorage
{
    public function enabled(): bool
    {
        return filter_var(AppSetting::valueFor('drive.enabled', '0'), FILTER_VALIDATE_BOOLEAN);
    }

    public function transfer(SubmissionFile $file): void
    {
        $file->loadMissing('submission:id,registration_number');
        if (! $this->enabled() || $file->type !== 'video' || $file->disk !== 'local' || $file->trashed_at) {
            return;
        }

        $local = Storage::disk('local');
        if (! $local->exists($file->path)) {
            throw new RuntimeException('File video lokal tidak ditemukan: '.$file->path);
        }

        $remotePath = $this->remotePath($file);
        $file->update(['storage_status' => 'transferring', 'transfer_error' => null]);

        try {
            $this->run(['copyto', $local->path($file->path), $this->remote($remotePath), '--check-first']);
            $size = $this->remoteSize($remotePath);
            if ($size !== (int) $file->size) {
                throw new RuntimeException("Ukuran file Drive {$size} byte tidak sama dengan file lokal {$file->size} byte.");
            }

            $url = trim($this->run(['link', $this->remote($remotePath)], allowFailure: true));
            if ($url === '' || ! filter_var($url, FILTER_VALIDATE_URL)) {
                $url = null;
            }

            if (! $local->delete($file->path) || $local->exists($file->path)) {
                throw new RuntimeException('File sudah tersimpan di Drive, tetapi salinan lokal gagal dihapus.');
            }
            // KTP dan berkas lain berada di folder submission yang sama.
            // Jangan menghapus folder kecuali sudah benar-benar kosong.
            $directory = dirname($file->path);
            if ($local->allFiles($directory) === []) {
                $local->deleteDirectory($directory);
            }

            $file->update([
                'disk' => 'gdrive',
                'path' => $remotePath,
                'remote_url' => $url,
                'storage_status' => 'remote',
                'transferred_at' => now(),
                'transfer_error' => null,
            ]);
        } catch (\Throwable $exception) {
            $file->update(['storage_status' => 'failed', 'transfer_error' => mb_substr($exception->getMessage(), 0, 2000)]);
            throw $exception;
        }
    }

    public function testConnection(): void
    {
        $this->run(['mkdir', $this->remote($this->basePath())]);
        $this->run(['lsd', $this->remote($this->basePath())]);
    }

    public function downloadUrl(SubmissionFile $file): string
    {
        if ($file->disk !== 'gdrive') {
            throw new RuntimeException('File bukan file Google Drive.');
        }
        if (filled($file->remote_url)) {
            return $file->remote_url;
        }
        $url = trim($this->run(['link', $this->remote($file->path)]));
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            throw new RuntimeException('Google Drive tidak menghasilkan tautan file yang valid.');
        }
        $file->update(['remote_url' => $url]);

        return $url;
    }

    public function moveToTrash(SubmissionFile $file): string
    {
        $trashPath = $this->basePath().'/Trash/Rejected/'.$file->submission_id.'/'.basename($file->path);
        $this->run(['moveto', $this->remote($file->path), $this->remote($trashPath)]);
        return $trashPath;
    }

    public function delete(SubmissionFile $file): void
    {
        if ($file->disk === 'gdrive') {
            $this->run(['deletefile', $this->remote($file->path)]);
            return;
        }
        Storage::disk($file->disk)->delete($file->path);
    }

    private function remoteSize(string $path): int
    {
        $decoded = json_decode($this->run(['size', $this->remote($path), '--json']), true, flags: JSON_THROW_ON_ERROR);
        return (int) ($decoded['bytes'] ?? -1);
    }

    private function remotePath(SubmissionFile $file): string
    {
        $number = preg_replace('/[^A-Za-z0-9_-]/', '-', $file->submission->registration_number);
        $extension = strtolower(pathinfo($file->path, PATHINFO_EXTENSION));
        $filename = $number.'-video'.($extension !== '' ? '.'.$extension : '');

        return $this->basePath().'/Submissions/'.$number.'/'.$filename;
    }

    private function remote(string $path): string
    {
        $remote = (string) AppSetting::valueFor('drive.remote', config('google-drive.remote'));
        if (! preg_match('/^[A-Za-z0-9_-]+$/', $remote)) {
            throw new RuntimeException('Nama remote rclone tidak valid.');
        }
        return $remote.':'.ltrim(str_replace('\\', '/', $path), '/');
    }

    private function basePath(): string
    {
        $path = trim((string) AppSetting::valueFor('drive.base_path', config('google-drive.base_path')), " /\\\t\n\r\0\x0B");
        return $path !== '' ? $path : 'Original Sessions';
    }

    private function run(array $arguments, bool $allowFailure = false): string
    {
        $binary = (string) AppSetting::valueFor('drive.binary', config('google-drive.binary'));
        $config = (string) AppSetting::valueFor('drive.config_path', config('google-drive.config_path'));
        $command = [$binary];
        if ($config !== '') {
            $command[] = '--config';
            $command[] = $config;
        }
        $command = [...$command, ...$arguments, '--stats=0', '--log-level=ERROR'];
        $process = new Process($command, base_path(), null, null, (float) config('google-drive.timeout', 3600));
        $process->run();
        if (! $process->isSuccessful() && ! $allowFailure) {
            throw new RuntimeException(trim($process->getErrorOutput() ?: $process->getOutput()) ?: 'Perintah rclone gagal dijalankan.');
        }
        return $process->getOutput();
    }
}

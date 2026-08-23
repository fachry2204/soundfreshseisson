<?php

namespace Tests\Feature;

use App\Models\UploadSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CleanupCompletedUploadChunksTest extends TestCase
{
    use RefreshDatabase;

    public function test_cleanup_removes_leftover_chunks_from_completed_sessions(): void
    {
        Storage::fake('local');
        $upload = UploadSession::create([
            'token_hash' => hash('sha256', 'token'),
            'type' => 'video',
            'original_name' => 'video.mp4',
            'declared_mime' => 'video/mp4',
            'detected_mime' => 'video/mp4',
            'expected_size' => 4,
            'chunk_size' => 4,
            'total_chunks' => 1,
            'expected_checksum' => hash('sha256', 'test'),
            'actual_checksum' => hash('sha256', 'test'),
            'received_chunks' => [0],
            'path' => "uploads/completed/test",
            'status' => 'completed',
            'expires_at' => now()->addHour(),
        ]);
        Storage::disk('local')->put("uploads/chunks/{$upload->id}/0.part", 'test');

        $this->artisan('uploads:cleanup')->assertSuccessful();

        $this->assertFalse(Storage::disk('local')->directoryExists("uploads/chunks/{$upload->id}"));
    }
}

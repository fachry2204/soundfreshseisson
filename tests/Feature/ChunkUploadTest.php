<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ChunkUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_chunks_require_capability_token_and_complete_with_checksum(): void
    {
        Storage::fake('local');
        $bytes = 'RIFF'.pack('V', 262136).'WAVEfmt '.pack('VvvVVvv', 16, 1, 1, 8000, 16000, 2, 16).'data'.pack('V', 262100).str_repeat("\0", 262100);
        $this->assertSame(262144, strlen($bytes));
        $checksum = hash('sha256', $bytes);
        $init = $this->postJson('/registration/uploads/init', ['type' => 'demo', 'name' => '../demo.wav', 'mime' => 'audio/wav', 'size' => strlen($bytes), 'chunk_size' => 262144, 'checksum' => $checksum])->assertCreated()->json();

        $chunk = UploadedFile::fake()->createWithContent('chunk.part', $bytes);
        $this->withHeader('X-Upload-Token', 'wrong')->post("/registration/uploads/{$init['id']}/chunk", ['index' => 0, 'chunk' => $chunk])->assertForbidden();
        $chunk = UploadedFile::fake()->createWithContent('chunk.part', $bytes);
        $this->withHeader('X-Upload-Token', $init['token'])->post("/registration/uploads/{$init['id']}/chunk", ['index' => 0, 'chunk' => $chunk])->assertOk();
        $this->withHeader('X-Upload-Token', $init['token'])->postJson("/registration/uploads/{$init['id']}/complete")->assertOk()->assertJson(['status' => 'completed', 'checksum' => $checksum]);

        $this->assertDatabaseHas('upload_sessions', ['id' => $init['id'], 'status' => 'completed', 'actual_checksum' => $checksum, 'original_name' => 'demo.wav']);
    }

    public function test_checksum_mismatch_marks_upload_failed(): void
    {
        Storage::fake('local');
        $bytes = 'RIFF'.str_repeat("\0", 262140);
        $init = $this->postJson('/registration/uploads/init', ['type' => 'demo', 'name' => 'demo.wav', 'mime' => 'audio/wav', 'size' => strlen($bytes), 'chunk_size' => 262144, 'checksum' => str_repeat('a', 64)])->assertCreated()->json();
        $chunk = UploadedFile::fake()->createWithContent('chunk.part', $bytes);
        $this->withHeader('X-Upload-Token', $init['token'])->post("/registration/uploads/{$init['id']}/chunk", ['index' => 0, 'chunk' => $chunk])->assertOk();
        $this->withHeader('X-Upload-Token', $init['token'])->postJson("/registration/uploads/{$init['id']}/complete")->assertUnprocessable();
        $this->assertDatabaseHas('upload_sessions', ['id' => $init['id'], 'status' => 'failed']);
    }

    public function test_video_upload_rejects_non_video_mime_type(): void
    {
        $this->postJson('/registration/uploads/init', [
            'type' => 'video',
            'name' => 'bukan-video.mp3',
            'mime' => 'audio/mpeg',
            'size' => 262144,
            'chunk_size' => 262144,
            'checksum' => str_repeat('a', 64),
        ])->assertUnprocessable()
            ->assertSee('File yang diupload harus berformat video');
    }
}

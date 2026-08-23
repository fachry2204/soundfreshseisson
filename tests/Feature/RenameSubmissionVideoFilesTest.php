<?php

namespace Tests\Feature;

use App\Models\Applicant;
use App\Models\ProgramPeriod;
use App\Models\Submission;
use App\Models\SubmissionFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RenameSubmissionVideoFilesTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_renames_existing_video_using_registration_number(): void
    {
        Storage::fake('local');
        $period = ProgramPeriod::create(['name' => 'Test', 'slug' => 'rename-video', 'opens_at' => now(), 'closes_at' => now()->addDay(), 'status' => 'open']);
        $applicant = Applicant::create(['full_name' => 'Peserta', 'nik' => '1234567890123456', 'nik_blind_index' => hash('sha256', 'rename'), 'birth_place' => 'Jakarta', 'birth_date' => '1990-01-01', 'email' => 'rename@example.test', 'whatsapp' => '628123456789', 'province' => 'DKI', 'city' => 'Jakarta', 'address' => 'Alamat']);
        $submission = Submission::create(['program_period_id' => $period->id, 'applicant_id' => $applicant->id, 'registration_number' => 'OS-2026-000344', 'status' => 'submitted', 'draft_token_hash' => hash('sha256', 'draft')]);
        $file = SubmissionFile::create(['submission_id' => $submission->id, 'disk' => 'local', 'path' => "submissions/{$submission->id}/old-name.mp4", 'type' => 'video', 'original_name' => 'old-name.mp4', 'mime' => 'video/mp4', 'size' => 5, 'checksum' => str_repeat('a', 64), 'scan_status' => 'clean']);
        Storage::disk('local')->put($file->path, 'video');

        $this->artisan('submissions:rename-video-files')->assertSuccessful();

        $destination = "submissions/{$submission->id}/OS-2026-000344-video.mp4";
        Storage::disk('local')->assertMissing("submissions/{$submission->id}/old-name.mp4");
        Storage::disk('local')->assertExists($destination);
        $this->assertSame($destination, $file->fresh()->path);
        $this->assertSame('OS-2026-000344-video.mp4', $file->fresh()->original_name);
    }
}

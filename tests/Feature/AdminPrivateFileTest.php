<?php

namespace Tests\Feature;

use App\Models\Applicant;
use App\Models\ProgramPeriod;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminPrivateFileTest extends TestCase
{
    use RefreshDatabase;

    public function test_viewer_cannot_download_private_identity_file(): void
    {
        Storage::fake('local');
        $viewer = User::factory()->create(['role' => 'viewer']);
        $period = ProgramPeriod::create(['name' => 'Test', 'slug' => 'test', 'opens_at' => now()->subDay(), 'closes_at' => now()->addDay(), 'status' => 'open']);
        $applicant = Applicant::create(['full_name' => 'Test', 'nik' => '1234567890123456', 'nik_blind_index' => hash('sha256', 'nik'), 'birth_place' => 'Jakarta', 'birth_date' => '1990-01-01', 'email' => 'test@example.test', 'whatsapp' => '628123456789', 'province' => 'DKI', 'city' => 'Jakarta', 'address' => 'Alamat']);
        $submission = Submission::create(['program_period_id' => $period->id, 'applicant_id' => $applicant->id, 'status' => 'submitted', 'draft_token_hash' => hash('sha256', 'draft')]);
        $file = SubmissionFile::create(['submission_id' => $submission->id, 'disk' => 'local', 'path' => 'secret/ktp.pdf', 'type' => 'ktp', 'original_name' => 'ktp.pdf', 'mime' => 'application/pdf', 'size' => 4, 'checksum' => str_repeat('a', 64)]);
        Storage::disk('local')->put($file->path, 'test');

        $this->actingAs($viewer)->get('/admin/files/'.$file->id)->assertForbidden();
    }

    public function test_admin_can_view_clean_private_file_inline(): void
    {
        Storage::fake('local');
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $period = ProgramPeriod::create(['name' => 'Test', 'slug' => 'test', 'opens_at' => now()->subDay(), 'closes_at' => now()->addDay(), 'status' => 'open']);
        $applicant = Applicant::create(['full_name' => 'Test', 'nik' => '1234567890123456', 'nik_blind_index' => hash('sha256', 'nik'), 'birth_place' => 'Jakarta', 'birth_date' => '1990-01-01', 'email' => 'test@example.test', 'whatsapp' => '628123456789', 'province' => 'DKI', 'city' => 'Jakarta', 'address' => 'Alamat']);
        $submission = Submission::create(['program_period_id' => $period->id, 'applicant_id' => $applicant->id, 'status' => 'submitted', 'draft_token_hash' => hash('sha256', 'draft')]);
        $file = SubmissionFile::create(['submission_id' => $submission->id, 'disk' => 'local', 'path' => 'secret/ktp.pdf', 'type' => 'ktp', 'original_name' => 'ktp.pdf', 'mime' => 'application/pdf', 'size' => 4, 'checksum' => str_repeat('a', 64), 'scan_status' => 'clean']);
        Storage::disk('local')->put($file->path, 'test');

        $this->actingAs($admin)
            ->get('/admin/files/'.$file->id.'?view=1')
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_can_download_pending_private_file(): void
    {
        Storage::fake('local');
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $period = ProgramPeriod::create(['name' => 'Test', 'slug' => 'pending-test', 'opens_at' => now()->subDay(), 'closes_at' => now()->addDay(), 'status' => 'open']);
        $applicant = Applicant::create(['full_name' => 'Test', 'nik' => '2234567890123456', 'nik_blind_index' => hash('sha256', 'pending-nik'), 'birth_place' => 'Jakarta', 'birth_date' => '1990-01-01', 'email' => 'pending@example.test', 'whatsapp' => '628123456789', 'province' => 'DKI', 'city' => 'Jakarta', 'address' => 'Alamat']);
        $submission = Submission::create(['program_period_id' => $period->id, 'applicant_id' => $applicant->id, 'status' => 'submitted', 'draft_token_hash' => hash('sha256', 'pending-draft')]);
        $file = SubmissionFile::create(['submission_id' => $submission->id, 'disk' => 'local', 'path' => 'secret/video.mp4', 'type' => 'video', 'original_name' => 'video.mp4', 'mime' => 'video/mp4', 'size' => 5, 'checksum' => str_repeat('b', 64), 'scan_status' => 'pending']);
        Storage::disk('local')->put($file->path, 'video');

        $this->actingAs($admin)
            ->get('/admin/files/'.$file->id)
            ->assertOk()
            ->assertDownload('video.mp4');
    }

    public function test_admin_cannot_download_infected_private_file(): void
    {
        Storage::fake('local');
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $period = ProgramPeriod::create(['name' => 'Test', 'slug' => 'infected-test', 'opens_at' => now()->subDay(), 'closes_at' => now()->addDay(), 'status' => 'open']);
        $applicant = Applicant::create(['full_name' => 'Test', 'nik' => '3234567890123456', 'nik_blind_index' => hash('sha256', 'infected-nik'), 'birth_place' => 'Jakarta', 'birth_date' => '1990-01-01', 'email' => 'infected@example.test', 'whatsapp' => '628123456789', 'province' => 'DKI', 'city' => 'Jakarta', 'address' => 'Alamat']);
        $submission = Submission::create(['program_period_id' => $period->id, 'applicant_id' => $applicant->id, 'status' => 'submitted', 'draft_token_hash' => hash('sha256', 'infected-draft')]);
        $file = SubmissionFile::create(['submission_id' => $submission->id, 'disk' => 'local', 'path' => 'secret/infected.mp4', 'type' => 'video', 'original_name' => 'infected.mp4', 'mime' => 'video/mp4', 'size' => 5, 'checksum' => str_repeat('c', 64), 'scan_status' => 'infected']);
        Storage::disk('local')->put($file->path, 'video');

        $this->actingAs($admin)
            ->get('/admin/files/'.$file->id)
            ->assertStatus(423);
    }
}

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

class AdminSubmissionDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_submission_and_its_stored_files(): void
    {
        Storage::fake('local');
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        [$submission, $file, $applicant] = $this->makeSubmission();
        Storage::disk('local')->put($file->path, 'video');

        $this->actingAs($admin)
            ->delete("/admin/submissions/{$submission->id}")
            ->assertRedirect('/admin/submissions')
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('submissions', ['id' => $submission->id]);
        $this->assertDatabaseMissing('applicants', ['id' => $applicant->id]);
        Storage::disk('local')->assertMissing($file->path);
    }

    public function test_non_privileged_admin_role_cannot_delete_submission(): void
    {
        $reviewer = User::factory()->create(['role' => 'administrative_reviewer', 'is_active' => true]);
        [$submission] = $this->makeSubmission();

        $this->actingAs($reviewer)
            ->delete("/admin/submissions/{$submission->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('submissions', ['id' => $submission->id]);
    }

    private function makeSubmission(): array
    {
        $period = ProgramPeriod::create(['name' => 'Test', 'slug' => 'delete-'.str()->random(8), 'opens_at' => now()->subDay(), 'closes_at' => now()->addDay(), 'status' => 'open']);
        $applicant = Applicant::create(['full_name' => 'Test', 'nik' => '1234567890123456', 'nik_blind_index' => hash('sha256', str()->random()), 'birth_place' => 'Jakarta', 'birth_date' => '1990-01-01', 'email' => str()->random().'@example.test', 'whatsapp' => '628123456789', 'province' => 'DKI', 'city' => 'Jakarta', 'address' => 'Alamat']);
        $submission = Submission::create(['program_period_id' => $period->id, 'applicant_id' => $applicant->id, 'status' => 'submitted', 'draft_token_hash' => hash('sha256', str()->random())]);
        $file = SubmissionFile::create(['submission_id' => $submission->id, 'disk' => 'local', 'path' => 'secret/'.str()->random().'.mp4', 'type' => 'video', 'original_name' => 'video.mp4', 'mime' => 'video/mp4', 'size' => 5, 'checksum' => str_repeat('a', 64), 'scan_status' => 'clean']);

        return [$submission, $file, $applicant];
    }
}

<?php

namespace Tests\Feature;

use App\Models\Applicant;
use App\Models\ProgramPeriod;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class ApplicantRevisionTest extends TestCase
{
    use RefreshDatabase;

    public function test_applicant_can_submit_only_requested_revision_through_signed_url(): void
    {
        Bus::fake();
        $period = ProgramPeriod::create(['name' => 'Test', 'slug' => 'test', 'opens_at' => now()->subDay(), 'closes_at' => now()->addDay(), 'status' => 'open']);
        $applicant = Applicant::create(['full_name' => 'Musisi', 'nik' => '1234567890123456', 'nik_blind_index' => hash('sha256', 'nik'), 'birth_place' => 'Jakarta', 'birth_date' => '1990-01-01', 'email' => 'musisi@example.test', 'whatsapp' => '628123456789', 'province' => 'DKI', 'city' => 'Jakarta', 'address' => 'Alamat']);
        $submission = Submission::create(['program_period_id' => $period->id, 'applicant_id' => $applicant->id, 'registration_number' => 'OS-2026-000001', 'status' => 'revision_requested', 'draft_token_hash' => hash('sha256', 'draft')]);
        $admin = User::factory()->create(['role' => 'program_admin']);
        $revision = $submission->revisionRequests()->create(['requested_by' => $admin->id, 'fields' => ['demo'], 'message' => 'Mohon perbarui demo.', 'deadline_at' => now()->addDay()]);
        $url = URL::temporarySignedRoute('applicant.revision', now()->addMinute(), ['submission' => $submission->id]);

        $this->post($url, ['demo_url' => 'https://drive.google.com/demo-baru', 'note' => 'Demo sudah diperbarui.'])->assertRedirect();

        $this->assertSame('administrative_review', $submission->fresh()->status->value);
        $this->assertNotNull($revision->fresh()->completed_at);
        $this->assertDatabaseHas('submission_links', ['submission_id' => $submission->id, 'type' => 'demo_revision']);
        $this->assertDatabaseHas('status_histories', ['submission_id' => $submission->id, 'from_status' => 'revision_requested', 'to_status' => 'administrative_review']);
    }

    public function test_unsigned_revision_is_rejected(): void
    {
        $period = ProgramPeriod::create(['name' => 'Test', 'slug' => 'unsigned', 'opens_at' => now()->subDay(), 'closes_at' => now()->addDay(), 'status' => 'open']);
        $applicant = Applicant::create(['full_name' => 'Musisi', 'nik' => '1234567890123456', 'nik_blind_index' => hash('sha256', 'unsigned'), 'birth_place' => 'Jakarta', 'birth_date' => '1990-01-01', 'email' => 'unsigned@example.test', 'whatsapp' => '628123456789', 'province' => 'DKI', 'city' => 'Jakarta', 'address' => 'Alamat']);
        $submission = Submission::create(['program_period_id' => $period->id, 'applicant_id' => $applicant->id, 'status' => 'revision_requested', 'draft_token_hash' => hash('sha256', 'unsigned')]);
        $this->post("/portal/{$submission->id}/revision")->assertForbidden();
    }
}

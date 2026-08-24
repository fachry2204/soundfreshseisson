<?php

namespace Tests\Feature;

use App\Enums\SubmissionStatus;
use App\Models\Applicant;
use App\Models\ProgramPeriod;
use App\Models\Submission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Tests\TestCase;

class ApplicantPortalTest extends TestCase
{
    use RefreshDatabase;

    private function submission(): Submission
    {
        $period = ProgramPeriod::create(['name' => 'Test', 'slug' => 'test', 'opens_at' => now()->subDay(), 'closes_at' => now()->addDay(), 'status' => 'open']);
        $applicant = Applicant::create(['full_name' => 'Musisi Test', 'nik' => '1234567890123456', 'nik_blind_index' => hash('sha256', 'nik'), 'birth_place' => 'Jakarta', 'birth_date' => '1990-01-01', 'email' => 'musisi@example.test', 'whatsapp' => '628123456789', 'province' => 'DKI Jakarta', 'city' => 'Jakarta', 'address' => 'Alamat']);
        $submission = Submission::create(['program_period_id' => $period->id, 'applicant_id' => $applicant->id, 'registration_number' => 'OS-2026-000001', 'status' => SubmissionStatus::Submitted, 'draft_token_hash' => hash('sha256', Str::random(64)), 'submitted_at' => now()]);
        $submission->song()->create(['title' => 'Lagu Test', 'genre' => 'Pop', 'language' => 'Indonesia', 'creation_year' => 2026, 'story' => str_repeat('Cerita lagu. ', 8)]);

        return $submission;
    }

    public function test_matching_identity_displays_submission_directly(): void
    {
        $submission = $this->submission();

        $this->post('/tracking/check', ['registration_number' => $submission->registration_number, 'email' => 'musisi@example.test'])
            ->assertRedirect('/tracking')
            ->assertSessionHas('tracking_submission_id', $submission->id);

        $this->get('/tracking')->assertInertia(fn ($page) => $page
            ->component('Applicant/RequestLink')
            ->where('submission.registration_number', 'OS-2026-000001')
            ->where('submission.applicant.full_name', 'Musisi Test')
            ->where('submission.song.title', 'Lagu Test')
            ->where('submission.status', 'Pendaftaran Diterima'));
    }

    public function test_mismatched_identity_shows_informative_error_without_data(): void
    {
        $submission = $this->submission();

        $this->withSession(['tracking_submission_id' => $submission->id])
            ->post('/tracking/check', ['registration_number' => 'OS-2026-999999', 'email' => 'nobody@example.test'])
            ->assertSessionHasErrors('lookup')
            ->assertSessionMissing('tracking_submission_id');
    }

    public function test_portal_rejects_unsigned_url_and_accepts_valid_signature(): void
    {
        $submission = $this->submission();
        $this->get('/portal/'.$submission->id)->assertForbidden();
        $url = URL::temporarySignedRoute('applicant.portal', now()->addMinute(), ['submission' => $submission->id]);
        $this->get($url)->assertOk();
    }
}

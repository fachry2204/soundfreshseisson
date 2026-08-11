<?php

namespace Tests\Feature;

use App\Enums\SubmissionStatus;
use App\Models\Applicant;
use App\Models\ProgramPeriod;
use App\Models\Submission;
use App\Notifications\ApplicantMagicLinkNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
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

    public function test_matching_identity_queues_magic_link_without_leaking_result(): void
    {
        Notification::fake();
        $submission = $this->submission();

        $this->post('/tracking/magic-link', ['registration_number' => $submission->registration_number, 'email' => 'musisi@example.test'])
            ->assertSessionHas('success');
        Notification::assertSentOnDemand(ApplicantMagicLinkNotification::class);

        $this->post('/tracking/magic-link', ['registration_number' => 'OS-2026-999999', 'email' => 'nobody@example.test'])
            ->assertSessionHas('success');
    }

    public function test_portal_rejects_unsigned_url_and_accepts_valid_signature(): void
    {
        $submission = $this->submission();
        $this->get('/portal/'.$submission->id)->assertForbidden();
        $url = URL::temporarySignedRoute('applicant.portal', now()->addMinute(), ['submission' => $submission->id]);
        $this->get($url)->assertOk();
    }
}

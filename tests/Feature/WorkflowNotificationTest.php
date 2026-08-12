<?php

namespace Tests\Feature;

use App\Jobs\SendSubmissionStatusNotification;
use App\Models\Applicant;
use App\Models\ProgramPeriod;
use App\Models\Submission;
use App\Notifications\SubmissionStatusNotification;
use App\Models\Song;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class WorkflowNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_delivery_is_idempotent_and_redacts_recipient(): void
    {
        Notification::fake();
        $period = ProgramPeriod::create(['name' => 'Test', 'slug' => 'test', 'opens_at' => now()->subDay(), 'closes_at' => now()->addDay(), 'status' => 'open']);
        $applicant = Applicant::create(['full_name' => 'Musisi', 'nik' => '1234567890123456', 'nik_blind_index' => hash('sha256', 'nik'), 'birth_place' => 'Jakarta', 'birth_date' => '1990-01-01', 'email' => 'musisi@example.test', 'whatsapp' => '628123456789', 'province' => 'DKI', 'city' => 'Jakarta', 'address' => 'Alamat']);
        $submission = Submission::create(['program_period_id' => $period->id, 'applicant_id' => $applicant->id, 'registration_number' => 'OS-2026-000001', 'status' => 'submitted', 'draft_token_hash' => hash('sha256', 'draft')]);

        $job = new SendSubmissionStatusNotification($submission->id, 'submitted');
        $job->handle();
        $job->handle();

        Notification::assertSentOnDemandTimes(SubmissionStatusNotification::class, 1);
        $this->assertDatabaseCount('notification_deliveries', 1);
        $this->assertDatabaseMissing('notification_deliveries', ['recipient_hash' => 'musisi@example.test']);
        $this->assertDatabaseHas('notification_deliveries', ['status' => 'sent', 'attempts' => 1]);
    }

    public function test_submitted_email_contains_submission_details_without_nik(): void
    {
        $period = ProgramPeriod::create(['name' => 'Original Sessions 2026', 'slug' => 'mail-test', 'opens_at' => now()->subDay(), 'closes_at' => now()->addDay(), 'status' => 'open']);
        $applicant = Applicant::create(['full_name' => 'Alya Musik', 'nik' => '1234567890123456', 'nik_blind_index' => hash('sha256', 'nik-mail'), 'birth_place' => 'Jakarta', 'birth_date' => '1990-01-01', 'email' => 'alya@example.test', 'whatsapp' => '628123456789', 'province' => 'DKI Jakarta', 'city' => 'Jakarta Selatan', 'district' => 'Tebet', 'village' => 'Tebet Barat', 'postal_code' => '12810', 'address' => 'Alamat']);
        $submission = Submission::create(['program_period_id' => $period->id, 'applicant_id' => $applicant->id, 'registration_number' => 'OS-2026-000099', 'status' => 'submitted', 'submitted_at' => now(), 'draft_token_hash' => hash('sha256', 'draft-mail')]);
        Song::create(['submission_id' => $submission->id, 'title' => 'Lagu Harapan', 'artist_name' => 'Alya', 'artist_social_url' => 'https://instagram.com/alya', 'songwriters' => [['name' => 'Alya Musik', 'role' => 'composer_author']], 'genre' => 'Pop', 'language' => 'Indonesia', 'creation_year' => 2026, 'story' => str_repeat('Cerita lagu ', 6)]);

        $notification = new SubmissionStatusNotification($submission->load(['applicant', 'song', 'files', 'links', 'period']), \App\Enums\SubmissionStatus::Submitted);
        $mail = $notification->toMail((object) []);
        $html = $this->app['view']->make($mail->view, $mail->viewData)->render();

        $this->assertSame('Lagu berhasil disubmit — OS-2026-000099', $mail->subject);
        $this->assertStringContainsString('Alya Musik', $html);
        $this->assertStringContainsString('Lagu Harapan', $html);
        $this->assertStringContainsString('OS-2026-000099', $html);
        $this->assertStringNotContainsString('1234567890123456', $html);
    }
}

<?php

namespace Tests\Feature;

use App\Jobs\SendSubmissionStatusNotification;
use App\Models\Applicant;
use App\Models\ProgramPeriod;
use App\Models\Submission;
use App\Notifications\SubmissionStatusNotification;
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
}

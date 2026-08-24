<?php

namespace Tests\Feature;

use App\Models\Applicant;
use App\Models\ProgramPeriod;
use App\Models\Submission;
use App\Models\User;
use App\Notifications\SubmissionStatusNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_message_monitoring_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true, 'email_verified_at' => now()]);
        [$submission] = $this->makeSubmission();
        $this->makeDelivery($submission, 'sent');
        $this->makeDelivery($submission, 'failed', 'SMTP connection refused', 'selected');

        $this->actingAs($admin)->get('/admin/messages')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Admin/Messages/Index')
                ->where('counts.sent', 1)
                ->where('counts.failed', 1)
                ->has('messages.data', 2));
    }

    public function test_admin_can_retry_failed_message_without_queue_worker(): void
    {
        Notification::fake();
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true, 'email_verified_at' => now()]);
        [$submission] = $this->makeSubmission();
        $submission->statusHistories()->create(['from_status' => 'administrative_review', 'to_status' => 'selected', 'actor_id' => $admin->id, 'reason' => 'Lagu sesuai hasil kurasi.']);
        $delivery = $this->makeDelivery($submission, 'failed', 'SMTP timeout', 'selected');

        $this->actingAs($admin)->post("/admin/messages/{$delivery}/retry")
            ->assertRedirect()
            ->assertSessionHas('success');

        Notification::assertSentOnDemandTimes(SubmissionStatusNotification::class, 1);
        $this->assertDatabaseHas('notification_deliveries', [
            'id' => $delivery,
            'status' => 'sent',
            'attempts' => 2,
            'last_error' => null,
        ]);
    }

    public function test_admin_can_resend_a_message_previously_marked_sent(): void
    {
        Notification::fake();
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true, 'email_verified_at' => now()]);
        [$submission] = $this->makeSubmission();
        $delivery = $this->makeDelivery($submission, 'sent', null, 'selected');

        $this->actingAs($admin)->post("/admin/messages/{$delivery}/retry")
            ->assertRedirect()
            ->assertSessionHas('success');

        Notification::assertSentOnDemandTimes(SubmissionStatusNotification::class, 1);
        $this->assertDatabaseHas('notification_deliveries', [
            'id' => $delivery,
            'status' => 'sent',
            'attempts' => 1,
        ]);
    }

    private function makeSubmission(): array
    {
        $period = ProgramPeriod::create(['name' => 'Test', 'slug' => str()->random(8), 'opens_at' => now()->subDay(), 'closes_at' => now()->addDay(), 'status' => 'open']);
        $applicant = Applicant::create(['full_name' => 'Musisi Test', 'nik' => '1234567890123456', 'nik_blind_index' => hash('sha256', str()->random()), 'birth_place' => 'Jakarta', 'birth_date' => '1990-01-01', 'email' => 'musisi@example.test', 'whatsapp' => '628123456789', 'province' => 'DKI', 'city' => 'Jakarta', 'address' => 'Alamat']);
        $submission = Submission::create(['program_period_id' => $period->id, 'applicant_id' => $applicant->id, 'registration_number' => 'OS-2026-000200', 'status' => 'selected', 'submitted_at' => now(), 'draft_token_hash' => hash('sha256', str()->random())]);
        $submission->song()->create(['title' => 'Nada Harapan', 'artist_name' => 'Musisi Test', 'songwriters' => [['name' => 'Musisi Test', 'role' => 'composer_author']], 'genre' => 'Pop', 'language' => 'Indonesia', 'creation_year' => 2026, 'story' => str_repeat('Cerita lagu ', 6)]);

        return [$submission, $applicant];
    }

    private function makeDelivery(Submission $submission, string $status, ?string $error = null, string $statusValue = 'submitted'): int
    {
        return DB::table('notification_deliveries')->insertGetId([
            'submission_id' => $submission->id,
            'channel' => 'mail',
            'template' => 'submission_status_'.$statusValue,
            'recipient_hash' => hash('sha256', str()->random()),
            'status' => $status,
            'attempts' => $status === 'failed' ? 1 : 0,
            'last_error' => $error,
            'idempotency_key' => hash('sha256', str()->random()),
            'sent_at' => $status === 'sent' ? now() : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

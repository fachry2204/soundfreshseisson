<?php

namespace Tests\Feature;

use App\Enums\SubmissionStatus;
use App\Models\Applicant;
use App\Models\ProgramPeriod;
use App\Models\Submission;
use App\Models\User;
use App\Notifications\SubmissionStatusNotification;
use App\Services\Submission\SubmissionStateMachine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SubmissionStatusFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_rejection_reason_is_required_but_other_status_notes_are_optional(): void
    {
        Notification::fake();
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true, 'email_verified_at' => now()]);
        $period = ProgramPeriod::create(['name' => 'Test', 'slug' => 'reason-required', 'opens_at' => now()->subDay(), 'closes_at' => now()->addDay(), 'status' => 'open']);
        $applicant = Applicant::create(['full_name' => 'Pendaftar', 'nik' => '1234567890123456', 'nik_blind_index' => hash('sha256', 'reason-required'), 'birth_place' => 'Jakarta', 'birth_date' => '1990-01-01', 'email' => 'reason@example.test', 'whatsapp' => '08123456789', 'province' => 'DKI Jakarta', 'city' => 'Jakarta', 'address' => 'Alamat']);
        $submission = Submission::create(['program_period_id' => $period->id, 'applicant_id' => $applicant->id, 'status' => 'submitted', 'submitted_at' => now(), 'draft_token_hash' => hash('sha256', 'reason-required')]);

        $this->actingAs($admin)
            ->patch("/admin/submissions/{$submission->id}/status", ['status' => 'not_selected', 'reason' => ''])
            ->assertSessionHasErrors(['reason']);

        $this->assertSame('submitted', $submission->fresh()->status->value);

        $this->actingAs($admin)
            ->patch("/admin/submissions/{$submission->id}/status", ['status' => 'not_selected', 'reason' => 'tidak'])
            ->assertSessionHasErrors(['reason']);

        $this->assertSame('submitted', $submission->fresh()->status->value);

        $this->actingAs($admin)
            ->patch("/admin/submissions/{$submission->id}/status", ['status' => 'administrative_review', 'reason' => ''])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertSame('administrative_review', $submission->fresh()->status->value);
        $this->assertDatabaseHas('status_histories', [
            'submission_id' => $submission->id,
            'to_status' => 'administrative_review',
            'reason' => null,
        ]);
    }

    public function test_admin_can_move_submission_through_simple_status_flow(): void
    {
        Notification::fake();
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $period = ProgramPeriod::create(['name' => 'Test', 'slug' => 'status-test', 'opens_at' => now()->subDay(), 'closes_at' => now()->addDay(), 'status' => 'open']);
        $applicant = Applicant::create(['full_name' => 'Pendaftar', 'nik' => '1234567890123456', 'nik_blind_index' => hash('sha256', 'nik-status'), 'birth_place' => 'Jakarta', 'birth_date' => '1990-01-01', 'email' => 'status@example.test', 'whatsapp' => '08123456789', 'province' => 'DKI Jakarta', 'city' => 'Jakarta', 'address' => 'Alamat']);
        $submission = Submission::create(['program_period_id' => $period->id, 'applicant_id' => $applicant->id, 'status' => 'submitted', 'draft_token_hash' => hash('sha256', 'status-test')]);
        $machine = app(SubmissionStateMachine::class);

        $machine->transition($submission, SubmissionStatus::AdministrativeReview, $admin, 'Mulai review');
        $machine->transition($submission->fresh(), SubmissionStatus::Selected, $admin, 'Diterima');
        $machine->transition($submission->fresh(), SubmissionStatus::NotSelected, $admin, 'Koreksi menjadi ditolak');

        $this->assertSame('not_selected', $submission->fresh()->status->value);
        $this->assertDatabaseCount('status_histories', 3);
        Notification::assertSentOnDemandTimes(SubmissionStatusNotification::class, 3);
        $this->assertTrue($submission->statusHistories()->with('actor')->get()->every(
            fn ($history) => $history->actor?->is($admin),
        ));
    }

    public function test_new_submission_with_empty_status_is_treated_as_draft(): void
    {
        Notification::fake();
        $period = ProgramPeriod::create(['name' => 'Test', 'slug' => 'empty-status', 'opens_at' => now()->subDay(), 'closes_at' => now()->addDay(), 'status' => 'open']);
        $applicant = Applicant::create(['full_name' => 'Pendaftar', 'nik' => '1234567890123456', 'nik_blind_index' => hash('sha256', 'empty-status'), 'birth_place' => 'Jakarta', 'birth_date' => '1990-01-01', 'email' => 'empty@example.test', 'whatsapp' => '08123456789', 'province' => 'DKI Jakarta', 'city' => 'Jakarta', 'address' => 'Alamat']);
        $submission = Submission::create(['program_period_id' => $period->id, 'applicant_id' => $applicant->id, 'status' => 'draft', 'draft_token_hash' => hash('sha256', 'empty-status')]);
        $submission->setRawAttributes([...$submission->getAttributes(), 'status' => '']);

        app(SubmissionStateMachine::class)->transition($submission, SubmissionStatus::Submitted, null, 'Dikirim');

        $this->assertSame('submitted', $submission->fresh()->status->value);
    }
}

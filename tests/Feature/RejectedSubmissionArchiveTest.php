<?php

namespace Tests\Feature;

use App\Enums\SubmissionStatus;
use App\Models\Applicant;
use App\Models\ProgramPeriod;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Models\User;
use App\Services\Submission\SubmissionStateMachine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RejectedSubmissionArchiveTest extends TestCase
{
    use RefreshDatabase;

    public function test_rejected_submission_stays_active_while_video_moves_to_trash(): void
    {
        Notification::fake();
        Storage::fake('local');
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true, 'email_verified_at' => now()]);
        $period = ProgramPeriod::create(['name' => 'Test', 'slug' => 'rejected-archive', 'opens_at' => now()->subDay(), 'closes_at' => now()->addDay(), 'status' => 'open']);
        $applicant = Applicant::create(['full_name' => 'Peserta Arsip', 'nik' => '1234567890123456', 'nik_blind_index' => hash('sha256', 'archive-nik'), 'birth_place' => 'Jakarta', 'birth_date' => '1990-01-01', 'email' => 'arsip@example.test', 'whatsapp' => '628123456789', 'province' => 'DKI', 'city' => 'Jakarta', 'address' => 'Alamat']);
        $submission = Submission::create(['program_period_id' => $period->id, 'applicant_id' => $applicant->id, 'registration_number' => 'OS-2026-000300', 'status' => 'submitted', 'submitted_at' => now(), 'draft_token_hash' => hash('sha256', 'archive-draft')]);
        $submission->song()->create(['title' => 'Lagu Ditolak', 'artist_name' => 'Peserta Arsip', 'songwriters' => [['name' => 'Peserta Arsip', 'role' => 'composer_author']], 'genre' => 'Pop', 'language' => 'Indonesia', 'creation_year' => 2026, 'story' => str_repeat('Cerita lagu ', 6)]);
        Storage::disk('local')->put("submissions/{$submission->id}/video.mp4", 'video');
        $file = SubmissionFile::create(['submission_id' => $submission->id, 'type' => 'video', 'disk' => 'local', 'path' => "submissions/{$submission->id}/video.mp4", 'original_name' => 'video.mp4', 'mime' => 'video/mp4', 'size' => 5, 'checksum' => str_repeat('a', 64), 'scan_status' => 'clean']);

        app(SubmissionStateMachine::class)->transition($submission, SubmissionStatus::NotSelected, $admin, 'Belum sesuai kebutuhan program.');

        $file->refresh();
        $this->assertSame('not_selected', $submission->fresh()->status->value);
        $this->assertNotNull($file->trashed_at);
        $this->assertSame($admin->id, $file->trashed_by);
        $this->assertSame('Belum sesuai kebutuhan program.', $file->trash_reason);
        $this->assertSame('trashed', $file->scan_status);
        Storage::disk('local')->assertMissing($file->original_path);
        Storage::disk('local')->assertExists($file->path);

        $this->actingAs($admin)->get('/admin/submissions')->assertOk()->assertInertia(fn ($page) => $page
            ->component('Admin/Submissions/Index')
            ->where('submissions.data.0.registration_number', 'OS-2026-000300'));
        $this->actingAs($admin)->get('/admin/trash')->assertOk()->assertInertia(fn ($page) => $page
            ->component('Admin/Trash/Index')
            ->has('files.data', 1));

        $this->actingAs($admin)->delete("/admin/trash/files/{$file->id}")->assertRedirect();
        Storage::disk('local')->assertMissing($file->path);
        $this->assertDatabaseMissing('submission_files', ['id' => $file->id]);
        $this->assertDatabaseHas('submissions', ['id' => $submission->id, 'status' => 'not_selected']);
    }
}

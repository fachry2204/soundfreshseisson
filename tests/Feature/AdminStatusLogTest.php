<?php

namespace Tests\Feature;

use App\Models\Applicant;
use App\Models\ProgramPeriod;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminStatusLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_see_current_user_status_and_the_admin_who_updated_it(): void
    {
        $admin = User::factory()->create(['name' => 'Admin Kurator', 'username' => 'kurator', 'role' => 'admin', 'is_active' => true, 'email_verified_at' => now()]);
        $period = ProgramPeriod::create(['name' => 'Test', 'slug' => 'status-log', 'opens_at' => now()->subDay(), 'closes_at' => now()->addDay(), 'status' => 'open']);
        $applicant = Applicant::create(['full_name' => 'Peserta Log', 'nik' => '1234567890123456', 'nik_blind_index' => hash('sha256', 'status-log'), 'birth_place' => 'Jakarta', 'birth_date' => '1990-01-01', 'email' => 'log@example.test', 'whatsapp' => '08123456789', 'province' => 'DKI Jakarta', 'city' => 'Jakarta', 'address' => 'Alamat']);
        $submission = Submission::create(['program_period_id' => $period->id, 'applicant_id' => $applicant->id, 'registration_number' => 'OS-2026-000777', 'status' => 'not_selected', 'submitted_at' => now(), 'draft_token_hash' => hash('sha256', 'status-log')]);
        $submission->song()->create(['title' => 'Lagu Log', 'artist_name' => 'Artis Log', 'songwriters' => [['name' => 'Peserta Log', 'role' => 'composer_author']], 'genre' => 'Pop', 'language' => 'Indonesia', 'creation_year' => 2026, 'story' => str_repeat('Cerita lagu ', 6)]);
        $submission->statusHistories()->create(['from_status' => 'administrative_review', 'to_status' => 'not_selected', 'actor_id' => $admin->id, 'reason' => 'Materi belum sesuai dengan arah program saat ini.']);

        $this->actingAs($admin)->get('/admin/status-logs')->assertOk()->assertInertia(fn ($page) => $page
            ->component('Admin/StatusLogs/Index')
            ->where('logs.data.0.registration_number', 'OS-2026-000777')
            ->where('logs.data.0.status', 'not_selected')
            ->where('logs.data.0.reason', 'Materi belum sesuai dengan arah program saat ini.')
            ->where('logs.data.0.updated_by_name', 'Admin Kurator')
            ->where('logs.data.0.updated_by_username', 'kurator'));
    }

    public function test_viewer_can_read_status_log(): void
    {
        $viewer = User::factory()->create(['role' => 'viewer', 'is_active' => true, 'email_verified_at' => now()]);

        $this->actingAs($viewer)->get('/admin/status-logs')->assertOk();
    }
}

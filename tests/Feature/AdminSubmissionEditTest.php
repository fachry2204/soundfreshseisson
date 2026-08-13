<?php

namespace Tests\Feature;

use App\Models\Applicant;
use App\Models\ProgramPeriod;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSubmissionEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_edit_submission_details(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true, 'email_verified_at' => now()]);
        $period = ProgramPeriod::create(['name' => 'Internal', 'slug' => 'internal', 'opens_at' => now()->subDay(), 'closes_at' => now()->addYear(), 'status' => 'open']);
        $applicant = Applicant::create(['full_name' => 'Nama Lama', 'nik' => '1234567890123456', 'nik_blind_index' => hash('sha256', 'old'), 'birth_place' => 'Jakarta', 'birth_date' => '1990-01-01', 'email' => 'lama@example.test', 'whatsapp' => '628123456789', 'province' => 'DKI Jakarta', 'city' => 'Jakarta', 'district' => 'Setiabudi', 'village' => 'Karet', 'postal_code' => '12920', 'address' => 'Alamat lama']);
        $submission = Submission::create(['program_period_id' => $period->id, 'applicant_id' => $applicant->id, 'registration_number' => 'OS-2026-000001', 'status' => 'submitted', 'draft_token_hash' => hash('sha256', 'edit-test')]);
        $submission->song()->create(['title' => 'Lagu Lama', 'artist_name' => 'Artis Lama', 'artist_social_url' => 'https://instagram.com/lama', 'songwriters' => [['name' => 'Penulis Lama', 'role' => 'composer']], 'genre' => 'Pop', 'language' => 'Indonesia', 'creation_year' => 2025, 'story' => 'Cerita lagu lama yang akan diperbarui.', 'has_cowriters' => false]);
        $submission->links()->create(['type' => 'video', 'url' => 'https://drive.google.com/old']);

        $payload = ['full_name' => 'Nama Baru', 'nik' => '6543210987654321', 'birth_place' => 'Bandung', 'birth_date' => '1992-02-02', 'email' => 'baru@example.test', 'whatsapp' => '081298765432', 'province' => 'Jawa Barat', 'city' => 'Bandung', 'district' => 'Coblong', 'village' => 'Dago', 'postal_code' => '40135', 'address' => 'Alamat baru', 'title' => 'Lagu Baru', 'artist_name' => 'Artis Baru', 'artist_social_url' => 'https://instagram.com/baru', 'artist_spotify_url' => 'https://open.spotify.com/artist/test', 'songwriters' => [['name' => 'Satu', 'role' => 'composer'], ['name' => 'Dua', 'role' => 'author']], 'genre' => 'Rock', 'language' => 'Indonesia', 'creation_year' => 2026, 'story' => 'Cerita lagu baru yang sudah direvisi oleh admin.', 'lyrics' => 'Lirik baru', 'video_url' => 'https://drive.google.com/new'];

        $this->actingAs($admin)->put("/admin/submissions/{$submission->id}/details", $payload)->assertSessionHas('success');

        $this->assertSame('Nama Baru', $applicant->fresh()->full_name);
        $this->assertSame('Lagu Baru', $submission->song->fresh()->title);
        $this->assertCount(2, $submission->song->fresh()->songwriters);
        $this->assertSame('https://drive.google.com/new', $submission->links()->where('type', 'video')->value('url'));
        $this->assertDatabaseHas('audit_logs', ['action' => 'submission.details_updated', 'actor_id' => $admin->id]);
    }
}

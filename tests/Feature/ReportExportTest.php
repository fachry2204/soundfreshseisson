<?php

namespace Tests\Feature;

use App\Models\Applicant;
use App\Models\ProgramPeriod;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_excludes_sensitive_pii_and_is_audited(): void
    {
        $admin = User::factory()->create(['role' => 'program_admin', 'email_verified_at' => now()]);
        $period = ProgramPeriod::create(['name' => 'Gelombang', 'slug' => 'gelombang', 'opens_at' => now()->subDay(), 'closes_at' => now()->addDay(), 'status' => 'open']);
        $applicant = Applicant::create(['full_name' => 'Nama Rahasia', 'nik' => '1234567890123456', 'nik_blind_index' => hash('sha256', 'nik'), 'birth_place' => 'Bandung', 'birth_date' => '1990-01-01', 'email' => 'rahasia@example.test', 'whatsapp' => '628123456789', 'province' => 'Jawa Barat', 'city' => 'Bandung', 'address' => 'Alamat Rahasia']);
        $submission = Submission::create(['program_period_id' => $period->id, 'applicant_id' => $applicant->id, 'registration_number' => 'OS-2026-000001', 'status' => 'submitted', 'draft_token_hash' => hash('sha256', 'draft'), 'submitted_at' => now()]);
        $submission->song()->create(['title' => 'Lagu Aman', 'genre' => 'Pop', 'language' => 'Indonesia', 'creation_year' => 2026, 'story' => str_repeat('Cerita. ', 10)]);

        $response = $this->actingAs($admin)->get("/admin/reports/{$period->id}/csv")->assertOk();
        $csv = $response->streamedContent();
        $this->assertStringContainsString('Lagu Aman', $csv);
        $this->assertStringNotContainsString('rahasia@example.test', $csv);
        $this->assertStringNotContainsString('1234567890123456', $csv);
        $this->assertDatabaseHas('audit_logs', ['action' => 'report.exported', 'actor_id' => $admin->id]);
    }

    public function test_viewer_cannot_export_report(): void
    {
        $viewer = User::factory()->create(['role' => 'viewer', 'email_verified_at' => now()]);
        $period = ProgramPeriod::create(['name' => 'Gelombang', 'slug' => 'gelombang', 'opens_at' => now()->subDay(), 'closes_at' => now()->addDay(), 'status' => 'open']);
        $this->actingAs($viewer)->get("/admin/reports/{$period->id}/csv")->assertForbidden();
    }
}

<?php

namespace Tests\Feature;

use App\Models\Applicant;
use App\Models\ProgramPeriod;
use App\Models\Submission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class RegistrationSuccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_success_page_accepts_relative_signed_url_on_current_host(): void
    {
        $period = ProgramPeriod::create([
            'name' => 'Original Sessions',
            'slug' => 'original-sessions',
            'opens_at' => now()->subDay(),
            'closes_at' => now()->addDay(),
            'status' => 'open',
        ]);
        $applicant = Applicant::create([
            'full_name' => 'Peserta',
            'nik' => '1234567890123456',
            'nik_blind_index' => hash('sha256', 'participant'),
            'birth_place' => 'Jakarta',
            'birth_date' => '1990-01-01',
            'email' => 'peserta@example.test',
            'whatsapp' => '628123456789',
            'province' => 'DKI Jakarta',
            'city' => 'Jakarta',
            'district' => 'Kebayoran Baru',
            'village' => 'Senayan',
            'postal_code' => '12190',
            'address' => 'Alamat peserta',
        ]);
        $submission = Submission::create([
            'program_period_id' => $period->id,
            'applicant_id' => $applicant->id,
            'status' => 'submitted',
            'registration_number' => 'OS-2026-000001',
            'draft_token_hash' => hash('sha256', 'draft'),
        ]);

        $url = URL::temporarySignedRoute(
            'registration.success',
            now()->addMinute(),
            ['submission' => $submission->id],
            absolute: false,
        );

        $this->get($url)
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Public/Success')
                ->where('registrationNumber', 'OS-2026-000001'));
    }
}

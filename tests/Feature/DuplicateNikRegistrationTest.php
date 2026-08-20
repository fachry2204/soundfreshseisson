<?php

namespace Tests\Feature;

use App\Models\Applicant;
use App\Models\ProgramPeriod;
use App\Models\Submission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DuplicateNikRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_same_nik_can_have_multiple_submissions_in_one_period(): void
    {
        $period = ProgramPeriod::create(['name' => 'Test', 'slug' => 'same-nik', 'opens_at' => now()->subDay(), 'closes_at' => now()->addDay(), 'status' => 'open']);
        $nikHash = hash_hmac('sha256', '1234567890123456', config('app.key'));

        foreach ([1, 2] as $index) {
            $applicant = Applicant::create([
                'full_name' => 'Pendaftar '.$index,
                'nik' => '1234567890123456',
                'nik_blind_index' => $nikHash,
                'birth_place' => 'Jakarta',
                'birth_date' => '1990-01-01',
                'email' => "pendaftar{$index}@example.test",
                'whatsapp' => '628123456789',
                'province' => 'DKI Jakarta',
                'city' => 'Jakarta',
                'address' => 'Alamat',
            ]);
            Submission::create([
                'program_period_id' => $period->id,
                'applicant_id' => $applicant->id,
                'registration_number' => 'OS-2026-'.str_pad((string) $index, 6, '0', STR_PAD_LEFT),
                'status' => 'submitted',
                'draft_token_hash' => hash('sha256', 'same-nik-'.$index),
            ]);
        }

        $this->assertSame(2, Submission::where('program_period_id', $period->id)->count());
        $this->assertSame(2, Applicant::where('nik_blind_index', $nikHash)->count());
    }
}

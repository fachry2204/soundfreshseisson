<?php

namespace Tests\Feature;

use App\Models\ProgramPeriod;
use App\Models\Submission;
use App\Services\Submission\RegistrationNumberGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RegistrationNumberGeneratorTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_uses_the_highest_existing_number_instead_of_submission_count(): void
    {
        $period = ProgramPeriod::create(['name' => 'Test', 'slug' => 'number-test', 'opens_at' => '2026-01-01', 'closes_at' => '2026-12-31', 'status' => 'open']);
        foreach ([1, 7] as $sequence) {
            Submission::create([
                'program_period_id' => $period->id,
                'registration_number' => 'OS-2026-'.str_pad((string) $sequence, 6, '0', STR_PAD_LEFT),
                'status' => 'submitted',
                'draft_token_hash' => hash('sha256', 'draft-'.$sequence),
            ]);
        }

        $number = DB::transaction(fn () => app(RegistrationNumberGenerator::class)->next($period));

        $this->assertSame('OS-2026-000008', $number);
    }
}

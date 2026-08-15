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

    public function test_reserved_number_is_not_reused_when_submission_is_deleted(): void
    {
        $period = ProgramPeriod::create(['name' => 'Test', 'slug' => 'delete-number-test', 'opens_at' => '2026-01-01', 'closes_at' => '2026-12-31', 'status' => 'open']);
        $submission = Submission::create([
            'program_period_id' => $period->id,
            'registration_number' => 'OS-2026-000007',
            'status' => 'submitted',
            'draft_token_hash' => hash('sha256', 'deleted-draft'),
        ]);

        $first = DB::transaction(fn () => app(RegistrationNumberGenerator::class)->next($period));
        $submission->delete();
        $second = DB::transaction(fn () => app(RegistrationNumberGenerator::class)->next($period));

        $this->assertSame('OS-2026-000008', $first);
        $this->assertSame('OS-2026-000009', $second);
    }

    public function test_number_is_unique_across_different_program_periods(): void
    {
        $oldPeriod = ProgramPeriod::create(['name' => 'Old', 'slug' => 'old-period', 'opens_at' => '2026-01-01', 'closes_at' => '2026-06-30', 'status' => 'open']);
        $currentPeriod = ProgramPeriod::create(['name' => 'Current', 'slug' => 'current-period', 'opens_at' => '2026-07-01', 'closes_at' => '2026-12-31', 'status' => 'open']);
        Submission::create([
            'program_period_id' => $oldPeriod->id,
            'registration_number' => 'OS-2026-000007',
            'status' => 'submitted',
            'draft_token_hash' => hash('sha256', 'old-period-draft'),
        ]);

        $number = DB::transaction(fn () => app(RegistrationNumberGenerator::class)->next($currentPeriod));

        $this->assertSame('OS-2026-000008', $number);
    }
}

<?php

namespace App\Services\Submission;

use App\Models\ProgramPeriod;
use App\Models\Submission;

class RegistrationNumberGenerator
{
    /**
     * Generate the next number while locking its period row. This method must
     * be called inside the transaction that persists the submission.
     */
    public function next(ProgramPeriod $period): string
    {
        $lockedPeriod = ProgramPeriod::query()->lockForUpdate()->findOrFail($period->id);
        $prefix = 'OS-'.$lockedPeriod->opens_at->year.'-';
        $latest = Submission::query()
            ->where('program_period_id', $lockedPeriod->id)
            ->where('registration_number', 'like', $prefix.'%')
            ->orderByDesc('registration_number')
            ->value('registration_number');

        $sequence = $latest ? ((int) substr($latest, strlen($prefix))) + 1 : 1;

        return $prefix.str_pad((string) $sequence, 6, '0', STR_PAD_LEFT);
    }
}

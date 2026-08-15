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
            ->where('registration_number', 'like', $prefix.'%')
            ->orderByDesc('registration_number')
            ->value('registration_number');

        $highestPersisted = $latest ? (int) substr($latest, strlen($prefix)) : 0;
        $settings = $lockedPeriod->settings ?? [];
        $lastReserved = (int) ($settings['registration_last_sequence'] ?? 0);
        $sequence = max($highestPersisted, $lastReserved) + 1;

        // Keep a permanent high-water mark. Deleting a submission must never
        // make an already issued registration number available again.
        $settings['registration_last_sequence'] = $sequence;
        $lockedPeriod->update(['settings' => $settings]);

        return $prefix.str_pad((string) $sequence, 6, '0', STR_PAD_LEFT);
    }
}

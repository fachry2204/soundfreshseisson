<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! config('services.clamav.enabled')) {
            DB::table('submission_files')
                ->where('scan_status', 'pending')
                ->update([
                    'scan_status' => 'clean',
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        // Status aman tidak dikembalikan ke pending saat rollback.
    }
};

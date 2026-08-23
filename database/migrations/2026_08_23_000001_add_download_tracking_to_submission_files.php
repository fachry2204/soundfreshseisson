<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submission_files', function (Blueprint $table) {
            $table->timestamp('downloaded_at')->nullable()->after('scan_status');
            $table->foreignId('downloaded_by')->nullable()->after('downloaded_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('submission_files', function (Blueprint $table) {
            $table->dropConstrainedForeignId('downloaded_by');
            $table->dropColumn('downloaded_at');
        });
    }
};

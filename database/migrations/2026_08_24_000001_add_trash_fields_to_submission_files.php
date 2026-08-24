<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submission_files', function (Blueprint $table): void {
            $table->timestamp('trashed_at')->nullable()->after('scan_status')->index();
            $table->foreignId('trashed_by')->nullable()->after('trashed_at')->constrained('users')->nullOnDelete();
            $table->text('trash_reason')->nullable()->after('trashed_by');
            $table->string('original_path')->nullable()->after('path');
        });
    }

    public function down(): void
    {
        Schema::table('submission_files', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('trashed_by');
            $table->dropColumn(['trashed_at', 'trash_reason', 'original_path']);
        });
    }
};

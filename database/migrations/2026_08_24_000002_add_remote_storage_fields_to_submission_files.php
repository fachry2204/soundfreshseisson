<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submission_files', function (Blueprint $table): void {
            $table->string('storage_status')->default('local')->after('disk')->index();
            $table->text('remote_url')->nullable()->after('path');
            $table->timestamp('transferred_at')->nullable()->after('remote_url');
            $table->text('transfer_error')->nullable()->after('transferred_at');
        });
    }

    public function down(): void
    {
        Schema::table('submission_files', function (Blueprint $table): void {
            $table->dropIndex(['storage_status']);
            $table->dropColumn(['storage_status', 'remote_url', 'transferred_at', 'transfer_error']);
        });
    }
};

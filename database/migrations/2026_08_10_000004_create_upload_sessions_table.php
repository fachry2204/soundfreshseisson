<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('upload_sessions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->char('token_hash', 64);
            $table->string('type');
            $table->string('original_name');
            $table->string('declared_mime');
            $table->string('detected_mime')->nullable();
            $table->unsignedBigInteger('expected_size');
            $table->unsignedInteger('chunk_size');
            $table->unsignedInteger('total_chunks');
            $table->char('expected_checksum', 64);
            $table->char('actual_checksum', 64)->nullable();
            $table->json('received_chunks')->nullable();
            $table->string('path')->nullable();
            $table->string('status')->default('initialized')->index();
            $table->foreignUlid('claimed_by_submission_id')->nullable()->constrained('submissions')->nullOnDelete();
            $table->timestamp('expires_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('upload_sessions');
    }
};

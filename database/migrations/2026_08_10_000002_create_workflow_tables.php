<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('revision_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('submission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requested_by')->constrained('users');
            $table->json('fields');
            $table->text('message');
            $table->dateTime('deadline_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
        Schema::create('internal_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('submission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('body');
            $table->timestamps();
        });
        Schema::create('review_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('submission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('assigned');
            $table->timestamp('assigned_at')->useCurrent();
            $table->unique(['submission_id', 'reviewer_id']);
        });
        Schema::create('review_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('review_criteria_id')->constrained('review_criteria')->cascadeOnDelete();
            $table->unsignedTinyInteger('score');
            $table->text('comment')->nullable();
            $table->boolean('is_final')->default(false);
            $table->timestamps();
            $table->unique(['review_assignment_id', 'review_criteria_id']);
        });
        Schema::create('review_decisions', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('submission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('decided_by')->constrained('users');
            $table->string('decision');
            $table->text('reason');
            $table->timestamps();
        });
        Schema::create('notification_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('submission_id')->nullable()->constrained()->nullOnDelete();
            $table->string('channel');
            $table->string('template');
            $table->string('recipient_hash', 64);
            $table->string('status')->default('queued')->index();
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->text('last_error')->nullable();
            $table->string('idempotency_key')->unique();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['notification_deliveries', 'review_decisions', 'review_scores', 'review_assignments', 'internal_notes', 'revision_requests'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};

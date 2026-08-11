<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('viewer')->index();
            $table->boolean('is_active')->default(true);
        });

        Schema::create('program_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamp('opens_at');
            $table->timestamp('closes_at');
            $table->string('timezone')->default('Asia/Jakarta');
            $table->string('status')->default('coming_soon')->index();
            $table->unsignedInteger('quota')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
        });
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('stage_name')->nullable();
            $table->text('nik');
            $table->char('nik_blind_index', 64)->index();
            $table->string('birth_place');
            $table->date('birth_date');
            $table->string('email')->index();
            $table->string('whatsapp')->index();
            $table->string('province');
            $table->string('city');
            $table->text('address');
            $table->string('instagram')->nullable();
            $table->string('tiktok')->nullable();
            $table->timestamps();
        });
        Schema::create('submissions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignId('program_period_id')->constrained()->cascadeOnDelete();
            $table->foreignId('applicant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('registration_number')->nullable()->unique();
            $table->string('status')->default('draft')->index();
            $table->char('draft_token_hash', 64)->unique();
            $table->string('idempotency_key')->nullable()->unique();
            $table->json('snapshot')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('submission_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('genre');
            $table->string('subgenre')->nullable();
            $table->string('language');
            $table->unsignedSmallInteger('duration_seconds')->nullable();
            $table->unsignedSmallInteger('creation_year');
            $table->text('story');
            $table->longText('lyrics')->nullable();
            $table->boolean('has_cowriters')->default(false);
            $table->timestamps();
        });
        Schema::create('submission_links', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('submission_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->text('url');
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });
        Schema::create('submission_files', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('submission_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('disk')->default('local');
            $table->string('path');
            $table->string('original_name');
            $table->string('mime');
            $table->unsignedBigInteger('size');
            $table->char('checksum', 64);
            $table->string('scan_status')->default('pending');
            $table->timestamps();
        });
        Schema::create('consents', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('submission_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('document_version');
            $table->timestamp('accepted_at');
            $table->char('ip_hash', 64);
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
        Schema::create('status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('submission_id')->constrained()->cascadeOnDelete();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('reason')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
        Schema::create('review_criteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_period_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedTinyInteger('weight');
            $table->unsignedSmallInteger('sort_order');
            $table->timestamps();
        });
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->text('question');
            $table->text('answer');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('content_sections', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('content');
            $table->unsignedInteger('revision')->default(1);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group');
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->boolean('is_secret')->default(false);
            $table->timestamps();
        });
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action')->index();
            $table->nullableUlidMorphs('auditable');
            $table->json('metadata')->nullable();
            $table->char('ip_hash', 64)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        foreach (['audit_logs', 'settings', 'content_sections', 'faqs', 'review_criteria', 'status_histories', 'consents', 'submission_files', 'submission_links', 'songs', 'submissions', 'applicants', 'program_periods'] as $table) {
            Schema::dropIfExists($table);
        }
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn(['role', 'is_active']));
    }
};

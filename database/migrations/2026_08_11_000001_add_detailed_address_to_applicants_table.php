<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->string('district')->nullable()->after('city');
            $table->string('village')->nullable()->after('district');
            $table->char('postal_code', 5)->nullable()->after('village');
        });
    }

    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropColumn(['district', 'village', 'postal_code']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->text('artist_social_url')->nullable()->after('artist_name');
            $table->text('artist_spotify_url')->nullable()->after('artist_social_url');
        });
    }

    public function down(): void
    {
        Schema::table('songs', fn (Blueprint $table) => $table->dropColumn(['artist_social_url', 'artist_spotify_url']));
    }
};

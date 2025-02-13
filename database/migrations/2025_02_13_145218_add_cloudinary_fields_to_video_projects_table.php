<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('video_projects', function (Blueprint $table) {
            $table->string('image_cloudinary_id')->nullable()->after('image_url');
            $table->string('audio_cloudinary_id')->nullable()->after('audio_url');
        });
    }

    public function down(): void
    {
        Schema::table('video_projects', function (Blueprint $table) {
            $table->dropColumn(['image_cloudinary_id', 'audio_cloudinary_id']);
        });
    }
};
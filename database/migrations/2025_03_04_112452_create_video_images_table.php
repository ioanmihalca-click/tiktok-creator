<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('video_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_project_id')->constrained()->onDelete('cascade'); // Cheie externă, ștergere în cascadă
            $table->string('url');
            $table->string('cloudinary_id')->nullable();
            $table->float('start'); // Folosește float pentru start și duration
            $table->float('duration');
            $table->unsignedInteger('order'); // Pentru ordonarea imaginilor
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_images');
    }
};

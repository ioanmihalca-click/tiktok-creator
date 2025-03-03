<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('video_projects', function (Blueprint $table) {
            $table->string('environment_type')->default('sandbox'); // 'sandbox' or 'production'
            $table->boolean('has_watermark')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('video_projects', function (Blueprint $table) {
            //
        });
    }
};

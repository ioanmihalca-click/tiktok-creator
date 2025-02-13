<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('video_projects', function (Blueprint $table) {
            $table->string('render_id')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('video_projects', function (Blueprint $table) {
            $table->dropColumn('render_id');
        });
    }
};
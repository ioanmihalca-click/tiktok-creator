<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('credit_packages', function (Blueprint $table) {
            $table->string('stripe_price_id')->nullable()->after('price'); // Adaugă coloana
        });
    }

    public function down()
    {
        Schema::table('credit_packages', function (Blueprint $table) {
            $table->dropColumn('stripe_price_id');
        });
    }
};

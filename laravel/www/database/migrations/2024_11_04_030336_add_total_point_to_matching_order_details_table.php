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
        Schema::table('matching_order_details', function (Blueprint $table) {
            $table->integer('mid_night_fee', )->nullable()->after('applied_point');
            $table->integer('total_point', )->nullable()->after('mid_night_fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matching_order_details', function (Blueprint $table) {
            $table->dropColumn('mid_night_fee');
            $table->dropColumn('total_point');
        });
    }
};

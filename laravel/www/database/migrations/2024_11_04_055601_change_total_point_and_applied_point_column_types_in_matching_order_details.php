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
            $table->decimal('total_point', 10, 2)->nullable()->after('mid_night_fee')->change();;
            $table->decimal('applied_point', 10, 2)->after('total_point')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matching_order_details', function (Blueprint $table) {
            $table->integer('total_point', )->nullable()->after('mid_night_fee')->change();
            $table->unsignedInteger('applied_point')->change();
        });
    }
};

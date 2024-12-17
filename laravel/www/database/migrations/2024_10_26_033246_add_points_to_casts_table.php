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
        Schema::table('casts', function (Blueprint $table) {
            $table->decimal('points_holded', 8, 2)->default(0)->after('siblings');
            $table->decimal('point_rate', 8, 2)->default(0)->after('points_holded');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('casts', function (Blueprint $table) {
            $table->dropColumn('points_holded');
            $table->dropColumn('point_rate');
        });
    }
};

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
            $table->integer('live_status')->default(112)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('casts', function (Blueprint $table) {
            $table->string('live_status')->nullable()->change();
        });
    }
};

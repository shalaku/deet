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
        Schema::table('cast_images', function (Blueprint $table) {
            $table->dropForeign(['cast_id']);

            $table->foreign('cast_id')
                ->references('id')
                ->on('casts')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cast_images', function (Blueprint $table) {
            $table->dropForeign(['cast_id']);

            $table->foreign('cast_id')
                ->references('id')
                ->on('casts');
        });
    }
};

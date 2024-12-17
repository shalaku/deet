<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('casts', function (Blueprint $table) {
            DB::statement('ALTER TABLE `casts` CHANGE `my_content` `my_comment` VARCHAR(255);');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('casts', function (Blueprint $table) {
            DB::statement('ALTER TABLE `casts` CHANGE `my_comment` `my_content` VARCHAR(255);');
        });
    }
};

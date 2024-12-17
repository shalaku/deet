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
            $table->string('prefecture_and_municipality')->nullable()->after('notices');
            $table->string('smoke')->nullable()->after('prefecture_and_municipality');
            $table->string('my_content')->nullable()->after('smoke');
            $table->boolean('siblings')->default(0)->after('my_content');
            $table->boolean('cohabitation')->default(0)->after('siblings');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('casts', function (Blueprint $table) {
            $table->dropColumn('cohabitation');
            $table->dropColumn('siblings');
            $table->dropColumn('my_content');
            $table->dropColumn('smoke');
            $table->dropColumn('prefecture_and_municipality');
        });
    }
};

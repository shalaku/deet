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
            $table->after('point_rate', function (Blueprint $table) {
                $table->string('live_status')->nullable();
                $table->decimal('basic_point_price', 10, 2)->nullable();
                $table->string('birthplace')->nullable();
                $table->string('final_academic_background')->nullable();
                $table->string('hair_style')->nullable();
                $table->string('hair_color')->nullable();
                $table->integer('score')->default(0);
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('casts', function (Blueprint $table) {
            $table->dropColumn([
                'live_status',
                'basic_point_price',
                'birthplace',
                'final_academic_background',
                'hair_style',
                'hair_color',
                'score',
            ]);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCastsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('casts', function (Blueprint $table) {
            $table->unsignedInteger('basic_point_price')->default(4000)->change();
            $table->unsignedBigInteger('points_holded')->default(0)->change();
            $table->unsignedSmallInteger('point_rate')->default(70)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('casts', function (Blueprint $table) {
            $table->decimal('basic_point_price', 10, 2)->nullable()->change();
            $table->decimal('points_holded', 8, 2)->default(0.00)->change();
            $table->decimal('point_rate', 8, 2)->default(0.00)->change();
        });
    }
}
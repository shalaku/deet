<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyPointsHoldedInCastsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('casts', function (Blueprint $table) {
            $table->integer('points_holded')->default(0)->change();
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
            $table->bigInteger('points_holded')->unsigned()->default(0)->change();
        });
    }
}
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCastRankBasePointTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cast_rank_base_point', function (Blueprint $table) {
            $table->id();
            $table->string('rank_name', 32)->unique();
            $table->unsignedInteger('point_amount');
            $table->timestamps();
        });

        DB::table('cast_rank_base_point')->insert([
            ['rank_name' => 'BLACK', 'point_amount' => 10000],
            ['rank_name' => 'PLATINUM', 'point_amount' => 8000],
            ['rank_name' => 'GOLD', 'point_amount' => 6000],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cast_rank_base_point');
    }
}

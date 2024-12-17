<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePointHistoriesTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('point_histories', function (Blueprint $table) {
            // point_amountを負の整数を許可し、NULLを許可しないように変更
            $table->integer('point_amount')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('point_histories', function (Blueprint $table) {
            // 元の状態に戻す
            $table->unsignedInteger('point_amount')->nullable(false)->change();
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePointHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('point_histories', function (Blueprint $table) {
            // NULLを許可するようにカラムを変更
            $table->integer('pay_amount')->nullable()->change();
            $table->string('payment_status', 64)->nullable()->change();
            $table->dateTime('sell_date_time')->nullable()->change();
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
            $table->integer('pay_amount')->nullable(false)->change();
            $table->string('payment_status', 64)->nullable(false)->change();
            $table->dateTime('sell_date_time')->nullable(false)->change();
        });
    }
}

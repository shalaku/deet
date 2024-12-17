<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMatchingOrderDetailsNullableColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('matching_order_details', function (Blueprint $table) {
            // extra_chargeカラムをNULLを許可するように変更
            $table->decimal('extra_charge', 10, 2)->nullable()->change();
            // designated_pointカラムをNULLを許可するように変更
            $table->decimal('designated_point', 10, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('matching_order_details', function (Blueprint $table) {
            // extra_chargeカラムをNOT NULLに戻す
            $table->decimal('extra_charge', 10, 2)->nullable(false)->default(0.00)->change();
            // designated_pointカラムをNOT NULLに戻す
            $table->decimal('designated_point', 10, 2)->nullable(false)->default(0.00)->change();
        });
    }
}

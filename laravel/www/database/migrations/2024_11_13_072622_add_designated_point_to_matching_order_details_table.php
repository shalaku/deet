<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDesignatedPointToMatchingOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('matching_order_details', function (Blueprint $table) {
            $table->decimal('designated_point', 10, 2)->default(0)->after('extra_charge');
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
            $table->dropColumn('designated_point');
        });
    }
}

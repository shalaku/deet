<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_extra_charge_to_matching_order_details_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraChargeToMatchingOrderDetailsTable extends Migration
{
    public function up()
    {
        Schema::table('matching_order_details', function (Blueprint $table) {
            $table->decimal('extra_charge', 10, 2)->default(0)->after('mid_night_fee');
        });
    }

    public function down()
    {
        Schema::table('matching_order_details', function (Blueprint $table) {
            $table->dropColumn('extra_charge');
        });
    }
}

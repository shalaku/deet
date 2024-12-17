<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDesignatedPointToRequestMatchingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('request_matching_details', function (Blueprint $table) {
            $table->decimal('designated_point', 10, 2)->default(0)->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('request_matching_details', function (Blueprint $table) {
            $table->dropColumn('designated_point');
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNumberOfPeopleToMatchingOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('matching_orders', function (Blueprint $table) {
            $table->unsignedInteger('number_of_people')->nullable()->after('end_date_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('matching_orders', function (Blueprint $table) {
            $table->dropColumn('number_of_people');
        });
    }
}

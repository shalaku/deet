<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('matching_order_details', function (Blueprint $table) {
            $table->longText('cast_memo')->nullable()->after('applied_point');
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
            $table->dropColumn('cast_memo');
        });
    }
};
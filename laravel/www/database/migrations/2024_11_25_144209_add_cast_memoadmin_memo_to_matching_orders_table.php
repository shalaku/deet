<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCastMemoadminMemoToMatchingOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('matching_orders', function (Blueprint $table) {
            $table->longText('cast_memo')->after('number_of_people')->nullable();
            $table->longText('admin_memo')->after('cast_memo')->nullable();
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
            $table->dropColumn('cast_memo');
            $table->dropColumn('admin_memo');
        });
    }
}
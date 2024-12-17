<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShownIdToRequestMatchingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('request_matchings', function (Blueprint $table) {
            $table->string('shown_id', 10)->nullable()->unique()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('request_matchings', function (Blueprint $table) {
            $table->dropColumn('shown_id');
        });
    }
}
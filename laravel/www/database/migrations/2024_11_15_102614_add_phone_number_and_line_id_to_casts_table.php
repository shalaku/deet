<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPhoneNumberAndLineIdToCastsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('casts', function (Blueprint $table) {
            $table->string('phone_number', 21)->nullable()->default(null)->after('building');
            $table->string('line_id', 64)->nullable()->default(null)->after('instagram_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('casts', function (Blueprint $table) {
            $table->dropColumn('phone_number');
            $table->dropColumn('line_id');
        });
    }
}

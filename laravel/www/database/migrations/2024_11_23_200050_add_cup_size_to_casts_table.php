<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCupSizeToCastsTable extends Migration
{
    public function up()
    {
        Schema::table('casts', function (Blueprint $table) {
            $table->string('cup_size', 3)->after('three_size_h')->nullable();
        });
    }

    public function down()
    {
        Schema::table('casts', function (Blueprint $table) {
            $table->dropColumn('cup_size');
        });
    }
}
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyStatusIdColumnInCustomersTable extends Migration
{
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['status_id']);
        });
    }

    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
        });
    }
}

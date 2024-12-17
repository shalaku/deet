<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddManagementTagsToCastsTable extends Migration
{
    public function up()
    {
        Schema::table('casts', function (Blueprint $table) {
            $table->longText('management_tags')->after('tag_of_spec')->nullable();
        });
    }

    public function down()
    {
        Schema::table('casts', function (Blueprint $table) {
            $table->dropColumn('management_tags');
        });
    }
}
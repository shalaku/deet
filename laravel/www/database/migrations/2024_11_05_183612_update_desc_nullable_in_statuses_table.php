<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDescNullableInStatusesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('statuses', function (Blueprint $table) {
            $table->string('desc')->nullable()->change(); // descをNULL許可に変更
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('statuses', function (Blueprint $table) {
            $table->string('desc')->nullable(false)->change(); // 元に戻す
        });
    }
}

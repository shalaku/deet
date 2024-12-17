<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNicknameToCastsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('casts', function (Blueprint $table) {
            $table->string('nickname', 64)->after('name_kana'); // name_kanaの次に追加
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('casts', function (Blueprint $table) {
            $table->dropColumn('nickname'); // nicknameカラムを削除
        });
    }
}

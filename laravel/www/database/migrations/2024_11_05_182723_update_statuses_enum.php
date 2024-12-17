<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStatusesEnum extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // status_categoryのenumを変更
        DB::statement("ALTER TABLE statuses MODIFY status_category ENUM('cast', 'cast_work', 'cast_live', 'request', 'request_detail', 'order', 'order_acception', 'customer', 'user')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 元のenumに戻す場合の処理
        DB::statement("ALTER TABLE statuses MODIFY status_category ENUM('cast', 'customer', 'user')");
    }
}

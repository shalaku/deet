<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statuses = [
            ['id' => 100, 'status_category' => 'cast', 'status_name' => '稼働'],
            ['id' => 101, 'status_category' => 'cast', 'status_name' => '一時停止'],
            ['id' => 102, 'status_category' => 'cast', 'status_name' => '強制停止'],
            ['id' => 103, 'status_category' => 'cast', 'status_name' => '削除済み'],
            ['id' => 110, 'status_category' => 'cast_work', 'status_name' => '待機'],
            ['id' => 111, 'status_category' => 'cast_work', 'status_name' => 'マッチング中'],
            ['id' => 112, 'status_category' => 'cast_work', 'status_name' => '業務外'],
            ['id' => 120, 'status_category' => 'cast_live', 'status_name' => 'オンライン'],
            ['id' => 121, 'status_category' => 'cast_live', 'status_name' => '離席'],
            ['id' => 122, 'status_category' => 'cast_live', 'status_name' => 'オフライン'],
            ['id' => 201, 'status_category' => 'customer', 'status_name' => '有効'],
            ['id' => 202, 'status_category' => 'customer', 'status_name' => '凍結'],
            ['id' => 203, 'status_category' => 'customer', 'status_name' => '削除済み'],
            ['id' => 301, 'status_category' => 'user', 'status_name' => '有効'],
            ['id' => 302, 'status_category' => 'user', 'status_name' => '凍結'],
            ['id' => 303, 'status_category' => 'user', 'status_name' => '削除済み'],
            ['id' => 501, 'status_category' => 'request', 'status_name' => '募集中'],
            ['id' => 502, 'status_category' => 'request', 'status_name' => '募集終了'],
            ['id' => 503, 'status_category' => 'request', 'status_name' => 'キャンセル'],
            ['id' => 504, 'status_category' => 'request', 'status_name' => '保留'],
            ['id' => 510, 'status_category' => 'request_detail', 'status_name' => '応募中'],
            ['id' => 511, 'status_category' => 'request_detail', 'status_name' => 'キャンセル'],
            ['id' => 512, 'status_category' => 'request_detail', 'status_name' => '確定'],
            ['id' => 513, 'status_category' => 'request_detail', 'status_name' => '指名'],
            ['id' => 514, 'status_category' => 'request_detail', 'status_name' => '落選'],
            ['id' => 600, 'status_category' => 'order', 'status_name' => '確定待ち'],
            ['id' => 601, 'status_category' => 'order', 'status_name' => '確定'],
            ['id' => 602, 'status_category' => 'order', 'status_name' => '進行中'],
            ['id' => 603, 'status_category' => 'order', 'status_name' => 'キャンセル'],
            ['id' => 604, 'status_category' => 'order', 'status_name' => '終了'],
            ['id' => 605, 'status_category' => 'order', 'status_name' => '拒否'],
            ['id' => 701, 'status_category' => 'order_acception', 'status_name' => '了承'],
            ['id' => 702, 'status_category' => 'order_acception', 'status_name' => '拒否'],
            ['id' => 703, 'status_category' => 'order_acception', 'status_name' => '確認中'],
        ];

        DB::table('statuses')->insert($statuses);
    }
}

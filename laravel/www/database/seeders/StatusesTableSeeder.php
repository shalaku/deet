<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('statuses')->truncate();

        DB::table('statuses')->insert([
            ['id' => 100, 'status_category' => 'cast', 'status_name' => '稼働', 'desc' => 'enabled'],
            ['id' => 101, 'status_category' => 'cast', 'status_name' => '一時停止', 'desc' => 'pause'],
            ['id' => 102, 'status_category' => 'cast', 'status_name' => '強制停止', 'desc' => 'stopped'],
            ['id' => 103, 'status_category' => 'cast', 'status_name' => '削除済み', 'desc' => 'deleated'],
            ['id' => 110, 'status_category' => 'cast_work', 'status_name' => '待機', 'desc' => 'standby'],
            ['id' => 111, 'status_category' => 'cast_work', 'status_name' => 'マッチング中', 'desc' => 'matching right now'],
            ['id' => 112, 'status_category' => 'cast_work', 'status_name' => '業務外', 'desc' => 'out of work time'],
            ['id' => 120, 'status_category' => 'cast_live', 'status_name' => 'オンライン', 'desc' => 'online(active)'],
            ['id' => 121, 'status_category' => 'cast_live', 'status_name' => '離席', 'desc' => 'not active but last few hours in active'],
            ['id' => 122, 'status_category' => 'cast_live', 'status_name' => 'オフライン', 'desc' => 'offline'],
            ['id' => 201, 'status_category' => 'customer', 'status_name' => '有効', 'desc' => 'enabled'],
            ['id' => 202, 'status_category' => 'customer', 'status_name' => '凍結', 'desc' => 'freeze'],
            ['id' => 203, 'status_category' => 'customer', 'status_name' => '削除済み', 'desc' => 'deleated'],
            ['id' => 301, 'status_category' => 'user', 'status_name' => '有効', 'desc' => 'enabled'],
            ['id' => 302, 'status_category' => 'user', 'status_name' => '凍結', 'desc' => 'freeze'],
            ['id' => 303, 'status_category' => 'user', 'status_name' => '削除済み', 'desc' => 'deleated'],
            ['id' => 501, 'status_category' => 'request', 'status_name' => '募集中', 'desc' => 'requesting'],
            ['id' => 502, 'status_category' => 'request', 'status_name' => '募集終了', 'desc' => 'request finished'],
            ['id' => 503, 'status_category' => 'request', 'status_name' => 'キャンセル', 'desc' => 'request cancel'],
            ['id' => 510, 'status_category' => 'request_detail', 'status_name' => '応募中', 'desc' => 'now applying（cast）'],
            ['id' => 511, 'status_category' => 'request_detail', 'status_name' => 'キャンセル', 'desc' => 'canceled'],
            ['id' => 512, 'status_category' => 'request_detail', 'status_name' => '確定', 'desc' => 'confirmed'],
            ['id' => 600, 'status_category' => 'order', 'status_name' => '確定待ち', 'desc' => 'ordered'],
            ['id' => 601, 'status_category' => 'order', 'status_name' => '確定', 'desc' => 'confirmed'],
            ['id' => 602, 'status_category' => 'order', 'status_name' => '進行中', 'desc' => 'in_progress'],
            ['id' => 603, 'status_category' => 'order', 'status_name' => 'キャンセル', 'desc' => 'cancel'],
            ['id' => 604, 'status_category' => 'order', 'status_name' => '終了', 'desc' => 'finished'],
            ['id' => 605, 'status_category' => 'order', 'status_name' => '拒否', 'desc' => 'rejected'],
            ['id' => 701, 'status_category' => 'order_acception', 'status_name' => '了承', 'desc' => 'accepted'],
            ['id' => 702, 'status_category' => 'order_acception', 'status_name' => '拒否', 'desc' => 'rejected'],
            ['id' => 703, 'status_category' => 'order_acception', 'status_name' => '確認中', 'desc' => 'pending'],
        ]);
    }
}

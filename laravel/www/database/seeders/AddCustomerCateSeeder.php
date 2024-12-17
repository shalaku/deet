<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddCustomerCateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $category = [
            ['id' => 1, 'category_name' => '一般男性'],
        ];

        DB::table('customer_category_lists')->insert($category);
    }
}

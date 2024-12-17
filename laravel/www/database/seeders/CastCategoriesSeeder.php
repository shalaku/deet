<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CastCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate the table
        // DB::table('cast_categories')->truncate();

        // Insert new categories
        DB::table('cast_categories')->insert([
            ['category_name' => '一般女性'],
            ['category_name' => '芸能人'],
        ]);
    }
}
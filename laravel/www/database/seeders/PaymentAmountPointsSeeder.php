<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentAmountPointsSeeder extends Seeder
{
    public function run()
    {
        $points = [
            1000,
            3000,
            5000,
            10000,
            30000,
            55000,
            50000,
            90000,
            100000,
            200000,
            300000,
            400000,
            500000,
        ];

        foreach ($points as $point) {
            DB::table('payment_amount_points')->insert([
                'points' => $point,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

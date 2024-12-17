<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
//use App\Models\AccountList;
use App\Models\User;
//use App\Models\UserList;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AddAdminOkadaSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

//        $account = AccountList::create([
//            'name_ja' => 'Owner',
//        ]);

        User::updateOrCreate([
            'name_ja' => 'OkadaAdmin',
            'uuid' => (string) Str::uuid(),
            'email' => 'y.okada@deet.jp',
            'password' => Hash::make('L|FKAb&R3BjA'),
            'account_type' => 'admin',
            'status' => 1,
            'user_type' => 'admin',
        ]);

//        $this->call([
//            StatusSeeder::class,
//            ImagesizesSeeder::class,
//        ]);
    }
}

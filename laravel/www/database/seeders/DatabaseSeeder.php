<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
//use App\Models\AccountList;
use App\Models\User;
//use App\Models\UserList;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
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
            'name_ja' => 'Admin',
            'uuid' => (string) Str::uuid(),
            'email' => 'admin@admin.com',
            'password' => Hash::make('password123456'),
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

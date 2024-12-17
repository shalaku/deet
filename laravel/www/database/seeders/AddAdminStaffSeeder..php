<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
//use App\Models\AccountList;
use App\Models\User;
//use App\Models\UserList;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AddAdminStaffSeeder extends Seeder
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
            'name_ja' => 'AdminStaff-1',
            'uuid' => (string) Str::uuid(),
            'email' => 'staff1@deet.jp',
            'password' => Hash::make('S2jNJ),,AABM'),
            'account_type' => 'admin',
            'status' => 1,
            'user_type' => 'staff',
        ]);
        User::updateOrCreate([
            'name_ja' => 'AdminStaff-2',
            'uuid' => (string) Str::uuid(),
            'email' => 'staff2@deet.jp',
            'password' => Hash::make('4nS5-)*f%xTK'),
            'account_type' => 'admin',
            'status' => 1,
            'user_type' => 'staff',
        ]);
        User::updateOrCreate([
            'name_ja' => 'AdminStaff-3',
            'uuid' => (string) Str::uuid(),
            'email' => 'staff3@deet.jp',
            'password' => Hash::make('q7wqG~pegxtz'),
            'account_type' => 'admin',
            'status' => 1,
            'user_type' => 'staff',
        ]);                

//        $this->call([
//            StatusSeeder::class,
//            ImagesizesSeeder::class,
//        ]);
    }
}

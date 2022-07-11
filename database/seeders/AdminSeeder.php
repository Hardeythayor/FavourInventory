<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'firstname' => 'Admin',
            'lastname' => "Admin",
            'middlename' => "A",
            'user_type' => 'admin',
            'email' => "admin@careernck.com",
            'password' => Hash::make("12345678")
         ]);
    }
}

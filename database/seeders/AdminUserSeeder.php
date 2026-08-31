<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@cc.cc',
            'password' => Hash::make('123456'),
            'role' => 'Admin',
            'login_enabled' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

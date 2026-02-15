<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KantinUserSeeder extends Seeder
{
    public function run(): void
    {
        if (!DB::table('user')->where('username', 'admin')->exists()) {
            DB::table('user')->insert([
                'username' => 'admin',
                'password' => password_hash('admin123', PASSWORD_BCRYPT),
                'level' => 1,
                'Kios' => 'Admin',
            ]);
        }
    }
}

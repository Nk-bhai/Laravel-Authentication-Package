<?php

namespace Nk\SystemAuth\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::connection('mysql')->table('users')->insert([
            'email' => 'nk@gmail.com',
            'password' => Hash::make('Nk@12345'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
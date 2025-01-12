<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
            ],
            [
                'name' => 'nouran',
                'email' => 'nouranessam782@gmail.com',
                'password' => Hash::make('nouran123'),
                'role' => 'user',
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ],
            [
                'name' => 'mayar',
                'email' => 'mayar@gmail.com',
                'password' => Hash::make('nouran123'),
                'role' => 'user',
            ],
        ]);
    }
}

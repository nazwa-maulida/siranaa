<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // Insert Admin
        DB::table('users')->insert([
            'username' => 'Admin', // Ganti 'name' menjadi 'username'
            'email' => 'admin@example.com', // Email default admin
            'password' => Hash::make('admin123'), // Password default admin
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert User Mitra
        DB::table('mitra')->insert([
            'username' => 'mitra default', // Ganti 'name' menjadi 'username'
            'email' => 'mitra@example.com',
            'password' => Hash::make('mitra123'),
            'role' => 'mitra',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert Perusahaan
        DB::table('perusahaan')->insert([
            'username' => 'Perusahaan default', // Ganti 'name' menjadi 'username'
            'email' => 'perusahaan@example.com',
            'password' => Hash::make('perusahaan123'),
            'role' => 'perusahaan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

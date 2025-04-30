<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Membuat admin
        User::create([
            'name' => 'Admin Winnicode',
            'email' => 'admin@winnicode.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Membuat user biasa
        User::create([
            'name' => 'Ahmad Fauzi',
            'email' => 'ahmad@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Import model User
use Illuminate\Support\Facades\Hash; // Import Hash facade

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@gmail.com', // Ganti dengan email admin Anda
            'password' => Hash::make('admin123'), // Ganti dengan password admin yang kuat
            'is_admin' => true, // Set ini menjadi true untuk admin
        ]);

        // Opsional: Buat beberapa user biasa juga
        User::create([
            'name' => 'User Biasa 1',
            'email' => 'user1@gmail.com',
            'password' => Hash::make('usera123'),
            'is_admin' => false,
        ]);

        User::create([
            'name' => 'User Biasa 2',
            'email' => 'user2@gmail.com',
            'password' => Hash::make('userb123'),
            'is_admin' => false,
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Local development admin. Change the password after the first login.
        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'role_name' => Role::SuperAdmin,
            'password' => 'Password@123',
        ]);
    }
}

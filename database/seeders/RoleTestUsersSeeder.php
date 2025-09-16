<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleTestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin user
        \App\Models\User::firstOrCreate(
            ['email' => 'superadmin@baltbep.com'],
            [
                'name' => 'Super Administrator',
                'first_name' => 'Super',
                'last_name' => 'Administrator',
                'username' => 'superadmin',
                'password' => \Illuminate\Support\Facades\Hash::make('superadmin123'),
                'role' => 'super_admin',
                'email_verified_at' => now(),
            ]
        );

        // Create Admin user
        \App\Models\User::firstOrCreate(
            ['email' => 'admin@baltbep.com'],
            [
                'name' => 'Administrator',
                'first_name' => 'Admin',
                'last_name' => 'User',
                'username' => 'admin',
                'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Create Customer user
        \App\Models\User::firstOrCreate(
            ['email' => 'customer@baltbep.com'],
            [
                'name' => 'Customer User',
                'first_name' => 'Customer',
                'last_name' => 'User',
                'username' => 'customer',
                'password' => \Illuminate\Support\Facades\Hash::make('customer123'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Created test users:');
        $this->command->info('Super Admin: superadmin@baltbep.com / superadmin123');
        $this->command->info('Admin: admin@baltbep.com / admin123');
        $this->command->info('Customer: customer@baltbep.com / customer123');
    }
}

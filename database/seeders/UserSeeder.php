<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure there's at least one superadmin
        if (!User::where('role', 'super_admin')->exists()) {
            User::create([
                'name' => 'System Super Admin',
                'email' => 'superadmin@system.com',
                'password' => Hash::make('SuperAdmin123!'),
                'role' => 'super_admin',
            ]);
        }
    }
}

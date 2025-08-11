<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Retrieve the admin role
        $adminRole = Role::where('name', 'admin')->first();

        // Create admin user
        $admin = User::firstOrCreate(
            [
                'email' => 'admin@example.com'
            ],
            [
                'name' => 'System Administrator',
                'password' => bcrypt('StrongPassword123!'),
                // Add other required fields
            ]
        );

        // Assign role
        if (!$admin->hasRole('admin')) {
            $admin->attachRole($adminRole);
        }
    }
}
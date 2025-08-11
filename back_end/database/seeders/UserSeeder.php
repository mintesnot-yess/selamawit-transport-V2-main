<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;

use Hash;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // register  user

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
            $admin->addRole('admin');
        }







    }
}

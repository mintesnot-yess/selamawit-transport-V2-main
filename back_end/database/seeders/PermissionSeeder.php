<?php


namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $modules = [
            "Order",
            "Payment Collection",
            "Client",
            "Expense",
            "Expense Type",
            "Vehicles",
            "Employee",
            "Location",
            "Load Type",
            "Bank",
            "Bank Account",
            "System User",
            "Role",
            "Permission",
            "Report",
            "Log"
        ];

        $actions = ['view', 'create', 'edit', 'delete']; // common CRUD actions

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                $permissionName = strtolower($action . '-' . str_replace(' ', '-', $module));

                Permission::firstOrCreate([
                    'name' => $permissionName,
                    'display_name' => ucfirst($action) . ' ' . $module,
                    'description' => ucfirst($action) . ' ' . $module,
                ]);
            }
        }
        // Create roles and assign permissions
        $adminPermissions = Permission::all();
        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'display_name' => 'Administrator'
        ]);
        $admin->syncPermissions($adminPermissions);

    }
}

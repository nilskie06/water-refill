<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions grouped by module
        $modules = ['customers', 'orders', 'deliveries', 'payments', 'reports', 'drivers', 'vehicles'];
        $actions = ['view', 'create', 'edit', 'delete', 'export'];

        $permissionIds = [];
        foreach ($modules as $module) {
            foreach ($actions as $action) {
                $p = Permission::create([
                    'name' => "{$action}_{$module}",
                    'group' => $module,
                    'description' => ucfirst($action) . ' ' . ucfirst($module),
                ]);
                $permissionIds[] = $p->id;
            }
        }

        // Create roles
        $admin = Role::create(['name' => 'admin', 'description' => 'Full system access']);
        $manager = Role::create(['name' => 'manager', 'description' => 'Manage operations and reports']);
        $staff = Role::create(['name' => 'staff', 'description' => 'Basic access to orders and deliveries']);
        $driver = Role::create(['name' => 'driver', 'description' => 'Delivery driver access']);

        // Admin gets all permissions
        $admin->permissions()->sync($permissionIds);

        // Manager gets most permissions (no delete on reports)
        $managerPerms = Permission::whereNotIn('name', ['delete_reports', 'export_reports'])->pluck('id')->toArray();
        $manager->permissions()->sync($managerPerms);

        // Staff gets view/create on orders and deliveries
        $staffPerms = Permission::whereIn('name', [
            'view_customers', 'create_customers',
            'view_orders', 'create_orders',
            'view_deliveries', 'create_deliveries',
            'view_payments', 'create_payments',
            'view_vehicles',
            'view_drivers',
        ])->pluck('id')->toArray();
        $staff->permissions()->sync($staffPerms);

        // Driver gets delivery-related only
        $driverPerms = Permission::whereIn('name', [
            'view_deliveries', 'edit_deliveries',
            'view_customers',
            'view_orders',
        ])->pluck('id')->toArray();
        $driver->permissions()->sync($driverPerms);

        // Update existing users
        User::where('role', 'admin')->update(['role_id' => $admin->id]);
        User::where('role', 'staff')->update(['role_id' => $staff->id]);
    }
}

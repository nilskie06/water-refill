<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        Menu::truncate();

        // Top-level menus
        $dashboard = Menu::create(['name' => 'Dashboard', 'url' => '/dashboard', 'icon' => '🏠', 'position' => 0, 'is_active' => true]);
        $customers = Menu::create(['name' => 'Customers', 'url' => '/customers', 'icon' => '👥', 'position' => 1, 'is_active' => true]);
        $orders = Menu::create(['name' => 'Orders', 'url' => '/orders', 'icon' => '📦', 'position' => 2, 'is_active' => true]);
        $payments = Menu::create(['name' => 'Payments', 'url' => '/payments', 'icon' => '💰', 'position' => 3, 'is_active' => true]);
        $bottles = Menu::create(['name' => 'Bottles', 'url' => '/bottles', 'icon' => '🍶', 'position' => 4, 'is_active' => true]);

        // Deliveries (parent with sub-menus)
        $deliveries = Menu::create(['name' => 'Deliveries', 'url' => '/deliveries', 'icon' => '🚚', 'position' => 5, 'is_active' => true]);
        Menu::create(['name' => 'Delivery List', 'url' => '/deliveries', 'icon' => '📋', 'parent_id' => $deliveries->id, 'position' => 0, 'is_active' => true]);
        Menu::create(['name' => 'Calendar', 'url' => '/deliveries/calendar', 'icon' => '🗓️', 'parent_id' => $deliveries->id, 'position' => 1, 'is_active' => true]);
        Menu::create(['name' => 'Routes', 'url' => '/deliveries/routes', 'icon' => '🗺️', 'parent_id' => $deliveries->id, 'position' => 2, 'is_active' => true]);
        Menu::create(['name' => 'Drivers', 'url' => '/drivers', 'icon' => '👤', 'parent_id' => $deliveries->id, 'position' => 3, 'is_active' => true]);
        Menu::create(['name' => 'Vehicles', 'url' => '/vehicles', 'icon' => '🚐', 'parent_id' => $deliveries->id, 'position' => 4, 'is_active' => true]);
        Menu::create(['name' => 'History', 'url' => '/deliveries/history', 'icon' => '📋', 'parent_id' => $deliveries->id, 'position' => 5, 'is_active' => true]);

        // Reports
        $reports = Menu::create(['name' => 'Reports', 'url' => '/reports', 'icon' => '📈', 'position' => 6, 'is_active' => true]);

        // Admin (parent with sub-menus)
        $admin = Menu::create(['name' => 'Admin', 'url' => null, 'icon' => '⚙️', 'position' => 7, 'is_active' => true]);
        Menu::create(['name' => 'Roles', 'url' => '/admin/roles', 'icon' => '🔐', 'parent_id' => $admin->id, 'position' => 0, 'is_active' => true]);
        Menu::create(['name' => 'Permissions', 'url' => '/admin/permissions', 'icon' => '🔑', 'parent_id' => $admin->id, 'position' => 1, 'is_active' => true]);
        Menu::create(['name' => 'Users', 'url' => '/admin/users', 'icon' => '👤', 'parent_id' => $admin->id, 'position' => 2, 'is_active' => true]);
        Menu::create(['name' => 'Menu Builder', 'url' => '/admin/menus', 'icon' => '📋', 'parent_id' => $admin->id, 'position' => 3, 'is_active' => true]);
    }
}

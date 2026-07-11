<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // Top-level menus
        $dashboard = Menu::create(['name' => 'Dashboard', 'url' => '/dashboard', 'icon' => '🏠', 'position' => 0]);
        $customers = Menu::create(['name' => 'Customers', 'url' => '/customers', 'icon' => '👥', 'position' => 1]);
        $orders = Menu::create(['name' => 'Orders', 'url' => '/orders', 'icon' => '📦', 'position' => 2]);
        $bottles = Menu::create(['name' => 'Bottles', 'url' => '/bottles', 'icon' => '🍶', 'position' => 3]);
        $payments = Menu::create(['name' => 'Payments', 'url' => '/payments', 'icon' => '💰', 'position' => 4]);

        // Deliveries (with sub-menus)
        $deliveries = Menu::create(['name' => 'Deliveries', 'url' => null, 'icon' => '🚚', 'position' => 5]);
        Menu::create(['name' => 'Delivery List', 'url' => '/deliveries', 'icon' => '📋', 'parent_id' => $deliveries->id, 'position' => 0]);
        Menu::create(['name' => 'Calendar', 'url' => '/deliveries/calendar', 'icon' => '🗓️', 'parent_id' => $deliveries->id, 'position' => 1]);
        Menu::create(['name' => 'Routes', 'url' => '/deliveries/routes', 'icon' => '🗺️', 'parent_id' => $deliveries->id, 'position' => 2]);
        Menu::create(['name' => 'History', 'url' => '/deliveries/history', 'icon' => '📋', 'parent_id' => $deliveries->id, 'position' => 3]);

        $reports = Menu::create(['name' => 'Reports', 'url' => '/reports', 'icon' => '📈', 'position' => 6]);

        // Admin (with sub-menus)
        $admin = Menu::create(['name' => 'Admin', 'url' => null, 'icon' => '⚙️', 'position' => 7]);
        Menu::create(['name' => 'Roles', 'url' => '/admin/roles', 'icon' => '🔐', 'parent_id' => $admin->id, 'position' => 0]);
        Menu::create(['name' => 'Permissions', 'url' => '/admin/permissions', 'icon' => '🔑', 'parent_id' => $admin->id, 'position' => 1]);
        Menu::create(['name' => 'Users', 'url' => '/admin/users', 'icon' => '👤', 'parent_id' => $admin->id, 'position' => 2]);
        Menu::create(['name' => 'Menu Builder', 'url' => '/admin/menus', 'icon' => '📋', 'parent_id' => $admin->id, 'position' => 3]);
    }
}

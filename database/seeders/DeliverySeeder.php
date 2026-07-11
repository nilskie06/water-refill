<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\Delivery;
use Illuminate\Database\Seeder;

class DeliverySeeder extends Seeder
{
    public function run(): void
    {
        // Sample Drivers
        $drivers = [
            ['name' => 'Juan dela Cruz', 'contact_number' => '09171234567', 'license_number' => 'N01-12-345678', 'status' => 'active'],
            ['name' => 'Pedro Santos', 'contact_number' => '09181234567', 'license_number' => 'N01-12-345679', 'status' => 'active'],
            ['name' => 'Miguel Reyes', 'contact_number' => '09191234567', 'license_number' => 'N01-12-345680', 'status' => 'active'],
            ['name' => 'Jose Ramos', 'contact_number' => '09201234567', 'license_number' => 'N01-12-345681', 'status' => 'inactive'],
        ];

        foreach ($drivers as $d) {
            Driver::create($d);
        }

        // Sample Vehicles
        $vehicles = [
            ['plate_number' => 'ABC 1234', 'description' => 'Honda TMX 155', 'capacity' => 10, 'status' => 'available'],
            ['plate_number' => 'DEF 5678', 'description' => 'Honda XR150', 'capacity' => 8, 'status' => 'available'],
            ['plate_number' => 'GHI 9012', 'description' => 'Suzuki Carry', 'capacity' => 30, 'status' => 'available'],
            ['plate_number' => 'JKL 3456', 'description' => 'Toyota HiAce', 'capacity' => 50, 'status' => 'in_use'],
        ];

        foreach ($vehicles as $v) {
            Vehicle::create($v);
        }

        // Sample Deliveries
        $customers = Customer::all();
        if ($customers->count() === 0) return;

        $statuses = ['scheduled', 'assigned', 'out_for_delivery', 'delivered', 'failed'];
        $types = ['regular', 'rush', 'scheduled', 'pickup'];
        $routes = ['morning', 'afternoon', 'evening'];

        for ($i = 0; $i < 15; $i++) {
            $customer = $customers->random();
            $date = now()->addDays(rand(-5, 10));
            $status = $statuses[array_rand($statuses)];
            $driverId = in_array($status, ['assigned', 'out_for_delivery', 'delivered']) ? Driver::active()->pluck('id')->random() : null;
            $vehicleId = $driverId ? Vehicle::where('status', '!=', 'maintenance')->pluck('id')->random() : null;

            Delivery::create([
                'delivery_no' => Delivery::generateDeliveryNo(),
                'customer_id' => $customer->id,
                'driver_id' => $driverId,
                'vehicle_id' => $vehicleId,
                'delivery_date' => $date->format('Y-m-d'),
                'delivery_time' => sprintf('%02d:%02d', rand(6, 17), [0, 15, 30, 45][array_rand([0, 15, 30, 45])]),
                'address' => $customer->address ?? 'Barangay ' . rand(1, 50) . ', Paranaque City',
                'contact_number' => $customer->contact,
                'quantity' => rand(1, 10),
                'delivery_type' => $types[array_rand($types)],
                'route' => $routes[array_rand($routes)],
                'status' => $status,
                'remarks' => $status === 'failed' ? 'Customer not available' : null,
            ]);
        }
    }
}

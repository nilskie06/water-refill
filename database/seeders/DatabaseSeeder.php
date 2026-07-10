<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@waterrefill.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create staff user
        User::create([
            'name' => 'Staff',
            'email' => 'staff@waterrefill.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
        ]);

        // Create sample customers
        $customers = [
            ['name' => 'Juan Dela Cruz', 'contact' => '09171234567', 'address' => '123 Main St, Barangay San Jose', 'notes' => 'VIP customer'],
            ['name' => 'Maria Santos', 'contact' => '09181234567', 'address' => '456 Oak Ave, Barangay San Miguel', 'notes' => ''],
            ['name' => 'Pedro Reyes', 'contact' => '09191234567', 'address' => '789 Pine Rd, Barangay San Isidro', 'notes' => 'Prefers morning delivery'],
            ['name' => 'Ana Garcia', 'contact' => '09201234567', 'address' => '321 Elm St, Barangay San Antonio', 'notes' => ''],
            ['name' => 'Carlos Mendoza', 'contact' => '09211234567', 'address' => '654 Cedar Ln, Barangay San Pedro', 'notes' => 'Bulk orders'],
        ];

        foreach ($customers as $data) {
            Customer::create($data);
        }

        // Create sample orders and payments
        $customerIds = Customer::pluck('id');
        $statuses = ['pending', 'delivered', 'completed', 'completed'];
        $methods = ['cash', 'gcash', 'maya', 'bank_transfer'];

        for ($i = 0; $i < 15; $i++) {
            $customerId = $customerIds->random();
            $quantity = rand(1, 5);
            $unitPrice = 25.00;
            $total = $quantity * $unitPrice;
            $status = $statuses[array_rand($statuses)];

            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'customer_id' => $customerId,
                'order_date' => now()->subDays(rand(0, 7))->toDateString(),
                'product' => 'Pure Water Gallon',
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'delivery_type' => rand(0, 1) ? 'pickup' : 'delivery',
                'bottle_in' => rand(0, $quantity),
                'bottle_out' => $quantity,
                'total' => $total,
                'status' => $status,
            ]);

            // Create payment if completed or delivered
            if (in_array($status, ['completed', 'delivered'])) {
                Payment::create([
                    'order_id' => $order->id,
                    'amount' => $total,
                    'payment_method' => $methods[array_rand($methods)],
                    'payment_date' => $order->order_date,
                ]);
            } elseif ($status === 'pending' && rand(0, 1)) {
                Payment::create([
                    'order_id' => $order->id,
                    'amount' => $total / 2,
                    'payment_method' => $methods[array_rand($methods)],
                    'payment_date' => $order->order_date,
                ]);
            }
        }

        echo "✅ Database seeded successfully!\n";
        echo "   Admin: admin@waterrefill.com / password\n";
        echo "   Staff: staff@waterrefill.com / password\n";
    }
}

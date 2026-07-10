<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        // Today's sales
        $todaySales = Order::where('order_date', $today)->sum('total');

        // Today's orders
        $todayOrders = Order::where('order_date', $today)->count();

        // Outstanding payments
        $outstanding = Order::where('status', '!=', 'cancelled')
            ->get()
            ->filter(function ($order) {
                return $order->balance > 0;
            })
            ->sum('balance');

        // Recent orders
        $recentOrders = Order::with('customer')
            ->latest()
            ->limit(10)
            ->get();

        // Total customers
        $totalCustomers = Customer::count();

        // Bottles out (unreturned)
        $bottlesOut = \App\Models\BottleBalance::sum('balance');

        return response()->json([
            'today_sales' => $todaySales,
            'today_orders' => $todayOrders,
            'outstanding_payments' => $outstanding,
            'total_customers' => $totalCustomers,
            'bottles_out' => $bottlesOut,
            'recent_orders' => $recentOrders,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Delivery;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function dailySales(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->toDateString();
        $dateTo = $request->date_to ?? $dateFrom;

        // Orders
        $totalOrders = Order::whereBetween('order_date', [$dateFrom, $dateTo])->count();
        $totalBottles = Order::whereBetween('order_date', [$dateFrom, $dateTo])->sum('bottle_out');
        $grossSales = Order::whereBetween('order_date', [$dateFrom, $dateTo])->sum('total');

        // Payments
        $paymentsReceived = Payment::whereBetween('payment_date', [$dateFrom, $dateTo])->sum('amount');
        $outstandingBalance = $grossSales - $paymentsReceived;

        // Deliveries
        $deliveryQuery = Delivery::whereDate('delivery_date', '>=', $dateFrom)
            ->whereDate('delivery_date', '<=', $dateTo);

        $totalDeliveries = $deliveryQuery->count();
        $completedDeliveries = (clone $deliveryQuery)->where('status', 'delivered')->count();
        $pendingDeliveries = (clone $deliveryQuery)->whereIn('status', ['scheduled', 'assigned'])->count();
        $outForDelivery = (clone $deliveryQuery)->where('status', 'out_for_delivery')->count();
        $failedDeliveries = (clone $deliveryQuery)->where('status', 'failed')->count();
        $cancelledDeliveries = (clone $deliveryQuery)->where('status', 'cancelled')->count();
        $deliveryBottles = (clone $deliveryQuery)->where('status', 'delivered')->sum('quantity');

        // Top customers
        $topCustomers = Order::whereBetween('order_date', [$dateFrom, $dateTo])
            ->select('customer_id', DB::raw('SUM(total) as total_spent'), DB::raw('COUNT(*) as order_count'))
            ->groupBy('customer_id')
            ->with('customer:id,name')
            ->orderByDesc('total_spent')
            ->limit(5)
            ->get();

        // Orders by status
        $ordersByStatus = Order::whereBetween('order_date', [$dateFrom, $dateTo])
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        // Payments by method
        $paymentsByMethod = Payment::whereBetween('payment_date', [$dateFrom, $dateTo])
            ->select('payment_method', DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->get();

        // Top drivers by deliveries completed
        $topDrivers = Delivery::whereDate('delivery_date', '>=', $dateFrom)
            ->whereDate('delivery_date', '<=', $dateTo)
            ->where('status', 'delivered')
            ->select('driver_id', DB::raw('COUNT(*) as delivery_count'), DB::raw('SUM(quantity) as total_bottles'))
            ->groupBy('driver_id')
            ->with('driver:id,name')
            ->orderByDesc('delivery_count')
            ->limit(5)
            ->get();

        return response()->json([
            'period' => ['from' => $dateFrom, 'to' => $dateTo],
            'summary' => [
                'total_orders' => $totalOrders,
                'total_bottles_sold' => $totalBottles,
                'gross_sales' => $grossSales,
                'payments_received' => $paymentsReceived,
                'outstanding_balance' => $outstandingBalance,
            ],
            'deliveries' => [
                'total' => $totalDeliveries,
                'completed' => $completedDeliveries,
                'pending' => $pendingDeliveries,
                'out_for_delivery' => $outForDelivery,
                'failed' => $failedDeliveries,
                'cancelled' => $cancelledDeliveries,
                'bottles_delivered' => $deliveryBottles,
            ],
            'top_customers' => $topCustomers,
            'orders_by_status' => $ordersByStatus,
            'payments_by_method' => $paymentsByMethod,
            'top_drivers' => $topDrivers,
        ]);
    }
}

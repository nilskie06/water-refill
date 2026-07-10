<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function dailySales(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->toDateString();
        $dateTo = $request->date_to ?? $dateFrom;

        // Total orders
        $totalOrders = Order::whereBetween('order_date', [$dateFrom, $dateTo])->count();

        // Total bottles sold
        $totalBottles = Order::whereBetween('order_date', [$dateFrom, $dateTo])->sum('bottle_out');

        // Gross sales
        $grossSales = Order::whereBetween('order_date', [$dateFrom, $dateTo])->sum('total');

        // Payments received
        $paymentsReceived = Payment::whereBetween('payment_date', [$dateFrom, $dateTo])->sum('amount');

        // Outstanding balance
        $outstandingBalance = $grossSales - $paymentsReceived;

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

        return response()->json([
            'period' => ['from' => $dateFrom, 'to' => $dateTo],
            'summary' => [
                'total_orders' => $totalOrders,
                'total_bottles_sold' => $totalBottles,
                'gross_sales' => $grossSales,
                'payments_received' => $paymentsReceived,
                'outstanding_balance' => $outstandingBalance,
            ],
            'top_customers' => $topCustomers,
            'orders_by_status' => $ordersByStatus,
            'payments_by_method' => $paymentsByMethod,
        ]);
    }
}

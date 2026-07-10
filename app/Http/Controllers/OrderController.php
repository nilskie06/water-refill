<?php

namespace App\Http\Controllers;

use App\Models\BottleBalance;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('customer');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->date_from) {
            $query->where('order_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->where('order_date', '<=', $request->date_to);
        }

        $orders = $query->latest()->paginate(20);

        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'product' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'delivery_type' => 'in:pickup,delivery',
            'bottle_in' => 'nullable|integer|min:0',
            'bottle_out' => 'nullable|integer|min:0',
        ]);

        $bottleOut = $request->bottle_out ?? $request->quantity;
        $total = $request->quantity * $request->unit_price;

        $order = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'customer_id' => $request->customer_id,
            'order_date' => now()->toDateString(),
            'product' => $request->product ?? 'Pure Water Gallon',
            'quantity' => $request->quantity,
            'unit_price' => $request->unit_price,
            'delivery_type' => $request->delivery_type ?? 'pickup',
            'bottle_in' => $request->bottle_in ?? 0,
            'bottle_out' => $bottleOut,
            'total' => $total,
            'status' => 'pending',
        ]);

        // Update bottle balance
        BottleBalance::updateForCustomer(
            $request->customer_id,
            $bottleOut,
            $request->bottle_in ?? 0
        );

        return response()->json($order->load('customer'), 201);
    }

    public function show(Order $order)
    {
        return response()->json($order->load(['customer', 'payments']));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'in:pending,delivered,completed,cancelled',
            'quantity' => 'integer|min:1',
            'unit_price' => 'numeric|min:0',
        ]);

        if ($request->has('status')) {
            $order->status = $request->status;
        }

        if ($request->has('quantity') || $request->has('unit_price')) {
            $order->quantity = $request->quantity ?? $order->quantity;
            $order->unit_price = $request->unit_price ?? $order->unit_price;
            $order->total = $order->quantity * $order->unit_price;
        }

        $order->save();

        return response()->json($order->load('customer'));
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return response()->json(['message' => 'Order deleted']);
    }
}

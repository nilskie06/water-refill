<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('order.customer');

        if ($request->order_id) {
            $query->where('order_id', $request->order_id);
        }

        if ($request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->date_from) {
            $query->where('payment_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->where('payment_date', '<=', $request->date_to);
        }

        $payments = $query->latest()->paginate(20);

        return response()->json($payments);
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,gcash,maya,bank_transfer',
            'payment_date' => 'required|date',
        ]);

        $order = Order::findOrFail($request->order_id);
        $amountPaid = $order->payments()->sum('amount') + $request->amount;

        if ($amountPaid > $order->total) {
            return response()->json([
                'message' => 'Payment exceeds order total',
                'order_total' => $order->total,
                'already_paid' => $order->payments()->sum('amount'),
            ], 422);
        }

        $payment = Payment::create([
            'order_id' => $request->order_id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'payment_date' => $request->payment_date,
        ]);

        // Auto-complete order if fully paid
        if ($amountPaid >= $order->total && $order->status !== 'cancelled') {
            $order->status = 'completed';
            $order->save();
        }

        return response()->json($payment->load('order.customer'), 201);
    }

    public function show(Payment $payment)
    {
        return response()->json($payment->load('order.customer'));
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();

        return response()->json(['message' => 'Payment deleted']);
    }
}

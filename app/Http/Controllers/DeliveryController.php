<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\Order;
use App\Models\BottleBalance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DeliveryController extends Controller
{
    public function index(Request $request)
    {
        $query = Delivery::with(['customer', 'driver', 'vehicle', 'order']);

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->date) {
            $query->whereDate('delivery_date', $request->date);
        }
        if ($request->driver_id) {
            $query->where('driver_id', $request->driver_id);
        }
        if ($request->search) {
            $query->where('delivery_no', 'like', "%{$request->search}%")
                  ->orWhereHas('customer', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
        }

        $deliveries = $query->orderByDesc('delivery_date')->orderByDesc('id')->paginate(20);
        return response()->json($deliveries);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_id' => 'nullable|exists:orders,id',
            'delivery_date' => 'required|date',
            'delivery_time' => 'nullable',
            'address' => 'required|string',
            'contact_number' => 'nullable|string|max:20',
            'quantity' => 'required|integer|min:1',
            'delivery_type' => 'in:regular,rush,scheduled,pickup',
            'route' => 'nullable|in:morning,afternoon,evening',
            'remarks' => 'nullable|string',
        ]);

        $data['delivery_no'] = Delivery::generateDeliveryNo();
        $data['status'] = 'scheduled';

        // If linked to order, sync order status
        if (!empty($data['order_id'])) {
            $order = Order::find($data['order_id']);
            if ($order && $order->status !== 'completed') {
                $order->update(['status' => 'delivered']);
            }
        }

        $delivery = Delivery::create($data);
        return response()->json($delivery->load(['customer', 'driver', 'vehicle', 'order']), 201);
    }

    public function show(Delivery $delivery)
    {
        return response()->json($delivery->load(['customer', 'driver', 'vehicle', 'order']));
    }

    public function update(Request $request, Delivery $delivery)
    {
        $data = $request->validate([
            'driver_id' => 'nullable|exists:drivers,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'status' => 'in:scheduled,assigned,out_for_delivery,delivered,failed,cancelled',
            'route' => 'nullable|in:morning,afternoon,evening',
            'remarks' => 'nullable|string',
            'delivery_date' => 'sometimes|date',
            'delivery_time' => 'nullable',
        ]);

        if (isset($data['driver_id']) && $data['driver_id'] && !isset($data['status'])) {
            $data['status'] = 'assigned';
        }

        $oldStatus = $delivery->status;
        $delivery->update($data);
        $newStatus = $delivery->status;

        if (isset($data['status'])) {
            // Vehicle status management
            if ($newStatus === 'out_for_delivery' && $delivery->vehicle_id) {
                Vehicle::where('id', $delivery->vehicle_id)->update(['status' => 'in_use']);
            }
            if (in_array($newStatus, ['delivered', 'failed', 'cancelled']) && $delivery->vehicle_id) {
                Vehicle::where('id', $delivery->vehicle_id)->update(['status' => 'available']);
            }

            // Order & bottle balance wiring
            if ($newStatus === 'delivered' && $oldStatus !== 'delivered') {
                $this->handleDeliveryCompleted($delivery);
            }
            if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                $this->handleDeliveryCancelled($delivery);
            }
            if ($newStatus === 'failed' && $oldStatus !== 'failed') {
                $this->handleDeliveryFailed($delivery);
            }
        }

        return response()->json($delivery->fresh()->load(['customer', 'driver', 'vehicle', 'order']));
    }

    public function destroy(Delivery $delivery)
    {
        // Revert order status if linked
        if ($delivery->order_id && $delivery->status !== 'cancelled') {
            $this->handleDeliveryCancelled($delivery);
        }
        $delivery->delete();
        return response()->json(['message' => 'Deleted']);
    }

    /**
     * Handle delivery completed — update order, bottle balance
     */
    private function handleDeliveryCompleted(Delivery $delivery)
    {
        // Update linked order status
        if ($delivery->order_id) {
            $order = Order::find($delivery->order_id);
            if ($order) {
                $order->update(['status' => 'completed']);
            }
        }

        // Update bottle balance (bottles sent out)
        if ($delivery->customer_id && $delivery->quantity > 0) {
            BottleBalance::updateForCustomer(
                $delivery->customer_id,
                $delivery->quantity,  // bottles out
                0                     // bottles returned (not yet)
            );
        }
    }

    /**
     * Handle delivery cancelled — revert order status
     */
    private function handleDeliveryCancelled(Delivery $delivery)
    {
        if ($delivery->order_id) {
            $order = Order::find($delivery->order_id);
            if ($order && $order->status === 'delivered') {
                $order->update(['status' => 'pending']);
            }
        }
    }

    /**
     * Handle delivery failed — revert order, note remarks
     */
    private function handleDeliveryFailed(Delivery $delivery)
    {
        if ($delivery->order_id) {
            $order = Order::find($delivery->order_id);
            if ($order && $order->status === 'delivered') {
                $order->update(['status' => 'pending']);
            }
        }
    }

    public function calendar(Request $request)
    {
        $from = Carbon::parse($request->get('from', now()->startOfMonth()));
        $to = Carbon::parse($request->get('to', now()->endOfMonth()));

        $deliveries = Delivery::with(['customer', 'driver'])
            ->whereBetween('delivery_date', [$from, $to])
            ->orderBy('delivery_date')
            ->get();

        return response()->json($deliveries);
    }

    public function routes(Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));

        $deliveries = Delivery::with(['customer', 'driver', 'vehicle'])
            ->whereDate('delivery_date', $date)
            ->whereNotIn('status', ['cancelled', 'failed'])
            ->orderBy('route')
            ->get()
            ->groupBy('route');

        return response()->json([
            'date' => $date,
            'routes' => $deliveries,
        ]);
    }

    public function history(Request $request)
    {
        $deliveries = Delivery::with(['customer', 'driver'])
            ->where('status', 'delivered')
            ->orderByDesc('delivery_date')
            ->paginate(20);

        return response()->json($deliveries);
    }

    public function dashboard()
    {
        $today = now()->format('Y-m-d');

        return response()->json([
            'today_total' => Delivery::whereDate('delivery_date', $today)->count(),
            'today_pending' => Delivery::whereDate('delivery_date', $today)->whereIn('status', ['scheduled', 'assigned'])->count(),
            'out_for_delivery' => Delivery::whereDate('delivery_date', $today)->where('status', 'out_for_delivery')->count(),
            'today_delivered' => Delivery::whereDate('delivery_date', $today)->where('status', 'delivered')->count(),
            'today_failed' => Delivery::whereDate('delivery_date', $today)->where('status', 'failed')->count(),
            'drivers_available' => Driver::active()->count(),
            'vehicles_available' => Vehicle::where('status', 'available')->count(),
        ]);
    }
}

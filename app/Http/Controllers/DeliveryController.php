<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\Order;
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
            $query->where('delivery_date', $request->date);
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

        $delivery = Delivery::create($data);
        return response()->json($delivery->load(['customer', 'driver', 'vehicle']), 201);
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

        $delivery->update($data);

        if (isset($data['status'])) {
            if ($data['status'] === 'out_for_delivery' && $delivery->vehicle_id) {
                Vehicle::where('id', $delivery->vehicle_id)->update(['status' => 'in_use']);
            }
            if (in_array($data['status'], ['delivered', 'failed', 'cancelled']) && $delivery->vehicle_id) {
                Vehicle::where('id', $delivery->vehicle_id)->update(['status' => 'available']);
            }
        }

        return response()->json($delivery->fresh()->load(['customer', 'driver', 'vehicle']));
    }

    public function destroy(Delivery $delivery)
    {
        $delivery->delete();
        return response()->json(['message' => 'Deleted']);
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
            ->where('delivery_date', $date)
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
            'today_total' => Delivery::where('delivery_date', $today)->count(),
            'today_pending' => Delivery::where('delivery_date', $today)->whereIn('status', ['scheduled', 'assigned'])->count(),
            'out_for_delivery' => Delivery::where('delivery_date', $today)->where('status', 'out_for_delivery')->count(),
            'today_delivered' => Delivery::where('delivery_date', $today)->where('status', 'delivered')->count(),
            'today_failed' => Delivery::where('delivery_date', $today)->where('status', 'failed')->count(),
            'drivers_available' => Driver::active()->count(),
            'vehicles_available' => Vehicle::where('status', 'available')->count(),
        ]);
    }
}

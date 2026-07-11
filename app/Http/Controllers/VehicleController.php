<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $vehicles = Vehicle::query()
            ->when($request->search, fn($q, $s) => $q->where('plate_number', 'like', "%{$s}%"))
            ->orderByDesc('id')
            ->paginate(20);

        return response()->json($vehicles);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'plate_number' => 'required|string|max:20|unique:vehicles',
            'description' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:0',
            'status' => 'in:available,in_use,maintenance',
        ]);

        $vehicle = Vehicle::create($data);
        return response()->json($vehicle, 201);
    }

    public function show(Vehicle $vehicle)
    {
        return response()->json($vehicle);
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $data = $request->validate([
            'plate_number' => 'sometimes|string|max:20|unique:vehicles,plate_number,' . $vehicle->id,
            'description' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:0',
            'status' => 'in:available,in_use,maintenance',
        ]);

        $vehicle->update($data);
        return response()->json($vehicle);
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return response()->json(['message' => 'Deleted']);
    }
}

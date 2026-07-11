<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        $drivers = Driver::query()
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->orderByDesc('id')
            ->paginate(20);

        return response()->json($drivers);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'license_number' => 'nullable|string|max:50',
            'status' => 'in:active,inactive,on_leave',
        ]);

        $driver = Driver::create($data);
        return response()->json($driver, 201);
    }

    public function show(Driver $driver)
    {
        return response()->json($driver);
    }

    public function update(Request $request, Driver $driver)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'license_number' => 'nullable|string|max:50',
            'status' => 'in:active,inactive,on_leave',
        ]);

        $driver->update($data);
        return response()->json($driver);
    }

    public function destroy(Driver $driver)
    {
        $driver->delete();
        return response()->json(['message' => 'Deleted']);
    }
}

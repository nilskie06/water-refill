<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Driver;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function register()
    {
        return view('auth.register');
    }

    public function dashboard()
    {
        return view('dashboard');
    }

    public function customers()
    {
        return view('customers.index');
    }

    public function customerShow(Customer $customer)
    {
        return view('customers.show', compact('customer'));
    }

    public function orders()
    {
        return view('orders.index');
    }

    public function orderCreate()
    {
        $customers = Customer::orderBy('name')->get();
        return view('orders.create', compact('customers'));
    }

    public function payments()
    {
        return view('payments.index');
    }

    public function paymentCreate()
    {
        $orders = Order::where('status', '!=', 'completed')->with('customer')->get();
        return view('payments.create', compact('orders'));
    }

    public function reports()
    {
        return view('reports.index');
    }

    public function bottles()
    {
        return view('bottles.index');
    }

    // Delivery module
    public function deliveries()
    {
        return view('deliveries.index');
    }

    public function deliveryCreate()
    {
        $customers = Customer::orderBy('name')->get();
        $drivers = Driver::active()->orderBy('name')->get();
        $vehicles = Vehicle::where('status', 'available')->orderBy('plate_number')->get();
        return view('deliveries.create', compact('customers', 'drivers', 'vehicles'));
    }

    public function deliveryCalendar()
    {
        return view('deliveries.calendar');
    }

    public function deliveryRoutes()
    {
        return view('deliveries.routes');
    }

    public function deliveryHistory()
    {
        return view('deliveries.history');
    }

    public function drivers()
    {
        return view('drivers.index');
    }

    public function vehicles()
    {
        return view('vehicles.index');
    }

    // Admin module
    public function roles()
    {
        return view('admin.roles');
    }

    public function permissions()
    {
        return view('admin.permissions');
    }

    public function users()
    {
        return view('admin.users');
    }

    public function menus()
    {
        return view('admin.menus');
    }
}

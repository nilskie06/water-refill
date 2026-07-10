<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
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
}

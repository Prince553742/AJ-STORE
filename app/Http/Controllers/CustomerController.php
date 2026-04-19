<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::withSum('creditSales as pending_total', 'total')
            ->orderBy('name')
            ->get();
        return view('customers', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);
        $customer = Customer::create($request->only('name', 'contact', 'address'));
        return response()->json($customer, 201);
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);
        $customer->update($request->only('name', 'contact', 'address'));
        return response()->json($customer);
    }

    public function destroy(Customer $customer)
    {
        if ($customer->creditSales()->where('status', 'pending')->exists()) {
            return response()->json(['error' => 'Customer has pending debts'], 400);
        }
        $customer->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
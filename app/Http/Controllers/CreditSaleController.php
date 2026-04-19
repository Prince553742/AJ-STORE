<?php

namespace App\Http\Controllers;

use App\Models\CreditSale;
use App\Models\Customer;
use Illuminate\Http\Request;

class CreditSaleController extends Controller
{
    public function index()
    {
        $debts = CreditSale::with('customer')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
        $customers = Customer::orderBy('name')->get();
        return view('debts', compact('debts', 'customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'item_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $total = $request->quantity * $request->price;

        $debt = CreditSale::create([
            'customer_id' => $request->customer_id,
            'item_name' => $request->item_name,
            'quantity' => $request->quantity,
            'price' => $request->price,
            'total' => $total,                 
            'due_date' => $request->due_date,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        return response()->json($debt, 201);
    }

    public function markAsPaid(CreditSale $creditSale)
    {
        if ($creditSale->status === 'paid') {
            return response()->json(['error' => 'Already paid'], 400);
        }
        $creditSale->markAsPaid();
        return response()->json(['message' => 'Marked as paid']);
    }
}
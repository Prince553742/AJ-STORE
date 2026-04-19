<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $pos = PurchaseOrder::orderBy('created_at', 'desc')->get();
        $items = Item::orderBy('name')->get();
        return view('purchase_orders', compact('pos', 'items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'purchase_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $poNumber = 'PO-' . date('Ymd') . '-' . str_pad(PurchaseOrder::count() + 1, 4, '0', STR_PAD_LEFT);
            $total = 0;
            foreach ($request->items as $itemData) {
                $total += $itemData['quantity'] * $itemData['unit_cost'];
            }
            $po = PurchaseOrder::create([
                'po_number' => $poNumber,
                'purchase_date' => $request->purchase_date,
                'status' => 'pending',
                'total_amount' => $total,
                'notes' => $request->notes,
            ]);
            foreach ($request->items as $itemData) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'item_id' => $itemData['item_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_cost' => $itemData['unit_cost'],
                    'total' => $itemData['quantity'] * $itemData['unit_cost'],
                ]);
            }
        });

        return response()->json(['message' => 'Purchase order created'], 201);
    }

    public function receive(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'pending') {
            return response()->json(['error' => 'Order already processed'], 400);
        }

        DB::transaction(function () use ($purchaseOrder) {
            foreach ($purchaseOrder->items as $poItem) {
                $item = $poItem->item;
                $item->stock += $poItem->quantity;
                $item->save();
            }
            $purchaseOrder->status = 'received';
            $purchaseOrder->save();
        });

        return response()->json(['message' => 'Order received and stock updated']);
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'pending') {
            return response()->json(['error' => 'Cannot delete processed order'], 400);
        }
        $purchaseOrder->delete();
        return response()->json(['message' => 'Deleted']);
    }
};
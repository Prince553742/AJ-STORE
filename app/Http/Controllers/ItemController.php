<?php

namespace App\Http\Controllers;
use App\Models\Item;
use Illuminate\Http\Request;
use App\Models\DailySale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::all();
        return view('price-list', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
        ]);

        $item = Item::create([
            'name' => $request->name,
            'category' => $request->category ?? 'General',
            'price' => $request->price,
            'stock' => 0,   // <-- default stock for new items
        ]);

        return response()->json($item, 201);
    }

    public function destroy($id)
    {
        $item = Item::findOrFail($id);
        $item->delete();
        return response()->json(['message' => 'Deleted'], 200);
    }

    public function updatePrice(Request $request, Item $item)
    {
        $request->validate(['price' => 'required|numeric|min:0']);
        $item->update(['price' => $request->price]);
        return response()->json($item);
    }

    public function updateName(Request $request, Item $item)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $item->update(['name' => $request->name]);
        return response()->json($item);
    }

    public function updateCategory(Request $request, Item $item)
    {
        $request->validate(['category' => 'required|string|max:100']);
        $item->update(['category' => $request->category]);
        return response()->json($item);
    }

    public function getCategories()
    {
        $categories = Item::select('category')->distinct()->pluck('category');
        return response()->json($categories);
    }

    public function recordDailySale(Request $request)
    {
        $today = now()->toDateString();
        $itemsData = $request->items; // array of {id, name, price, quantity}

        // Prevent duplicate
        if (DailySale::where('sale_date', $today)->exists()) {
            return response()->json(['error' => 'Today\'s sales already recorded'], 400);
        }

        DB::transaction(function () use ($today, $itemsData) {
            $total = collect($itemsData)->sum(fn($i) => $i['price'] * $i['quantity']);
            $sale = DailySale::create([
                'sale_date' => $today,
                'total_amount' => $total,
            ]);

            foreach ($itemsData as $item) {
                if ($item['quantity'] > 0) {
                    SaleItem::create([
                        'daily_sale_id' => $sale->id,
                        'item_id' => $item['id'],
                        'item_name' => $item['name'],
                        'price' => $item['price'],
                        'quantity' => $item['quantity'],
                        'total' => $item['price'] * $item['quantity'],
                    ]);
                }
            }
        });

        return response()->json(['message' => 'Daily sales recorded successfully']);
    }

    public function salesHistory()
    {
        $sales = DailySale::with('items')->orderBy('sale_date', 'desc')->get();
        return view('sales-history', compact('sales'));
    }


    // Show stock management page
    public function stocksIndex()
    {
        $items = Item::orderBy('name')->get();
        return view('stocks', compact('items'));
    }

    // Update stock for an item
    public function updateStock(Request $request, Item $item)
    {
        $request->validate([
            'stock' => 'required|integer|min:0',
        ]);
        $item->update(['stock' => $request->stock]);
        return response()->json(['success' => true, 'stock' => $item->stock]);
    }
}

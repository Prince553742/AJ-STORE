<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, user-scalable=yes">
    <title>AJ's Store · Sales History</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4f46e5">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .sidebar-link.active { background-color: #eef2ff; color: #4f46e5; }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased">

<div class="flex h-screen overflow-hidden">
    
    <!-- LEFT SIDEBAR (unified) -->
    <aside class="w-72 bg-white shadow-xl border-r border-gray-200 flex flex-col">
        <div class="p-6 border-b border-gray-100">
            <h1 class="text-3xl font-extrabold text-indigo-700 flex items-center gap-2">
                <i class="fas fa-store text-indigo-500"></i> AJ's Store
            </h1>
            <p class="text-gray-500 text-sm mt-1">Daily Liquidation</p>
        </div>
        <nav class="flex-1 p-4">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-lg font-medium transition text-gray-600 hover:bg-gray-50">DASHBOARD</a>
            <a href="{{ route('items.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-lg font-medium transition mt-2 text-gray-600 hover:bg-gray-50">PRICE LIST</a>
            <a href="{{ route('stocks.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-lg font-medium transition mt-2 text-gray-600 hover:bg-gray-50">STOCKS</a>
            <a href="{{ route('debts.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-lg font-medium transition mt-2 text-gray-600 hover:bg-gray-50">DEBTS</a>
            <a href="{{ route('customers.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-lg font-medium transition mt-2 text-gray-600 hover:bg-gray-50">CUSTOMERS</a>
            <a href="{{ route('expenses.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-lg font-medium transition mt-2 text-gray-600 hover:bg-gray-50">EXPENSES</a>
            <a href="{{ route('purchase-orders.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-lg font-medium transition mt-2 text-gray-600 hover:bg-gray-50">PURCHASE ORDERS</a>
            <a href="{{ route('sales.history') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-lg font-medium transition mt-2 bg-indigo-50 text-indigo-700">SALES HISTORY</a>
            <div class="mt-8 text-xs text-gray-400 px-4 pt-8 border-t border-gray-100">
                <i class="fas fa-tablet-alt"></i> POCO Pad M1 · Tablet mode
            </div>
        </nav>
    </aside>

    <!-- MAIN CONTENT (unchanged) -->
    <main class="flex-1 overflow-y-auto p-6 md:p-8">
        <div class="max-w-6xl mx-auto">
            <h1 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                <i class="fas fa-history text-indigo-600"></i> Sales History
            </h1>

            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Date</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Total Sales (₱)</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Items Sold</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sales as $sale)
                            <tr class="border-t hover:bg-gray-50 transition">
                                <td class="px-6 py-4">{{ $sale->sale_date->format('F j, Y') }}</td>
                                <td class="px-6 py-4 font-bold text-green-600">₱ {{ number_format($sale->total_amount, 2) }}</td>
                                <td class="px-6 py-4">{{ $sale->items->sum('quantity') }} items</td>
                                <td class="px-6 py-4">
                                    <button onclick="toggleDetails({{ $sale->id }})" class="text-indigo-600 hover:text-indigo-800 font-medium">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <div id="details-{{ $sale->id }}" class="hidden mt-2 bg-gray-50 p-3 rounded-lg text-sm">
                                        @foreach($sale->items as $item)
                                            <div class="py-1 border-b last:border-0">
                                                {{ $item->item_name }} × {{ $item->quantity }} = ₱ {{ number_format($item->total, 2) }}
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-12 text-gray-400">
                                    <i class="fas fa-receipt text-4xl mb-2 block"></i>
                                    No sales recorded yet.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    function toggleDetails(id) {
        const el = document.getElementById(`details-${id}`);
        if (el) el.classList.toggle('hidden');
    }
</script>
<script>
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/sw.js')
      .then(reg => console.log('Service Worker registered', reg))
      .catch(err => console.log('Service Worker registration failed', err));
  });
}
</script>
</body>
</html>
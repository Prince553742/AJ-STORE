<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, user-scalable=yes">
    <title>AJ's Store · Stock Manager</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4f46e5">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .stock-btn { min-width: 48px; min-height: 48px; font-size: 1.2rem; border-radius: 12px; }
        .stock-btn:active { transform: scale(0.94); }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased">

<div x-data="stockManager()" x-init="initItems(@js($items))" class="flex h-screen overflow-hidden">
    
    <!-- LEFT SIDEBAR (unified) -->
    <aside class="w-72 bg-white shadow-xl border-r border-gray-200 flex flex-col">
        <div class="p-6 border-b border-gray-100">
            <h1 class="text-3xl font-extrabold text-indigo-700 flex items-center gap-2">
                <i class="fas fa-store text-indigo-500"></i> AJ's Store
            </h1>
            <p class="text-gray-500 text-sm mt-1">Stock Manager</p>
        </div>
        <nav class="flex-1 p-4">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-lg font-medium transition text-gray-600 hover:bg-gray-50">DASHBOARD</a>
            <a href="{{ route('items.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-lg font-medium transition mt-2 text-gray-600 hover:bg-gray-50">PRICE LIST</a>
            <a href="{{ route('stocks.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-lg font-medium transition mt-2 bg-indigo-50 text-indigo-700">STOCKS</a>
            <a href="{{ route('debts.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-lg font-medium transition mt-2 text-gray-600 hover:bg-gray-50">DEBTS</a>
            <a href="{{ route('customers.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-lg font-medium transition mt-2 text-gray-600 hover:bg-gray-50">CUSTOMERS</a>
            <a href="{{ route('expenses.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-lg font-medium transition mt-2 text-gray-600 hover:bg-gray-50">EXPENSES</a>
            <a href="{{ route('purchase-orders.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-lg font-medium transition mt-2 text-gray-600 hover:bg-gray-50">PURCHASE ORDERS</a>
            <a href="{{ route('sales.history') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-lg font-medium transition mt-2 text-gray-600 hover:bg-gray-50">SALES HISTORY</a>
            <div class="mt-8 text-xs text-gray-400 px-4 pt-8 border-t border-gray-100">
                <i class="fas fa-tablet-alt"></i> POCO Pad M1 · Tablet mode
            </div>
        </nav>
    </aside>

    <!-- MAIN CONTENT (unchanged) -->
    <main class="flex-1 overflow-y-auto p-6 md:p-8">
        <div class="max-w-5xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-boxes text-indigo-600"></i> Inventory Stock
                </h1>
                <div class="flex gap-3">
                    <select x-model="selectedCategory" class="border rounded-xl px-4 py-2 bg-white">
                        <option value="">All Categories</option>
                        <option value="General">General</option>
                        <option value="Beverages">Beverages</option>
                        <option value="Snacks">Snacks</option>
                        <option value="Canned Goods">Canned Goods</option>
                        <option value="Personal Care">Personal Care</option>
                    </select>
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" x-model="searchQuery" placeholder="Search item..." 
                               class="pl-10 pr-4 py-2 border rounded-xl w-64">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Item</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Category</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-600">Current Stock</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-600">Adjust Stock</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-600">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="item in filteredItems" :key="item.id">
                                <tr class="border-t hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium" x-text="item.name"></td>
                                    <td class="px-6 py-4" x-text="item.category"></td>
                                    <td class="px-6 py-4 text-center">
                                        <span x-text="item.stock" :class="{'text-red-600 font-bold': item.stock < 5}"></span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button @click="adjustStock(item, -1)" class="stock-btn bg-red-100 text-red-700 w-10 h-10 rounded-lg flex items-center justify-center">-</button>
                                            <input type="number" x-model="item.stock" @change="updateStock(item)" class="w-20 text-center border rounded-lg py-2">
                                            <button @click="adjustStock(item, 1)" class="stock-btn bg-green-100 text-green-700 w-10 h-10 rounded-lg flex items-center justify-center">+</button>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span x-show="item.stock <= 0" class="text-red-600 font-semibold">Out of Stock</span>
                                        <span x-show="item.stock > 0 && item.stock < 5" class="text-yellow-600 font-semibold">Low Stock</span>
                                        <span x-show="item.stock >= 5" class="text-green-600">In Stock</span>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="filteredItems.length === 0">
                                <td colspan="5" class="text-center py-12 text-gray-400">No items found. Add some products first.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
function stockManager() {
    return {
        items: [],
        searchQuery: '',
        selectedCategory: '',
        initItems(serverItems) {
            this.items = serverItems.map(item => ({
                id: item.id,
                name: item.name,
                category: item.category || 'General',
                stock: item.stock ?? 0
            }));
        },
        get filteredItems() {
            let filtered = this.items;
            if (this.searchQuery.trim()) {
                const q = this.searchQuery.toLowerCase();
                filtered = filtered.filter(item => item.name.toLowerCase().includes(q));
            }
            if (this.selectedCategory) {
                filtered = filtered.filter(item => item.category === this.selectedCategory);
            }
            return filtered;
        },
        adjustStock(item, delta) {
            let newStock = (item.stock || 0) + delta;
            if (newStock < 0) newStock = 0;
            item.stock = newStock;
            this.updateStock(item);
        },
        async updateStock(item) {
            try {
                const res = await fetch(`/items/${item.id}/stock`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ stock: item.stock })
                });
                if (!res.ok) {
                    const err = await res.json();
                    alert('Failed to update stock: ' + (err.message || 'Unknown error'));
                }
            } catch(e) {
                console.error(e);
                alert('Network error while updating stock');
            }
        }
    }
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
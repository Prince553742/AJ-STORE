<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, user-scalable=yes">
    <title>AJ's Store · Price List</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4f46e5">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .qty-btn, .delete-btn, .edit-btn { min-width: 48px; min-height: 48px; font-size: 1.2rem; border-radius: 12px; transition: all 0.1s ease; }
        .qty-btn:active, .delete-btn:active, .edit-btn:active { transform: scale(0.94); }
        .add-item-btn, .action-btn { min-height: 48px; font-size: 1rem; }
        table th, table td { padding: 0.75rem 0.5rem; }
        .editable { cursor: pointer; border-bottom: 1px dashed #ccc; }
        .editable:hover { background-color: #f0f0f0; }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased">

<div x-data="priceListApp()" x-init="initItems(@js($items)); loadCategories()" class="flex h-screen overflow-hidden">
    
    <!-- LEFT SIDEBAR (unified with dashboard) -->
    <aside class="w-72 bg-white shadow-xl border-r border-gray-200 flex flex-col">
        <div class="p-6 border-b border-gray-100">
            <h1 class="text-3xl font-extrabold text-indigo-700 flex items-center gap-2">
                <i class="fas fa-store text-indigo-500"></i> AJ's Store
            </h1>
            <p class="text-gray-500 text-sm mt-1">Daily Liquidation</p>
        </div>
        <nav class="flex-1 p-4">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-lg font-medium transition text-gray-600 hover:bg-gray-50">DASHBOARD</a>
            <a href="{{ route('items.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-lg font-medium transition mt-2 bg-indigo-50 text-indigo-700">PRICE LIST</a>
            <a href="{{ route('stocks.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-lg font-medium transition mt-2 text-gray-600 hover:bg-gray-50">STOCKS</a>
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

    <!-- MAIN CONTENT -->
    <main class="flex-1 overflow-y-auto p-4 md:p-6">
        <!-- SEARCH + CATEGORY FILTER + ACTION BUTTONS (Clear Cart removed) -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-6">
            <div class="relative col-span-2">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" x-model="searchQuery" placeholder="Search item..." 
                       class="w-full pl-10 pr-4 py-3 border rounded-xl focus:ring-2 focus:ring-indigo-400">
            </div>
            <select x-model="selectedCategory" @change="filterByCategory = selectedCategory" class="border rounded-xl px-4 py-3 bg-white">
                <option value="">All Categories</option>
                <template x-for="cat in categories" :key="cat">
                    <option x-text="cat" :value="cat"></option>
                </template>
            </select>
            <!-- Only "Record Day" button remains -->
            <div class="flex gap-2">
                <button @click="recordDailySale" class="action-btn bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-xl flex-1 flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> Record Day
                </button>
            </div>
        </div>

        <!-- ADD ITEM CARD -->
        <div class="bg-white rounded-2xl shadow-md p-4 mb-6">
            <h2 class="text-lg font-semibold flex items-center gap-2 mb-3"><i class="fas fa-plus-circle text-emerald-500"></i> Add New Item</h2>
            <div class="flex flex-col sm:flex-row gap-3">
                <input type="text" x-model="newItemName" placeholder="Item name" class="flex-1 px-4 py-3 border rounded-xl">
                <select x-model="newItemCategory" class="w-full sm:w-40 px-4 py-3 border rounded-xl bg-white">
                    <option value="General">General</option>
                    <option value="Beverages">Beverages</option>
                    <option value="Snacks">Snacks</option>
                    <option value="Canned Goods">Canned Goods</option>
                    <option value="Personal Care">Personal Care</option>
                </select>
                <input type="number" step="0.01" x-model="newItemPrice" placeholder="Price (₱)" class="w-full sm:w-32 px-4 py-3 border rounded-xl">
                <button @click="addItem" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl">Add</button>
            </div>
        </div>

        <!-- PRICE LIST TABLE -->
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-3 text-left">Qty</th>
                            <th class="px-3 py-3 text-left">Item</th>
                            <th class="px-3 py-3 text-left">Category</th>
                            <th class="px-3 py-3 text-left">Price (₱)</th>
                            <th class="px-3 py-3 text-left">Total (₱)</th>
                            <th class="px-3 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="item in filteredItems" :key="item.id">
                            <tr :class="{'bg-blue-50': item.quantity > 0}">
                                <td class="px-3 py-2 text-xl font-bold text-indigo-700" x-text="item.quantity"></td>
                                <td class="px-3 py-2">
                                    <span @click="editItemName(item)" class="editable inline-block" x-text="item.name"></span>
                                </td>
                                <td class="px-3 py-2">
                                    <select x-model="item.category" @change="updateCategory(item)" class="border rounded px-2 py-1 text-sm">
                                        <option value="General">General</option>
                                        <option value="Beverages">Beverages</option>
                                        <option value="Snacks">Snacks</option>
                                        <option value="Canned Goods">Canned Goods</option>
                                        <option value="Personal Care">Personal Care</option>
                                    </select>
                                </td>
                                <td class="px-3 py-2">
                                    <span @click="editItemPrice(item)" class="editable" x-text="`₱ ${item.price.toFixed(2)}`"></span>
                                </td>
                                <td class="px-3 py-2 font-semibold text-emerald-700" x-text="`₱ ${(item.price * item.quantity).toFixed(2)}`"></td>
                                <td class="px-3 py-2 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <div class="flex gap-1">
                                            <button @click="item.quantity--" :disabled="item.quantity <= 0" class="qty-btn bg-red-100 text-red-700 w-10 h-10 rounded-lg">-</button>
                                            <button @click="item.quantity++" class="qty-btn bg-green-100 text-green-700 w-10 h-10 rounded-lg">+</button>
                                        </div>
                                        <button @click="deleteItem(item)" class="delete-btn bg-red-100 text-red-600 w-10 h-10 rounded-lg ml-2"><i class="fas fa-trash-alt"></i></button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="filteredItems.length === 0">
                            <td colspan="6" class="text-center py-8 text-gray-400">No items found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- GRAND TOTAL CARD -->
        <div class="mt-6 bg-gradient-to-r from-indigo-600 to-indigo-800 rounded-2xl p-5 text-white">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold">TOTAL SALES TODAY</h2>
                <div class="text-3xl font-black">₱ <span x-text="grandTotal().toFixed(2)">0.00</span></div>
            </div>
        </div>
    </main>
</div>

<script>
function priceListApp() {
    return {
        activeTab: 'priceList',
        items: [],
        newItemName: '',
        newItemCategory: 'General',
        newItemPrice: '',
        searchQuery: '',
        selectedCategory: '',
        filterByCategory: '',
        categories: ['General'],

        init() {
            this.$watch('items', () => {
                const qtyMap = {};
                this.items.forEach(item => { qtyMap[item.id] = item.quantity; });
                localStorage.setItem('ajstore_cart', JSON.stringify(qtyMap));
            }, { deep: true });
        },

        async loadCategories() {
            try {
                const res = await fetch('{{ route("categories.list") }}');
                if (res.ok) {
                    const cats = await res.json();
                    this.categories = cats;
                }
            } catch(e) { console.log(e); }
        },

        initItems(serverItems) {
            let loadedItems = serverItems.map(item => ({
                id: item.id,
                name: item.name,
                category: item.category || 'General',
                price: typeof item.price === 'number' ? item.price : parseFloat(item.price),
                quantity: 0
            }));
            const saved = localStorage.getItem('ajstore_cart');
            if (saved) {
                const savedQuantities = JSON.parse(saved);
                loadedItems = loadedItems.map(item => ({
                    ...item,
                    quantity: savedQuantities[item.id] || 0
                }));
            }
            this.items = loadedItems;
        },

        get filteredItems() {
            let filtered = this.items;
            if (this.searchQuery.trim()) {
                const q = this.searchQuery.toLowerCase();
                filtered = filtered.filter(item => item.name.toLowerCase().includes(q));
            }
            if (this.filterByCategory) {
                filtered = filtered.filter(item => item.category === this.filterByCategory);
            }
            return filtered;
        },

        async addItem() {
            if (!this.newItemName.trim() || !this.newItemPrice) return;
            const price = parseFloat(this.newItemPrice);
            if (isNaN(price) || price < 0) return;
            const formData = new FormData();
            formData.append('name', this.newItemName);
            formData.append('category', this.newItemCategory);
            formData.append('price', price);
            try {
                const res = await fetch('{{ route("items.store") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: formData
                });
                if (res.ok) {
                    const newItem = await res.json();
                    this.items.push({
                        id: newItem.id,
                        name: newItem.name,
                        category: newItem.category,
                        price: parseFloat(newItem.price),
                        quantity: 0
                    });
                    this.newItemName = '';
                    this.newItemCategory = 'General';
                    this.newItemPrice = '';
                    await this.loadCategories();
                } else alert('Error adding item');
            } catch(err) { alert('Network error'); }
        },

        async editItemName(item) {
            const newName = prompt('Edit item name:', item.name);
            if (!newName || newName === item.name) return;
            try {
                const res = await fetch(`/items/${item.id}/name`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ name: newName })
                });
                if (res.ok) item.name = newName;
                else alert('Failed to update name');
            } catch(e) { alert('Network error'); }
        },

        async editItemPrice(item) {
            let newPrice = prompt('Edit price (₱):', item.price);
            if (!newPrice) return;
            newPrice = parseFloat(newPrice);
            if (isNaN(newPrice) || newPrice < 0) return;
            try {
                const res = await fetch(`/items/${item.id}/price`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ price: newPrice })
                });
                if (res.ok) item.price = newPrice;
                else alert('Failed to update price');
            } catch(e) { alert('Network error'); }
        },

        async updateCategory(item) {
            try {
                const res = await fetch(`/items/${item.id}/category`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ category: item.category })
                });
                if (!res.ok) alert('Failed to update category');
                else await this.loadCategories();
            } catch(e) { alert('Network error'); }
        },

        async recordDailySale() {
            const itemsWithQty = this.items.filter(i => i.quantity > 0);
            if (itemsWithQty.length === 0) {
                alert('No items with quantity. Add some quantities first.');
                return;
            }
            if (!confirm('Record today\'s sales? This will save all quantities and then reset them for the next day.')) return;
            const payload = itemsWithQty.map(i => ({
                id: i.id, name: i.name, price: i.price, quantity: i.quantity
            }));
            try {
                const res = await fetch('{{ route("record.sale") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ items: payload })
                });
                if (res.ok) {
                    alert('Daily sales recorded successfully!');
                    this.items.forEach(item => item.quantity = 0);
                } else {
                    const err = await res.json();
                    alert(err.error || 'Failed to record sales');
                }
            } catch(e) { alert('Network error'); }
        },

        deleteItem(item) {
            if (!confirm(`Delete "${item.name}"?`)) return;
            fetch(`/items/${item.id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(res => {
                if (res.ok) {
                    this.items = this.items.filter(i => i.id !== item.id);
                    // Also remove this item's quantity from localStorage
                    let cart = JSON.parse(localStorage.getItem('ajstore_cart') || '{}');
                    delete cart[item.id];
                    localStorage.setItem('ajstore_cart', JSON.stringify(cart));
                } else {
                    alert('Delete failed');
                }
            }).catch(() => alert('Network error'));
        },

        grandTotal() {
            return this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
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
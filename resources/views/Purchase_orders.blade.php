<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AJ's Store · Buying Stocks</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4f46e5">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100">

<div x-data="poManager()" x-init="initData(@js($pos), @js($items))" class="flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <aside class="w-72 bg-white shadow-xl border-r flex flex-col">
        <div class="p-6 border-b"><h1 class="text-3xl font-bold text-indigo-700">AJ's Store</h1><p class="text-gray-500">Buying Stocks</p></div>
        <nav class="flex-1 p-4">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50">DASHBOARD</a>
            <a href="{{ route('items.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 mt-2">PRICE LIST</a>
            <a href="{{ route('stocks.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 mt-2">STOCKS</a>
            <a href="{{ route('debts.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 mt-2">DEBTS</a>
            <a href="{{ route('customers.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 mt-2">CUSTOMERS</a>
            <a href="{{ route('expenses.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 mt-2">EXPENSES</a>
            <a href="{{ route('purchase-orders.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-indigo-50 text-indigo-700 mt-2">BUYING STOCKS</a>
            <a href="{{ route('sales.history') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 mt-2">SALES HISTORY</a>
            <div class="mt-8 text-xs text-gray-400 px-4 pt-8 border-t">POCO Pad M1 · Tablet mode</div>
        </nav>
    </aside>

    <main class="flex-1 overflow-y-auto p-6">
        <div class="max-w-6xl mx-auto">
            <div class="flex justify-between mb-4">
                <h1 class="text-2xl font-bold">Buying Stocks</h1>
                <button @click="openModal()" class="bg-green-600 text-white px-4 py-2 rounded-xl">+ Create PO</button>
            </div>
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3">PO #</th>
                            <th class="px-6 py-3">Purchase Date</th>
                            <th class="px-6 py-3">Total (₱)</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="po in pos" :key="po.id">
                            <tr class="border-t">
                                <td class="px-6 py-3" x-text="po.po_number"></td>
                                <td class="px-6 py-3" x-text="po.purchase_date ? new Date(po.purchase_date).toLocaleDateString() : '—'"></td>
                                <td class="px-6 py-3" x-text="'₱ ' + po.total_amount.toFixed(2)"></td>
                                <td class="px-6 py-3">
                                    <span x-show="po.status === 'pending'" class="text-yellow-600 font-semibold">Pending</span>
                                    <span x-show="po.status === 'received'" class="text-green-600 font-semibold">Received</span>
                                    <span x-show="po.status === 'cancelled'" class="text-red-600">Cancelled</span>
                                </td>
                                <td class="px-6 py-3">
                                    <button x-show="po.status === 'pending'" @click="receivePO(po)" class="bg-blue-100 text-blue-700 px-2 py-1 rounded">Receive</button>
                                    <button x-show="po.status === 'pending'" @click="deletePO(po)" class="text-red-600 ml-2">Delete</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create PO Modal (only purchase date) -->
        <div x-show="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl p-6 max-h-screen overflow-y-auto">
                <h2 class="text-xl font-bold mb-4">Create Purchase Order</h2>
                <div class="space-y-3">
                    <div><label>Purchase Date</label><input type="date" x-model="newPO.purchase_date" class="w-full border rounded-xl px-3 py-2"></div>
                    <div><label>Notes</label><textarea x-model="newPO.notes" class="w-full border rounded-xl px-3 py-2" rows="2"></textarea></div>
                    <div><label class="font-bold">Items</label>
                        <div class="space-y-2">
                            <template x-for="(item, idx) in newPO.items" :key="idx">
                                <div class="flex gap-2 items-center">
                                    <select x-model="item.item_id" class="flex-1 border rounded px-2 py-1">
                                        <option value="">Select Item</option>
                                        <template x-for="i in availableItems" :key="i.id">
                                            <option :value="i.id" x-text="i.name"></option>
                                        </template>
                                    </select>
                                    <input type="number" x-model="item.quantity" placeholder="Qty" class="w-24 border rounded px-2 py-1">
                                    <input type="number" step="0.01" x-model="item.unit_cost" placeholder="Cost" class="w-32 border rounded px-2 py-1">
                                    <button @click="removeItem(idx)" class="text-red-600"><i class="fas fa-trash"></i></button>
                                </div>
                            </template>
                            <button @click="addItem" class="text-indigo-600 text-sm"><i class="fas fa-plus"></i> Add Item</button>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button @click="closeModal" class="px-4 py-2 border rounded-xl">Cancel</button>
                    <button @click="savePO" class="px-4 py-2 bg-indigo-600 text-white rounded-xl">Create PO</button>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
function poManager() {
    return {
        pos: [],
        availableItems: [],
        showModal: false,
        newPO: {
            purchase_date: new Date().toISOString().slice(0,10),
            notes: '',
            items: [{ item_id: '', quantity: 1, unit_cost: 0 }]
        },
        initData(pos, items) {
            this.pos = pos;
            this.availableItems = items;
        },
        openModal() { this.showModal = true; },
        closeModal() { this.showModal = false; this.newPO = { purchase_date: new Date().toISOString().slice(0,10), notes: '', items: [{ item_id: '', quantity: 1, unit_cost: 0 }] }; },
        addItem() { this.newPO.items.push({ item_id: '', quantity: 1, unit_cost: 0 }); },
        removeItem(idx) { this.newPO.items.splice(idx, 1); },
        async savePO() {
            if (this.newPO.items.length === 0 || !this.newPO.items[0].item_id) { alert('Add at least one item'); return; }
            let res = await fetch('/purchase-orders', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify(this.newPO)
            });
            if (res.ok) {
                let newPo = await res.json();
                this.pos.unshift(newPo);
                this.closeModal();
            } else alert('Error creating PO');
        },
        async receivePO(po) {
            if (!confirm(`Mark PO ${po.po_number} as received? Stock will be updated.`)) return;
            let res = await fetch(`/purchase-orders/${po.id}/receive`, { method: 'PATCH', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
            if (res.ok) {
                po.status = 'received';
                alert('Order received and stock updated');
            } else alert('Failed');
        },
        async deletePO(po) {
            if (!confirm(`Delete PO ${po.po_number}?`)) return;
            let res = await fetch(`/purchase-orders/${po.id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
            if (res.ok) this.pos = this.pos.filter(p => p.id !== po.id);
            else alert('Delete failed');
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
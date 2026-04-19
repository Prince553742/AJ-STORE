<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>AJ's Store · Debts</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4f46e5">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100">

<div x-data="debtManager()" x-init="initData(@js($debts), @js($customers))" class="flex h-screen overflow-hidden">
    
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
            <a href="{{ route('debts.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-lg font-medium transition mt-2 bg-indigo-50 text-indigo-700">DEBTS</a>
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
    <main class="flex-1 overflow-y-auto p-6">
        <div class="max-w-5xl mx-auto">
            <div class="flex justify-between mb-4">
                <h1 class="text-2xl font-bold">Outstanding Debts</h1>
                <button @click="openModal()" class="bg-green-600 text-white px-4 py-2 rounded-xl shadow">+ Record Debt</button>
            </div>

            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Customer</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Item</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold text-gray-600">Qty</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold text-gray-600">Total (₱)</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold text-gray-600">Due Date</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold text-gray-600">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="debt in debts" :key="debt.id">
                            <tr class="border-t hover:bg-gray-50">
                                <td class="px-6 py-3" x-text="debt.customer.name"></td>
                                <td class="px-6 py-3" x-text="debt.item_name"></td>
                                <td class="px-6 py-3 text-center" x-text="debt.quantity"></td>
                                <td class="px-6 py-3 text-center font-semibold" x-text="'₱ ' + (parseFloat(debt.total) || 0).toFixed(2)"></td>
                                <td class="px-6 py-3 text-center" x-text="debt.due_date ? new Date(debt.due_date).toLocaleDateString() : '—'"></td>
                                <td class="px-6 py-3 text-center">
                                    <button @click="markPaid(debt)" class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm hover:bg-green-200">
                                        <i class="fas fa-check"></i> Mark Paid
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="debts.length === 0">
                            <td colspan="6" class="text-center py-8 text-gray-400">No outstanding debts. Great! 🙌</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL: Record Debt -->
        <div x-show="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
                <h2 class="text-xl font-bold mb-4">Record New Debt</h2>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Customer *</label>
                        <select x-model="form.customer_id" class="w-full border rounded-xl px-3 py-2">
                            <option value="">-- Select Customer --</option>
                            <template x-for="c in customers" :key="c.id">
                                <option :value="c.id" x-text="c.name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Item name *</label>
                        <input type="text" x-model="form.item_name" placeholder="e.g., 10 mighty green" class="w-full border rounded-xl px-3 py-2">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                            <input type="number" x-model="form.quantity" min="1" class="w-full border rounded-xl px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Price per unit (₱) *</label>
                            <input type="number" step="0.01" x-model="form.price" min="0" class="w-full border rounded-xl px-3 py-2">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Due date (optional)</label>
                        <input type="date" x-model="form.due_date" class="w-full border rounded-xl px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes (optional)</label>
                        <textarea x-model="form.notes" rows="2" class="w-full border rounded-xl px-3 py-2" placeholder="Any extra info..."></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button @click="closeModal()" class="px-4 py-2 border rounded-xl hover:bg-gray-50">Cancel</button>
                    <button @click="saveDebt()" class="px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700">Save Debt</button>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
function debtManager() {
    return {
        debts: [],
        customers: [],
        showModal: false,
        form: { customer_id: '', item_name: '', quantity: 1, price: 0, due_date: '', notes: '' },
        initData(debts, customers) { this.debts = debts; this.customers = customers; },
        openModal() { this.form = { customer_id: '', item_name: '', quantity: 1, price: 0, due_date: '', notes: '' }; this.showModal = true; },
        closeModal() { this.showModal = false; },
        async saveDebt() {
            if (!this.form.customer_id) { alert('Please select a customer'); return; }
            if (!this.form.item_name.trim()) { alert('Please enter item name'); return; }
            if (this.form.quantity < 1) { alert('Quantity must be at least 1'); return; }
            if (this.form.price <= 0) { alert('Price must be greater than 0'); return; }
            try {
                const res = await fetch('/debts', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify(this.form)
                });
                if (res.ok) {
                    const newDebt = await res.json();
                    const customer = this.customers.find(c => c.id === newDebt.customer_id);
                    newDebt.customer = customer;
                    this.debts.unshift(newDebt);
                    this.closeModal();
                } else {
                    const err = await res.json();
                    alert('Error: ' + (err.message || 'Could not save debt'));
                }
            } catch(e) { alert('Network error. Check if server is running.'); }
        },
        async markPaid(debt) {
            if (!confirm(`Mark as paid for ${debt.customer.name}?`)) return;
            try {
                const res = await fetch(`/debts/${debt.id}/pay`, { method: 'PATCH', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
                if (res.ok) this.debts = this.debts.filter(d => d.id !== debt.id);
                else alert('Failed to mark as paid');
            } catch(e) { alert('Network error'); }
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
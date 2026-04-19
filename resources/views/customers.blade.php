<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AJ's Store · Customers</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4f46e5">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100">

<div x-data="customerManager()" x-init="initCustomers(@js($customers))" class="flex h-screen overflow-hidden">
    
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
            <a href="{{ route('customers.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-lg font-medium transition mt-2 bg-indigo-50 text-indigo-700">CUSTOMERS</a>
            <a href="{{ route('expenses.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-lg font-medium transition mt-2 text-gray-600 hover:bg-gray-50">EXPENSES</a>
            <a href="{{ route('purchase-orders.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-lg font-medium transition mt-2 text-gray-600 hover:bg-gray-50">PURCHASE ORDERS</a>
            <a href="{{ route('sales.history') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-lg font-medium transition mt-2 text-gray-600 hover:bg-gray-50">SALES HISTORY</a>
            <div class="mt-8 text-xs text-gray-400 px-4 pt-8 border-t border-gray-100">
                <i class="fas fa-tablet-alt"></i> POCO Pad M1 · Tablet mode
            </div>
        </nav>
    </aside>

    <main class="flex-1 overflow-y-auto p-6">
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between mb-4">
                <h1 class="text-2xl font-bold">Customers</h1>
                <button @click="showAddModal = true" class="bg-indigo-600 text-white px-4 py-2 rounded-xl">+ Add Customer</button>
            </div>
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Name</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Contact</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold text-gray-600">Pending Debt (₱)</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="c in customers" :key="c.id">
                            <tr class="border-t">
                                <td class="px-6 py-3" x-text="c.name"></td>
                                <td class="px-6 py-3" x-text="c.contact"></td>
                                <td class="px-6 py-3 text-center font-semibold" x-text="'₱ ' + (parseFloat(c.pending_total) || 0).toFixed(2)"></td>
                                <td class="px-6 py-3 text-center">
                                    <button @click="editCustomer(c)" class="text-blue-600">Edit</button>
                                    <button @click="deleteCustomer(c)" class="text-red-600 ml-2">Delete</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <div x-show="showAddModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
            <div class="bg-white p-6 rounded-xl w-96">
                <h2 class="text-xl font-bold mb-4" x-text="editingId ? 'Edit Customer' : 'Add Customer'"></h2>
                <input type="text" x-model="form.name" placeholder="Name" class="w-full border rounded p-2 mb-2">
                <input type="text" x-model="form.contact" placeholder="Contact" class="w-full border rounded p-2 mb-2">
                <input type="text" x-model="form.address" placeholder="Address" class="w-full border rounded p-2 mb-4">
                <div class="flex justify-end gap-2">
                    <button @click="closeModal" class="px-4 py-2 border rounded">Cancel</button>
                    <button @click="saveCustomer" class="px-4 py-2 bg-indigo-600 text-white rounded">Save</button>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
function customerManager() {
    return {
        customers: [],
        showAddModal: false,
        editingId: null,
        form: { name: '', contact: '', address: '' },
        initCustomers(data) { this.customers = data; },
        editCustomer(c) { this.editingId = c.id; this.form = {...c}; this.showAddModal = true; },
        closeModal() { this.showAddModal = false; this.editingId = null; this.form = { name: '', contact: '', address: '' }; },
        async saveCustomer() {
            let url = this.editingId ? `/customers/${this.editingId}` : '/customers';
            let method = this.editingId ? 'PUT' : 'POST';
            let res = await fetch(url, {
                method,
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify(this.form)
            });
            if (res.ok) {
                let data = await res.json();
                if (this.editingId) {
                    let idx = this.customers.findIndex(c => c.id === this.editingId);
                    if (idx !== -1) this.customers[idx] = data;
                } else {
                    this.customers.push(data);
                }
                this.closeModal();
            } else alert('Error saving customer');
        },
        async deleteCustomer(c) {
            if (!confirm(`Delete ${c.name}?`)) return;
            let res = await fetch(`/customers/${c.id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
            if (res.ok) this.customers = this.customers.filter(cust => cust.id !== c.id);
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
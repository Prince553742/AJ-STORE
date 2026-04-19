<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AJ's Store · Expenses</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4f46e5">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100">

<div x-data="expenseManager()" x-init="initExpenses(@js($expenses))" class="flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <aside class="w-72 bg-white shadow-xl border-r flex flex-col">
        <div class="p-6 border-b">
            <h1 class="text-3xl font-bold text-indigo-700">AJ's Store</h1>
            <p class="text-gray-500">Expense Tracker</p>
        </div>
        <nav class="flex-1 p-4">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50">DASHBOARD</a>
            <a href="{{ route('items.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 mt-2">PRICE LIST</a>
            <a href="{{ route('stocks.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 mt-2">STOCKS</a>
            <a href="{{ route('debts.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 mt-2">DEBTS</a>
            <a href="{{ route('customers.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 mt-2">CUSTOMERS</a>
            <a href="{{ route('expenses.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-indigo-50 text-indigo-700 mt-2">EXPENSES</a>
            <a href="{{ route('purchase-orders.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 mt-2">PURCHASE ORDERS</a>
            <a href="{{ route('sales.history') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 mt-2">SALES HISTORY</a>
            <div class="mt-8 text-xs text-gray-400 px-4 pt-8 border-t">POCO Pad M1 · Tablet mode</div>
        </nav>
    </aside>

    <main class="flex-1 overflow-y-auto p-6">
        <div class="max-w-5xl mx-auto">
            <div class="flex justify-between mb-4">
                <h1 class="text-2xl font-bold">Expenses</h1>
                <button @click="openModal()" class="bg-indigo-600 text-white px-4 py-2 rounded-xl">+ Add Expense</button>
            </div>
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">Description</th>
                            <th class="px-6 py-3 text-left">Amount (₱)</th>
                            <th class="px-6 py-3 text-left">Date</th>
                            <th class="px-6 py-3 text-left">Category</th>
                            <th class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="exp in expenses" :key="exp.id">
                            <tr class="border-t">
                                <td class="px-6 py-3" x-text="exp.description"></td>
                                <td class="px-6 py-3" x-text="'₱ ' + (parseFloat(exp.amount) || 0).toFixed(2)"></td>
                                <td class="px-6 py-3" x-text="exp.expense_date ? new Date(exp.expense_date).toLocaleDateString() : '—'"></td>
                                <td class="px-6 py-3" x-text="exp.category || '—'"></td>
                                <td class="px-6 py-3 text-center">
                                    <button @click="editExpense(exp)" class="text-blue-600">Edit</button>
                                    <button @click="deleteExpense(exp)" class="text-red-600 ml-2">Delete</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <div x-show="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
                <h2 class="text-xl font-bold mb-4" x-text="editingId ? 'Edit Expense' : 'Add Expense'"></h2>
                <div class="space-y-3">
                    <input type="text" x-model="form.description" placeholder="Description" class="w-full border rounded-xl px-3 py-2">
                    <input type="number" step="0.01" x-model="form.amount" placeholder="Amount" class="w-full border rounded-xl px-3 py-2">
                    <input type="date" x-model="form.expense_date" class="w-full border rounded-xl px-3 py-2">
                    <input type="text" x-model="form.category" placeholder="Category (e.g., Utilities, Supplies)" class="w-full border rounded-xl px-3 py-2">
                    <textarea x-model="form.notes" placeholder="Notes" class="w-full border rounded-xl px-3 py-2" rows="2"></textarea>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button @click="closeModal" class="px-4 py-2 border rounded-xl">Cancel</button>
                    <button @click="saveExpense" class="px-4 py-2 bg-indigo-600 text-white rounded-xl">Save</button>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
function expenseManager() {
    return {
        expenses: [],
        showModal: false,
        editingId: null,
        form: { description: '', amount: 0, expense_date: '', category: '', notes: '' },
        initExpenses(data) { this.expenses = data; },
        openModal() { this.form = { description: '', amount: 0, expense_date: '', category: '', notes: '' }; this.showModal = true; },
        editExpense(exp) { this.editingId = exp.id; this.form = {...exp, expense_date: exp.expense_date ? exp.expense_date.slice(0,10) : ''}; this.showModal = true; },
        closeModal() { this.showModal = false; this.editingId = null; this.form = { description: '', amount: 0, expense_date: '', category: '', notes: '' }; },
        async saveExpense() {
            let url = this.editingId ? `/expenses/${this.editingId}` : '/expenses';
            let method = this.editingId ? 'PUT' : 'POST';
            let res = await fetch(url, {
                method,
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify(this.form)
            });
            if (res.ok) {
                let data = await res.json();
                if (this.editingId) {
                    let idx = this.expenses.findIndex(e => e.id === this.editingId);
                    if (idx !== -1) this.expenses[idx] = data;
                } else {
                    this.expenses.unshift(data);
                }
                this.closeModal();
            } else alert('Error saving expense');
        },
        async deleteExpense(exp) {
            if (!confirm(`Delete expense "${exp.description}"?`)) return;
            let res = await fetch(`/expenses/${exp.id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
            if (res.ok) this.expenses = this.expenses.filter(e => e.id !== exp.id);
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
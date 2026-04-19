<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AJ's Store · Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4f46e5">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
</head>
<body class="bg-gray-100">

<div x-data="dashboard()" x-init="init()" class="flex h-screen overflow-hidden">
    <!-- Sidebar (same as other pages) -->
    <aside class="w-72 bg-white shadow-xl border-r flex flex-col">
        <div class="p-6 border-b">
            <h1 class="text-3xl font-bold text-indigo-700">AJ's Store</h1>
            <p class="text-gray-500">Dashboard</p>
        </div>
        <nav class="flex-1 p-4">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-indigo-50 text-indigo-700">DASHBOARD</a>
            <a href="{{ route('items.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 mt-2">PRICE LIST</a>
            <a href="{{ route('stocks.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 mt-2">STOCKS</a>
            <a href="{{ route('debts.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 mt-2">DEBTS</a>
            <a href="{{ route('customers.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 mt-2">CUSTOMERS</a>
            <a href="{{ route('expenses.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 mt-2">EXPENSES</a>
            <a href="{{ route('purchase-orders.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 mt-2">PURCHASE ORDERS</a>
            <a href="{{ route('sales.history') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 mt-2">SALES HISTORY</a>
            <div class="mt-8 text-xs text-gray-400 px-4 pt-8 border-t">POCO Pad M1 · Tablet mode</div>
        </nav>
    </aside>

    <main class="flex-1 overflow-y-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Store Dashboard</h1>

        <!-- Low Stock Alert -->
        <div class="bg-white rounded-2xl shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4 flex items-center gap-2"><i class="fas fa-exclamation-triangle text-red-500"></i> Low Stock Alerts</h2>
            <div x-show="lowStockItems.length === 0" class="text-green-600">✅ All items are above reorder level.</div>
            <div x-show="lowStockItems.length > 0">
                <table class="min-w-full">
                    <thead class="bg-gray-50"><tr><th class="px-4 py-2 text-left">Item</th><th>Current Stock</th><th>Reorder Level</th><th>Suggested Order</th></tr></thead>
                    <tbody>
                        <template x-for="item in lowStockItems" :key="item.id">
                            <tr class="border-t">
                                <td class="px-4 py-2" x-text="item.name"></td>
                                <td class="px-4 py-2" x-text="item.stock"></td>
                                <td class="px-4 py-2" x-text="item.reorder_level"></td>
                                <td class="px-4 py-2" x-text="Math.max(0, item.reorder_level - item.stock + 5)"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sales & Expenses Summary -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-2xl shadow-md p-6">
                <h2 class="text-xl font-semibold mb-2">Today's Sales</h2>
                <p class="text-3xl font-bold text-green-600" x-text="'₱ ' + todaySales.toFixed(2)"></p>
            </div>
            <div class="bg-white rounded-2xl shadow-md p-6">
                <h2 class="text-xl font-semibold mb-2">Today's Expenses</h2>
                <p class="text-3xl font-bold text-red-600" x-text="'₱ ' + todayExpenses.toFixed(2)"></p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-2">Net Profit Today</h2>
            <p class="text-3xl font-bold text-indigo-600" x-text="'₱ ' + (todaySales - todayExpenses).toFixed(2)"></p>
        </div>

        <!-- Sales Chart -->
        <div class="bg-white rounded-2xl shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Sales Trend</h2>
                <select x-model="chartPeriod" @change="loadChartData" class="border rounded-xl px-3 py-1">
                    <option value="day">Today</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                </select>
            </div>
            <canvas id="salesChart" width="400" height="200"></canvas>
        </div>
    </main>
</div>

<script>
function dashboard() {
    return {
        lowStockItems: [],
        todaySales: 0,
        todayExpenses: 0,
        chartPeriod: 'month',
        chart: null,
        async init() {
            await this.fetchLowStock();
            await this.fetchTodaySales();
            await this.fetchTodayExpenses();
            await this.loadChartData();
        },
        async fetchLowStock() {
            const res = await fetch('/api/low-stock');
            if (res.ok) this.lowStockItems = await res.json();
        },
        async fetchTodaySales() {
            const res = await fetch('/api/today-sales');
            if (res.ok) this.todaySales = await res.json();
        },
        async fetchTodayExpenses() {
            const res = await fetch('/api/today-expenses');
            if (res.ok) this.todayExpenses = await res.json();
        },
        async loadChartData() {
            const res = await fetch(`/api/sales-chart?period=${this.chartPeriod}`);
            if (res.ok) {
                const data = await res.json();
                if (this.chart) this.chart.destroy();
                const ctx = document.getElementById('salesChart').getContext('2d');
                this.chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Sales (₱)',
                            data: data.values,
                            borderColor: '#4f46e5',
                            backgroundColor: 'rgba(79, 70, 229, 0.1)',
                            fill: true,
                            tension: 0.3
                        }]
                    }
                });
            }
        }
    }
}
</script>
</body>
</html>
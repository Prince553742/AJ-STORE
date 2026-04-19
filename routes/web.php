<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\CreditSaleController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\PurchaseOrderController;
use Illuminate\Support\Facades\Route;

// Items & Price List
Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::post('/items', [ItemController::class, 'store'])->name('items.store');
Route::delete('/items/{id}', [ItemController::class, 'destroy'])->name('items.destroy');
Route::patch('/items/{item}/price', [ItemController::class, 'updatePrice'])->name('items.update-price');
Route::patch('/items/{item}/name', [ItemController::class, 'updateName'])->name('items.update-name');
Route::patch('/items/{item}/category', [ItemController::class, 'updateCategory'])->name('items.update-category');
Route::get('/categories', [ItemController::class, 'getCategories'])->name('categories.list');

// Daily Sales
Route::post('/record-sale', [ItemController::class, 'recordDailySale'])->name('record.sale');
Route::get('/sales', [ItemController::class, 'salesHistory'])->name('sales.history');

// Stocks
Route::get('/stocks', [ItemController::class, 'stocksIndex'])->name('stocks.index');
Route::patch('/items/{item}/stock', [ItemController::class, 'updateStock'])->name('items.update-stock');

// Customers
Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

// Debts (Credit Sales)
Route::get('/debts', [CreditSaleController::class, 'index'])->name('debts.index');
Route::post('/debts', [CreditSaleController::class, 'store'])->name('debts.store');
Route::patch('/debts/{creditSale}/pay', [CreditSaleController::class, 'markAsPaid'])->name('debts.pay');

// Dashboard & API
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/api/low-stock', [DashboardController::class, 'lowStock']);
Route::get('/api/today-sales', [DashboardController::class, 'todaySales']);
Route::get('/api/today-expenses', [DashboardController::class, 'todayExpenses']);
Route::get('/api/sales-chart', [DashboardController::class, 'salesChartData']);

// Expenses (full CRUD)
Route::resource('expenses', ExpenseController::class)->except(['create', 'edit']);

// Purchase Orders (no supplier)
Route::get('/purchase-orders', [PurchaseOrderController::class, 'index'])->name('purchase-orders.index');
Route::post('/purchase-orders', [PurchaseOrderController::class, 'store'])->name('purchase-orders.store');
Route::patch('/purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');
Route::delete('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'destroy'])->name('purchase-orders.destroy');
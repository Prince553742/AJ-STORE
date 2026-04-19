<?php

use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::post('/items', [ItemController::class, 'store'])->name('items.store');
Route::delete('/items/{id}', [ItemController::class, 'destroy'])->name('items.destroy');
Route::patch('/items/{item}/price', [ItemController::class, 'updatePrice'])->name('items.update-price');
Route::patch('/items/{item}/name', [ItemController::class, 'updateName'])->name('items.update-name');
Route::patch('/items/{item}/category', [ItemController::class, 'updateCategory'])->name('items.update-category');
Route::get('/categories', [ItemController::class, 'getCategories'])->name('categories.list');
Route::post('/record-sale', [ItemController::class, 'recordDailySale'])->name('record.sale');
Route::get('/sales', [ItemController::class, 'salesHistory'])->name('sales.history');
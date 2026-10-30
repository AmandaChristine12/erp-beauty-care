<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SalesController;

// Halaman utama otomatis mengarah ke modul Sales & CRM (Milik Anggota 1)
Route::get('/', function () {
    return redirect()->route('sales.index');
});

// Modul Sales & CRM (Tanggung Jawab Anggota 1)
Route::prefix('sales')->name('sales.')->group(function () {
    Route::get('/', [SalesController::class, 'index'])->name('index');
    Route::get('/create', [SalesController::class, 'create'])->name('create');
    Route::post('/store', [SalesController::class, 'store'])->name('store');
    Route::get('/customers', [SalesController::class, 'customers'])->name('customers');
    Route::get('/customers/{id}', [SalesController::class, 'customerDetail'])->name('customer.detail');
    Route::get('/{id}', [SalesController::class, 'show'])->name('show');
    Route::post('/{id}/confirm', [SalesController::class, 'confirm'])->name('confirm');
});

// Shortcut URL Customers (S-02 / F-03)
Route::get('/customers', [SalesController::class, 'customers'])->name('customers.index');

// Tombol Sakti: Reset Database ke Baseline Seed Resmi
Route::post('/system/reset-seed', [SalesController::class, 'resetSeed'])->name('system.reset-seed');

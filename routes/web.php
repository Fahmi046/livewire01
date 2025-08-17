<?php

use App\Livewire\ProductForm;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/produk', ProductForm::class);

Route::get('/customers', function () {
    return view('customers.index');
});

Route::get('/shopping-cart', function () {
    return view('cart.index');
});
Route::get('/orders', function () {
    return view('orders'); // Pastikan Anda mengarah ke nama file: orders
});

Route::get('/penjualan', function () {
    return view('penjualan');
});

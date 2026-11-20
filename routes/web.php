<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BalController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProdukController;
use Illuminate\Support\Facades\Route;

Route::get('/', [KatalogController::class, 'index'])->name('katalog.index');
Route::get('/katalog/{produk}', [KatalogController::class, 'show'])->name('katalog.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
    Route::post('/keranjang/{produk}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/keranjang/{produk}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/keranjang/{produk}', [CartController::class, 'remove'])->name('cart.remove');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::get('/pesanan', [PesananController::class, 'index'])->name('pesanan.index');
    Route::get('/pesanan/{pesanan}', [PesananController::class, 'show'])->name('pesanan.show');
    Route::post('/pesanan/{pesanan}/bayar', [CheckoutController::class, 'pay'])->name('pesanan.pay');
});

Route::middleware(['auth', 'role:owner,admin,kasir'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos', [PosController::class, 'store'])->name('pos.store');
    Route::get('/pos/{pesanan}/nota', [PosController::class, 'nota'])->name('pos.nota');
});

Route::middleware(['auth', 'role:owner,admin'])->prefix('admin')->group(function () {
    Route::resource('bal', BalController::class)->except('show')->names('bal');
    Route::get('/bal/{bal}', [BalController::class, 'show'])->name('bal.show');
    Route::post('/bal/{bal}/biaya', [BalController::class, 'storeBiaya'])->name('bal.biaya.store');
    Route::delete('/bal/{bal}/biaya/{biaya}', [BalController::class, 'destroyBiaya'])->name('bal.biaya.destroy');

    Route::resource('produk', ProdukController::class);
    Route::get('/produk/{produk}/label', [ProdukController::class, 'label'])->name('produk.label');
});

Route::middleware(['auth', 'role:owner,admin'])->group(function () {
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
});

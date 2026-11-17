<?php
use App\Http\Controllers\Pembeli\CheckoutController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Pembeli\MenuController;
use App\Http\Controllers\Pembeli\CartController;
use App\Http\Controllers\ProfileController;

// Landing Page
Route::get('/', [LandingController::class, 'index'])
    ->name('landing');

// Menu & Pesanan (bisa dibuka tamu maupun pengguna login)
Route::get('/menu', [MenuController::class, 'index'])
    ->name('menu.index');

// Keranjang
Route::get('/keranjang', [CartController::class, 'index'])
    ->name('keranjang.index');
Route::get('/keranjang/detail', [CartController::class, 'detail'])
    ->name('keranjang.detail');

// Authentication (hanya untuk yang belum login)
Route::middleware('guest')->group(function () {

    // Login
    Route::get('/login', [LoginController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'login'])
        ->name('login.process');

    // Register
    Route::get('/register', [RegisterController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [RegisterController::class, 'register'])
        ->name('register.process');

});

// Area setelah login
Route::middleware('auth')->group(function () {

    // Sementara: pembeli diarahkan ke halaman menu
    Route::get('/pembeli/dashboard', fn () => redirect()->route('menu.index'))
        ->name('pembeli.dashboard');

    // Placeholder sampai dashboard penjual dibuat
    Route::get('/penjual/dashboard', fn () => 'Dashboard penjual belum dibuat.')
        ->name('penjual.dashboard');

    // Profil pengguna
    Route::get('/profil', [ProfileController::class, 'show'])
        ->name('profil');

    Route::put('/profil/password', [ProfileController::class, 'updatePassword'])
        ->name('profil.password');

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');

    // Checkout & pembayaran
    Route::post('/checkout', [CheckoutController::class, 'store'])
        ->name('checkout.store');

    Route::get('/pembayaran/{id}', [CheckoutController::class, 'show'])
        ->name('pembayaran.show');

    Route::post('/pembayaran/{id}/simulasi', [CheckoutController::class, 'simulatePayment'])
        ->name('pembayaran.simulasi');

    // Status Pesanan (placeholder sampai halaman aslinya dibuat)
    Route::get('/status-pesanan', fn () => 'Halaman status pesanan belum dibuat.')
        ->name('pesanan.status');

});
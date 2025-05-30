<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Unit\PemesananController;
use App\Http\Controllers\Unit\TransaksiController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route(auth()->user()->role === 'admin' ? 'admin.dashboard' : 'unit.dashboard');
    });

    Route::get('/unit/dashboard', function () {
        return view('unit.dashboard');
    })->name('unit.dashboard');
});

Route::middleware(['auth', 'is_admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', fn() => view('admin.dashboard'))->name('admin.dashboard');
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('admin.users.update');
    Route::get('/vendors', [VendorController::class, 'index'])->name('admin.vendors.index');
    Route::post('/vendors', [VendorController::class, 'store'])->name('admin.vendors.store');
    Route::put('/vendors/{id}', [VendorController::class, 'update'])->name('admin.vendors.update');
    Route::delete('/vendors/{id}', [VendorController::class, 'destroy'])->name('admin.vendors.destroy');
});

Route::middleware(['auth'])->prefix('unit')->group(function () {
    Route::get('/dashboard', fn() => view('unit.dashboard'))->name('unit.dashboard');
    Route::get('/pemesanan', [PemesananController::class, 'index'])->name('unit.pemesanan.index');
    Route::post('/pemesanan', [PemesananController::class, 'store'])->name('unit.pemesanan.store');
    Route::get('/unit/transaksi', [TransaksiController::class, 'index'])->name('unit.transaksi.index');
    Route::post('/unit/transaksi/{id}/reorder', [App\Http\Controllers\Unit\TransaksiController::class, 'reorder'])->name('unit.transaksi.reorder');
});

require __DIR__ . '/auth.php';

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BarangController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GudangController;
use App\Http\Controllers\Admin\CounterController;
use App\Http\Controllers\Admin\KasirController;
use App\Http\Controllers\Admin\UserAuthController;
use App\Http\Controllers\Admin\PemesananController;
use App\Http\Controllers\Admin\PersediaanMasukController;
use App\Http\Controllers\Admin\PermintaanCounterController;
use App\Http\Controllers\Admin\PengirimanCounterController;
use App\Http\Controllers\Admin\PenjualanController;

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
//     return view('pages.dashboard.index');
// });

Route::controller(UserAuthController::class)->group(function () {
    Route::get('/auth', 'index')->name('auth');
});

Route::controller(DashboardController::class)->group(function () {
    Route::get('/', 'index')->name('dashboard');
});

Route::controller(BarangController::class)->group(function () {
    Route::get('/barang', 'index')->name('barang');
    Route::get('/barang/create', 'create')->name('barang.create');
    Route::get('/barang/edit/{slug}', 'edit')->name('barang.edit');
    Route::post('/barang/store', 'store')->name('barang.store');
    Route::post('/barang/update/{slug}', 'update')->name('barang.update');
    Route::get('/barang/destroy/{slug}', 'destroy')->name('barang.destroy');
    Route::post('/barang/biayapenyimpanan', 'biayaPenyimpanan')->name('barang.biayapenyimpanan');
});

Route::controller(GudangController::class)->group(function () {
    Route::get('/gudang', 'index')->name('gudang');
    Route::get('/gudang/edit/{slug}', 'edit')->name('gudang.edit');
    Route::post('/gudang/update/{slug}', 'update')->name('gudang.update');
});

Route::controller(CounterController::class)->group(function () {
    Route::get('/counter', 'index')->name('counter');
    Route::get('/counter/create', 'create')->name('counter.create');
    Route::post('/counter/store', 'store')->name('counter.store');
    Route::get('/counter/edit/{slug}', 'edit')->name('counter.edit');
    Route::post('/counter/update/{slug}', 'update')->name('counter.update');
    Route::get('/counter/destroy/{slug}', 'destroy')->name('counter.destroy');
});

Route::controller(PemesananController::class)->group(function () {
    Route::get('/pemesanan', 'index')->name('pemesanan');
});

Route::controller(PersediaanMasukController::class)->group(function () {
    Route::get('/persediaan-masuk', 'index')->name('persediaan-masuk');
});

Route::controller(PermintaanCounterController::class)->group(function () {
    Route::get('/permintaan-counter', 'index')->name('permintaan-counter');
});

Route::controller(PengirimanCounterController::class)->group(function () {
    Route::get('/pengiriman-counter', 'index')->name('pengiriman-counter');
});

Route::controller(PenjualanController::class)->group(function () {
    Route::get('/penjualan', 'index')->name('penjualan');
});

Route::controller(KasirController::class)->group(function () {
    Route::get('/kasir', 'index')->name('kasir');
});

// Route::get('/login', function () {
//     return view('pages.auth.index');
// });

// Route::get('/barang', function () {
//     return view('pages.barang.index');
// });

// Route::get('/tambahbarang', function () {
//     return view('pages.barang.create');
// });

// Route::get('/editbarang', function () {
//     return view('pages.barang.edit');
// });

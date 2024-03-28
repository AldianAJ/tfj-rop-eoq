<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BarangController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GudangController;
use App\Http\Controllers\Admin\CounterController;
use App\Http\Controllers\Admin\SupplierController;
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
    Route::post('/auth/login', 'login')->name('auth.login');
});

Route::middleware('user')->group(function () {
    Route::controller(UserAuthController::class)->group(function () {
        Route::get('/auth/logout', 'logout')->name('auth.logout');
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
        Route::post('/barang/detail', 'detailQuantity')->name('barang.detail');
        Route::get('/barang/checkrop', 'checkROP')->name('barang.checkROP');
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

    Route::controller(SupplierController::class)->group(function () {
        Route::get('/supplier', 'index')->name('supplier');
        Route::get('/supplier/create', 'create')->name('supplier.create');
        Route::post('/supplier/store', 'store')->name('supplier.store');
        Route::get('/supplier/edit/{slug}', 'edit')->name('supplier.edit');
        Route::post('/supplier/update/{slug}', 'update')->name('supplier.update');
        Route::get('/supplier/destroy/{slug}', 'destroy')->name('supplier.destroy');
    });

    Route::controller(PemesananController::class)->group(function () {
        Route::get('/pemesanan', 'index')->name('pemesanan');
        Route::get('/pemesanan/persediaan-baru', 'createNewPersediaan')->name('pemesanan.create.new-persediaan');
        Route::get('/pemesanan/create', 'create')->name('pemesanan.create');
        Route::post('/pemesanan/store', 'store')->name('pemesanan.store');
        Route::post('/pemesanan/hitung', 'hitungEOQ')->name('pemesanan.hitung');
        Route::get('/pemesanan/detail/{slug}', 'detail')->name('pemesanan.detail');
        Route::post('/pemesanan/persetujuan', 'persetujuan')->name('pemesanan.persetujuan');
        Route::get('/pemesanan/dipesan/{slug}', 'dipesan')->name('pemesanan.dipesan');
        Route::get('/pemesanan/history', 'indexHistory')->name('pemesanan.history');
        Route::post('/pemesanan/history/detail', 'detailHistory')->name('pemesanan.detailHistory');
        Route::get('/pemesanan/exportPDF/{slug}', 'exportPDF')->name('pemesanan.exportPDF');
    });

    Route::controller(PersediaanMasukController::class)->group(function () {
        Route::get('/persediaan-masuk', 'index')->name('persediaan-masuk');
        Route::get('/persediaan-masuk/detail/{slug}', 'detail')->name('persediaan-masuk.detail');
        Route::get('/persediaan-masuk/diterima/{slug}/{id}', 'addDiterimaTemporary')->name('persediaan-masuk.add');
        Route::get('/persediaan-masuk/store/{slug}', 'store')->name('persediaan-masuk.store');
    });

    Route::controller(PermintaanCounterController::class)->group(function () {
        Route::get('/permintaan-counter', 'index')->name('permintaan-counter');
        Route::get('/permintaan-counter/create', 'create')->name('permintaan-counter.create');
        Route::post('/permintaan-counter/store', 'store')->name('permintaan-counter.store');
        Route::post('/permintaan-counter/detail', 'detail')->name('permintaan-counter.detail');
        Route::get('/permintaan-counter/detail/{slug}', 'detailByGudang')->name('permintaan-counter.detailByGudang');
        Route::get('/permintaan-counter/checkstatus', 'checkStatus')->name('permintaan.checkStatus');
        Route::get('/permintaan-counter/persetujuan/{slug}/{id}', 'createPersetujuan')->name('permintaan-counter.persetujuan');
        Route::post('/permintaan-counter/temp/persetujuan', 'temporaryPersetujuan')->name('permintaan-counter.tmpPersetujuan');
        Route::get('/permintaan-counter/pengiriman/store/{slug}', 'storePengiriman')->name('permintaan-counter.storePersetujuan');
        Route::get('/permintaan-counter/history', 'indexHistory')->name('permintaan-counter.history');
        Route::get('/permintaan-counter/exportPDF/{slug}', 'exportPDF')->name('permintaan-counter.exportPDF');
    });

    Route::controller(PengirimanCounterController::class)->group(function () {
        Route::get('/pengiriman-counter', 'index')->name('pengiriman-counter');
        Route::get('/pengiriman-counter/detail/{slug}', 'show')->name('pengiriman-counter.detail');
        Route::get('/pengiriman-counter/store/penerimaan/{slug}', 'storePenerimaan')->name('pengiriman-counter.penerimaan');
        Route::get('/pengiriman-counter/keep/counter', 'indexBarangDiambil')->name('pengiriman-counter.barangDiambil');
        Route::get('/pengiriman-counter/history', 'indexHistory')->name('pengiriman-counter.history');
        Route::post('/pengiriman-counter/history/detail', 'detailHistory')->name('pengiriman-counter.detailHistory');
        Route::get('/pengiriman-counter/exportPDF/{slug}', 'exportPDF')->name('pengiriman-counter.exportPDF');
        Route::get('/pengiriman_counter/update/status/{pengiriman_counter_id}/{barang_id}', 'updateStatus')->name('pengiriman-counter.updateStatus');
    });

    Route::controller(PenjualanController::class)->group(function () {
        Route::get('/penjualan', 'index')->name('penjualan');
        Route::post('/penjualan/filter', 'filter')->name('penjualan.filter');
        Route::post('/penjualan/detail', 'detail')->name('penjualan.detail');
        Route::post('/penjualan/exportPDF', 'exportPDF')->name('penjualan.exportPDF');
    });

    Route::controller(KasirController::class)->group(function () {
        Route::get('/kasir', 'index')->name('kasir');
        Route::post('/kasir/store', 'store')->name('kasir.store');
    });
});

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;

class DashboardController extends Controller
{
    public function userAuth()
    {
        $user = Auth::guard('user')->user();
        return $user;
    }

    public function index(Request $request)
    {
        $user = $this->userAuth();
        if ($request->ajax()) {
            $barangs = DB::table('barangs as b')
                ->join('barang_gudangs as bg', 'b.barang_id', '=', 'bg.barang_id')
                ->join('barang_counters as bc', 'b.barang_id', '=', 'bc.barang_id')
                ->join('detail_penjualans as dp', 'bc.barang_counter_id', '=', 'dp.barang_counter_id')
                ->selectRaw('b.barang_id, b.slug, b.nama_barang, b.harga_barang, b.biaya_penyimpanan, b.rop,((SELECT SUM(stok_masuk)-SUM(stok_keluar) FROM barang_gudangs WHERE barang_id = b.barang_id GROUP BY barang_id) + (SELECT SUM(stok_masuk)-SUM(stok_keluar) FROM barang_counters WHERE barang_id = b.barang_id GROUP BY barang_id)) as qty_total, b.ss, round(sum(dp.quantity) / 30, 2) as avg')
                ->groupByRaw("b.barang_id, b.slug, b.nama_barang, b.harga_barang, b.biaya_penyimpanan, b.rop, ((SELECT SUM(stok_masuk)-SUM(stok_keluar) FROM barang_gudangs WHERE barang_id = b.barang_id GROUP BY barang_id) + (SELECT SUM(stok_masuk)-SUM(stok_keluar) FROM barang_counters WHERE barang_id = b.barang_id GROUP BY barang_id))")
                ->orderByRaw("((SELECT SUM(stok_masuk)-SUM(stok_keluar) FROM barang_gudangs WHERE barang_id = b.barang_id GROUP BY barang_id) + (SELECT SUM(stok_masuk)-SUM(stok_keluar) FROM barang_counters WHERE barang_id = b.barang_id GROUP BY barang_id)) <= b.rop desc, b.barang_id asc")
                ->get();

            return DataTables::of($barangs)
                ->addColumn('action', function ($object) {
                    $html = ' <a href="' . route("pemesanan.create") . '" class="btn btn-success waves-effect waves-light">'
                        . ' <i class="bx bx-edit align-middle me-2 font-size-18"></i>Edit</a>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $jumlah_jenis = DB::table('barangs')->count();

        $total_transaksi = DB::table('penjualans')
            ->whereMonth('tanggal_penjualan', Carbon::now()->format('m'))
            ->whereYear('tanggal_penjualan', Carbon::now()->format('Y'))
            ->count();

        $penjualan = DB::table('penjualans')
            ->selectRaw('SUM(grand_total) as total_pendapatan')
            ->whereMonth('tanggal_penjualan', Carbon::now()->format('m'))
            ->whereYear('tanggal_penjualan', Carbon::now()->format('Y'))
            ->first();

        $jumlah_counter = DB::table('counters')->count();
        setlocale(LC_ALL, 'IND');
        $bulan_tahun = date('F Y');


        return view('pages.dashboard.index', compact('user', 'jumlah_jenis', 'total_transaksi', 'penjualan', 'jumlah_counter', 'bulan_tahun'));
    }
}

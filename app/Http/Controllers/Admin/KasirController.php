<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\BarangCounter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\Penjualan;
use App\Models\Admin\DetailPenjualan;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;


class KasirController extends Controller
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
            $counters = DB::table('counters')
                ->select('counter_id')
                ->where('user_id', $user->user_id)
                ->first();

            $data = DB::table('barang_counters as a')
                ->select('a.barang_counter_id', 'b.barang_id', 'b.nama_barang', 'b.harga_barang', 'a.slug')
                ->selectRaw('(a.stok_masuk - a.stok_keluar) as quantity')
                ->join('barangs as b', 'a.barang_id', '=', 'b.barang_id')
                ->where('a.counter_id', $counters->counter_id)
                ->orderBy('a.barang_counter_id', 'ASC')
                ->get();

            foreach ($data as $item) {
                $item->action = '<button class="btn btn-primary waves-effect waves-light btn-add" data-bs-toggle="modal"
                data-bs-target="#quantityModal"><i class="bx bxs-cart align-middle font-size-18"></i></button>';
            }

            return response()->json($data);
        }

        return view('pages.kasir.index', compact('user'));
    }


    public function store(Request $request)
    {
        $user = $this->userAuth();
        $counters = DB::table('counters')->where('user_id', $user->user_id)->first();
        $counter_id = $counters->counter_id;
        $keranjangs = json_decode($request->keranjang);

        DB::beginTransaction();
        try {
            $penjualan_id = Penjualan::generatePenjualanCounterId($counter_id);
            $penjualans = new Penjualan;
            $penjualans->penjualan_id = $penjualan_id;
            $penjualans->slug = Str::random(16);
            $penjualans->counter_id = $counter_id;
            $penjualans->grand_total = $request->grand_total;
            $penjualans->tanggal_penjualan = Carbon::now();
            $penjualans->save();

            foreach ($keranjangs as $keranjang) {
                $detail_penjualans = new DetailPenjualan;
                $detail_penjualans->penjualan_id = $penjualan_id;
                $detail_penjualans->barang_counter_id = $keranjang->barang_counter_id;
                $detail_penjualans->quantity = $keranjang->jumlah;
                $detail_penjualans->subtotal = $keranjang->subtotal;
                $detail_penjualans->save();

                $barang_counters = BarangCounter::where('barang_counter_id', $keranjang->barang_counter_id)->first();
                $barang_counters->stok_keluar += $keranjang->jumlah;
                $barang_counters->save();
            }
            DB::commit();
            return response()->json([], 200);
        } catch (\Exception $ex) {
            echo $ex->getMessage();
            DB::rollBack();
        }
    }
}

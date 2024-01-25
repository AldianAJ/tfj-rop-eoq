<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\DetailPersediaanMasuk;
use App\Models\Admin\Pemesanan;
use Illuminate\Http\Request;
use App\Models\Admin\PersediaanMasuk;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;
use App\Models\Admin\BarangGudang;

class PersediaanMasukController extends Controller
{

    public function userAuth()
    {
        $user = Auth::guard('user')->user();
        return $user;
    }

    public function jumlahHari($bulan_tahun)
    {
        $jumlah = date('t', strtotime("06-2023" . "01"));
        return $jumlah;
    }

    public function hitung()
    {
        $bulan_tahun = DB::table('penjualans')
            ->selectRaw('DATE_FORMAT(MAX(tanggal_penjualan),"%m-%Y") as bulan')
            ->whereRaw('DATE_FORMAT(tanggal_penjualan, "%m-%Y") < DATE_FORMAT(now(), "%m-%Y")')
            ->first();
        $barang_id = "B00001";
        $data = DB::table('detail_penjualans as dp')
            ->join('penjualans as p', 'dp.penjualan_id', '=', 'p.penjualan_id')
            ->join('barang_counters as bc', 'dp.barang_counter_id', '=', 'bc.barang_counter_id')
            ->join('barangs as b', 'bc.barang_id', '=', 'b.barang_id')
            ->selectRaw('max(dp.quantity) as max, round(avg(dp.quantity)) as avg, sum(dp.quantity) as total')
            ->whereRaw("b.barang_id = '" . $barang_id . "' AND DATE_FORMAT(p.tanggal_penjualan, '%m-%Y') = '" . $bulan_tahun->bulan . "'")->first();
        $now = Carbon::now();
        $subdate = Carbon::now()->subDays(7);
        $avg_date = DB::table('pemesanans as p')
            ->join('persediaan_masuks as pm', 'p.pemesanan_id', '=', 'pm.pemesanan_id')
            ->selectRaw('round(avg(DATEDIFF( pm.tanggal_persediaan_masuk, p.tanggal_pemesanan))) as lead_time')
            ->where('p.status_pemesanan', 'Selesai')
            ->whereBetween('pm.tanggal_persediaan_masuk', [$subdate, $now])
            ->first();
        $lead_time = !empty($avg_date) ? $avg_date->lead_time : 2;
        $ss = ($data->max - $data->avg) * $lead_time;
        $jumlah_hari = $this->jumlahHari($bulan_tahun->bulan);
        $d = (int)round($data->total / $jumlah_hari);
        $rop = ($d * $lead_time) + $ss;
        dd($jumlah_hari);
    }

    public function index(Request $request)
    {
        $user = $this->userAuth();
        $path = 'persediaan-masuk';
        if ($request->ajax()) {
            $pemesanan = DB::table('pemesanans')
                ->where('status_pemesanan', 'Dipesan')
                ->orderBy('tanggal_pemesanan', 'desc')
                ->get();

            return DataTables::of($pemesanan)
                ->addColumn('action', function ($object) use ($path) {
                    $html = ' <a href="' . route($path . '.detail', ["slug" => $object->slug]) . '" class="btn btn-success waves-effect waves-light">'
                        . ' <i class="bx bx-transfer-alt align-middle me-2 font-size-18"></i>Proses</a>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.persediaan-masuk.index', compact('user'));
    }

    public function detail($slug)
    {
        $user = $this->userAuth();
        $pemesanan = DB::table('pemesanans')
            ->where('slug', $slug)
            ->first();

        $details = DB::table('detail_pemesanans as dp')
            ->join('pemesanans as p', 'dp.pemesanan_id', '=', 'p.pemesanan_id')
            ->join('barangs as b', 'dp.barang_id', '=', 'b.barang_id')
            ->select('dp.id', 'p.pemesanan_id', 'p.slug', 'b.nama_barang', 'dp.jumlah_pemesanan', 'b.barang_id')
            ->where('p.slug', $slug)
            ->get();

        $temporary_masuk = session("temporary_masuk");
        $count_detail = count($details);
        $count_tmp = (array)$temporary_masuk;
        $count_tmp = count($count_tmp);

        return view('pages.persediaan-masuk.detail', compact('user', 'pemesanan', 'details', 'temporary_masuk', 'count_detail', 'count_tmp'));
    }

    public function addDiterimaTemporary($slug, $id)
    {
        $detail = DB::table('detail_pemesanans as dp')
            ->join('pemesanans as p', 'dp.pemesanan_id', '=', 'p.pemesanan_id')
            ->join('barangs as b', 'dp.barang_id', '=', 'b.barang_id')
            ->select('dp.id', 'p.pemesanan_id', 'p.slug', 'b.nama_barang', 'dp.jumlah_pemesanan', 'b.barang_id')
            ->where(['p.slug' => $slug, 'dp.id' => $id])
            ->first();

        $temporary_masuk = session("temporary_masuk");
        $temporary_masuk[$detail->pemesanan_id . "/" . $detail->barang_id] = [
            "status" => "Selesai"
        ];

        session(["temporary_masuk" => $temporary_masuk]);
        return redirect()->route('persediaan-masuk.detail', ["slug" => $detail->slug]);
    }

    public function store($slug)
    {
        $pemesanan = Pemesanan::where('slug', $slug)
            ->first();

        $details = DB::table('detail_pemesanans as dp')
            ->join('pemesanans as p', 'dp.pemesanan_id', '=', 'p.pemesanan_id')
            ->join('barangs as b', 'dp.barang_id', '=', 'b.barang_id')
            ->select('dp.id', 'p.pemesanan_id', 'p.slug', 'b.nama_barang', 'dp.jumlah_pemesanan', 'b.barang_id')
            ->where('p.slug', $slug)
            ->get();

        DB::beginTransaction();
        try {
            $persediaan_masuk_id = PersediaanMasuk::generatePersediaanMasukId();
            $persediaan_masuk = new PersediaanMasuk;
            $persediaan_masuk->persediaan_masuk_id = $persediaan_masuk_id;
            $persediaan_masuk->slug = Str::random(16);
            $persediaan_masuk->pemesanan_id = $pemesanan->pemesanan_id;
            $persediaan_masuk->tanggal_persediaan_masuk = Carbon::now();
            $persediaan_masuk->save();
            foreach ($details as $detail) {
                $detail_persediaan = new DetailPersediaanMasuk;
                $detail_persediaan->persediaan_masuk_id = $persediaan_masuk_id;
                $detail_persediaan->barang_id = $detail->barang_id;
                $detail_persediaan->jumlah_persediaan_masuk = $detail->jumlah_pemesanan;
                $detail_persediaan->save();
                $barang = BarangGudang::where('barang_id', $detail->barang_id)->first();
                $barang->stok_masuk += $detail->jumlah_pemesanan;
                $barang->save();
            }
            $pemesanan->status_pemesanan = 'Selesai';
            $pemesanan->save();
            DB::commit();
            session()->forget("temporary_masuk");
            return redirect()->route('persediaan-masuk');
        } catch (\Exception $ex) {
            echo $ex->getMessage();
            DB::rollBack();
        }
    }
}

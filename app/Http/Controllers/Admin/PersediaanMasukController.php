<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\PersediaanMasuk;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PersediaanMasukController extends Controller
{
    public function jumlahHari($bulan_tahun)
    {
        # code...
        $jumlah = date('t', strtotime($bulan_tahun . "01"));
        return $jumlah;
    }

    public function index()
    {
        // $persediaan_masuk_id = PersediaanMasuk::generatePersediaanMasukId();
        // dd($persediaan_masuk_id);
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
        $subdate = Carbon::now()->subDay(7);
        $avg_date = DB::table('pemesanans as p')
            ->join('persediaan_masuks as pm', 'p.pemesanan_id', '=', 'pm.pemesanan_id')
            ->selectRaw('round(avg(DATEDIFF( pm.tanggal_persediaan_masuk, p.tanggal_pemesanan))) as lead_time')
            ->whereBetween('pm.tanggal_persediaan_masuk', [$subdate, $now])
            ->first();
        $lead_time = !empty($avg_date) ? $avg_date->lead_time : 2;
        $ss = ($data->max - $data->avg) * $lead_time;
        $jumlah_hari = $this->jumlahHari($bulan_tahun->bulan);
        $d = (int)round($data->total / $jumlah_hari);
        $rop = ($d * $lead_time) + $ss;
        dd($rop);
    }
}

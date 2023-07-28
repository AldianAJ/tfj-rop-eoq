<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\BarangCounter;
use App\Models\Admin\DetailPengirimanCounter;
use Illuminate\Http\Request;
use App\Models\Admin\PengirimanCounter;
use App\Models\Admin\PermintaanCounter;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class PengirimanCounterController extends Controller
{
    public function userAuth()
    {
        $user = Auth::guard('user')->user();
        return $user;
    }

    public function index()
    {
        $pengiriman_counter_id = PengirimanCounter::generatePengirimanCounterId('C00002');
        dd($pengiriman_counter_id);
    }

    public function show($slug)
    {
        $user = $this->userAuth();
        $permintaan = DB::table('permintaan_counters')
            ->where('slug', $slug)
            ->first();
        $data = DB::table('pengiriman_counters')
            ->where('permintaan_counter_id', $permintaan->permintaan_counter_id)
            ->first();

        $query = 'SELECT a.pengiriman_counter_id, a.slug, a.permintaan_counter_id, g.name, d.nama_barang, b.jumlah_pengiriman, b.persetujuan, b.catatan
            FROM pengiriman_counters AS a
            LEFT JOIN detail_pengiriman_counters AS b ON a.pengiriman_counter_id = b.pengiriman_counter_id
            LEFT JOIN permintaan_counters AS c on a.permintaan_counter_id = c.permintaan_counter_id
            LEFT JOIN barangs AS d ON b.barang_id = d.barang_id
            LEFT JOIN gudangs AS e ON b.gudang_id = e.gudang_id
            LEFT JOIN counters AS f ON b.counter_id = f.counter_id
            LEFT JOIN users AS g ON e.user_id=g.user_id OR f.user_id = g.user_id WHERE a.permintaan_counter_id = "' . $permintaan->permintaan_counter_id . '"';

        $pengirimans = DB::select($query);

        return view('pages.pengiriman.detail', compact('user', 'pengirimans', 'permintaan', 'data'));
    }

    public function storePenerimaan($slug)
    {
        $pengiriman = PengirimanCounter::where('slug', $slug)->first();
        $permintaan = PermintaanCounter::where('permintaan_counter_id', $pengiriman->permintaan_counter_id)->first();
        $details = DB::table('detail_pengiriman_counters')->where('pengiriman_counter_id', $pengiriman->pengiriman_counter_id)->get();
        DB::beginTransaction();
        try {
            $permintaan->status = 'Diterima/Selesai';
            $permintaan->save();
            $pengiriman->tanggal_penerimaan = Carbon::now();
            $pengiriman->save();
            foreach ($details as $detail) {
                if ($detail->persetujuan == 'Setuju') {
                    $barang_counter = BarangCounter::where(['barang_id' => $detail->barang_id, 'counter_id' => $permintaan->counter_id])->first();
                    $barang_counter->stok_masuk += $detail->jumlah_pengiriman;
                    $barang_counter->save();
                }
            }
            DB::commit();
        } catch (\Exception $ex) {
            //throw $th;
            echo $ex->getMessage();
            DB::rollBack();
        }
        return redirect()->route('permintaan-counter');
    }

    public function indexBarangDiambil(Request $request)
    {
        $user = $this->userAuth();
        if ($request->ajax()) {
            $counter = DB::table('counters')
                ->where('user_id', $user->user_id)
                ->first();

            $barang_diambil = DB::table('detail_pengiriman_counters as dp')
                ->join('pengiriman_counters as pg', 'dp.pengiriman_counter_id', '=', 'pg.pengiriman_counter_id')
                ->join('permintaan_counters as pm', 'pg.permintaan_counter_id', '=', 'pm.permintaan_counter_id')
                ->join('barangs as b', 'dp.barang_id', '=', 'b.barang_id')
                ->join('barang_counters as bc', 'b.barang_id', '=', 'bc.barang_id')
                ->join('counters as c', 'pm.counter_id', '=', 'c.counter_id')
                ->join('users as u', 'c.user_id', '=', 'u.user_id')
                ->selectRaw('DISTINCT pg.pengiriman_counter_id, pm.permintaan_counter_id, b.barang_id, b.nama_barang, pm.tanggal_permintaan, dp.jumlah_pengiriman, u.name, dp.status_pengiriman')
                ->where('dp.counter_id', $counter->counter_id)
                ->get();

            return DataTables::of($barang_diambil)->make(true);
        }
        return view('pages.history.barang-diambil', compact('user'));
    }

    public function updateStatus($pengiriman_counter_id, $barang_id)
    {
        DB::beginTransaction();
        try {
            $detail_pengiriman = DetailPengirimanCounter::where(['pengiriman_counter_id' => $pengiriman_counter_id, 'barang_id' => $barang_id])->first();
            $detail_pengiriman->status_pengiriman = 'Dikirim';
            $detail_pengiriman->save();

            $barang_diambil = DB::table('detail_pengiriman_counters as dp')
                ->join('pengiriman_counters as pg', 'dp.pengiriman_counter_id', '=', 'pg.pengiriman_counter_id')
                ->join('permintaan_counters as pm', 'pg.permintaan_counter_id', '=', 'pm.permintaan_counter_id')
                ->join('barangs as b', 'dp.barang_id', '=', 'b.barang_id')
                ->join('barang_counters as bc', 'b.barang_id', '=', 'bc.barang_id')
                ->join('counters as c', 'pm.counter_id', '=', 'c.counter_id')
                ->join('users as u', 'c.user_id', '=', 'u.user_id')
                ->selectRaw('DISTINCT pg.pengiriman_counter_id, pm.permintaan_counter_id, b.barang_id, b.nama_barang, pm.tanggal_permintaan, dp.jumlah_pengiriman, u.name, dp.status_pengiriman')
                ->where(['pg.pengiriman_counter_id' => $pengiriman_counter_id, 'dp.persetujuan' => 'setuju', 'dp.status_pengiriman' => 'Menunggu Dikirim'])
                ->get();
            // dd($barang_diambil);
            if (count($barang_diambil) < 1) {
                // dd($barang_diambil);
                $pengiriman = DB::table('pengiriman_counters')->where('pengiriman_counter_id', $pengiriman_counter_id)->first();
                $permintaan = PermintaanCounter::where('permintaan_counter_id', $pengiriman->permintaan_counter_id)->first();
                $permintaan->status = 'Dikirim';
                $permintaan->save();
            }
            DB::commit();
            return redirect()->route('pengiriman-counter.barangDiambil');
        } catch (\Exception $ex) {
            //throw $th;
            echo $ex->getMessage();
            DB::rollBack();
        }
    }

    public function indexHistory(Request $request)
    {
        $user = $this->userAuth();
        $path = "pengiriman-counter";
        if ($request->ajax()) {
            $pengirimans = DB::table('pengiriman_counters as pgc')
                ->join('permintaan_counters as pmc', 'pgc.permintaan_counter_id', '=', 'pmc.permintaan_counter_id')
                ->join('counters as c', 'pmc.counter_id', '=', 'c.counter_id')
                ->join('users as u', 'c.user_id', '=', 'u.user_id')
                ->selectRaw('pgc.pengiriman_counter_id, u.name, pmc.permintaan_counter_id, pmc.status, pgc.tanggal_pengiriman, pgc.tanggal_penerimaan, pgc.slug')
                ->where('pmc.status', 'Diterima/Selesai')
                ->orWhere('pmc.status', 'Ditolak')
                ->orderByRaw('pgc.tanggal_pengiriman desc, pgc.pengiriman_counter_id desc')
                ->get();

            return DataTables::of($pengirimans)
                ->addColumn('action', function ($object) use ($path) {
                    $html = ' <button class="btn btn-info waves-effect waves-light btn-detail" data-bs-toggle="modal" data-bs-target="#detailModal">'
                        . '  <i class="bx bx-detail font-size-18 align-middle me-2"></i>Detail</button>';
                    $html .= ' <a href="' . route($path . '.exportPDF', ["slug" => $object->slug]) . '" class="btn btn-primary waves-effect waves-light">'
                        . ' <i class="bx bxs-printer align-middle me-2 font-size-18"></i>Cetak PDF</a>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('pages.history.pengiriman-counter', compact('user'));
    }


    public function detailHistory(Request $request)
    {
        # code...
        $slug = $request->slug;
        $query = 'SELECT a.pengiriman_counter_id, a.slug, a.permintaan_counter_id, g.name, d.nama_barang, b.jumlah_pengiriman, b.persetujuan, b.catatan
            FROM pengiriman_counters AS a
            LEFT JOIN detail_pengiriman_counters AS b ON a.pengiriman_counter_id = b.pengiriman_counter_id
            LEFT JOIN permintaan_counters AS c on a.permintaan_counter_id = c.permintaan_counter_id
            LEFT JOIN barangs AS d ON b.barang_id = d.barang_id
            LEFT JOIN gudangs AS e ON b.gudang_id = e.gudang_id
            LEFT JOIN counters AS f ON b.counter_id = f.counter_id
            LEFT JOIN users AS g ON e.user_id=g.user_id OR f.user_id = g.user_id WHERE a.slug = "' . $slug . '"';

        $pengirimans = DB::select($query);
        return DataTables::of($pengirimans)->make(true);
    }

    public function exportPDF($slug)
    {
        $month = [
            "01" => "Januari",
            "02" => "Februari",
            "03" => "Maret",
            "04" => "April",
            "05" => "Mei",
            "06" => "Juni",
            "07" => "Juli",
            "08" => "Agustus",
            "09" => "September",
            "10" => "Oktober",
            "11" => "November",
            "12" => "Desember",
        ];
        $tanggal = Carbon::now()->format('d') . ' ' . $month[Carbon::now()->format('m')] . ' ' . Carbon::now()->format('Y');

        $pengiriman_counter = DB::table('pengiriman_counters')
            ->where('slug', $slug)
            ->first();
        $query = 'SELECT a.pengiriman_counter_id, a.slug, a.permintaan_counter_id, g.name, d.nama_barang, b.jumlah_pengiriman, b.persetujuan, b.catatan
        FROM pengiriman_counters AS a
        LEFT JOIN detail_pengiriman_counters AS b ON a.pengiriman_counter_id = b.pengiriman_counter_id
        LEFT JOIN permintaan_counters AS c on a.permintaan_counter_id = c.permintaan_counter_id
        LEFT JOIN barangs AS d ON b.barang_id = d.barang_id
        LEFT JOIN gudangs AS e ON b.gudang_id = e.gudang_id
        LEFT JOIN counters AS f ON b.counter_id = f.counter_id
        LEFT JOIN users AS g ON e.user_id=g.user_id OR f.user_id = g.user_id WHERE a.slug = "' . $slug . '"';
        $details = DB::select($query);

        $title = 'Laporan Pengiriman Counter ' . $pengiriman_counter->pengiriman_counter_id;
        // dd($title);
        $pdf = Pdf::loadView('pages.export.pengiriman-counter', compact('details', 'title', 'tanggal'));
        return $pdf->download($title . ".pdf");
    }
}

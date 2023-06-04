<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Pemesanan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use App\Models\Admin\DetailPemesanan;
use Illuminate\Support\Str;

class PemesananController extends Controller
{

    public function userAuth()
    {
        $user = Auth::guard('user')->user();
        return $user;
    }

    public function index(Request $request)
    {
        $user = $this->userAuth();
        $path = 'pemesanan';
        if ($request->ajax()) {
            $pemesanans = DB::table('pemesanans')
                ->where('status_pemesanan', 'Menunggu Persetujuan')
                ->orWhere('status_pemesanan', 'Disetujui')
                ->orderBy('tanggal_pemesanan', 'desc')
                ->get();

            return DataTables::of($pemesanans)
                ->addColumn('action', function ($object) use ($path, $user) {
                    if ($user->role == 'owner' && $object->status_pemesanan == 'Menunggu Persetujuan') {
                        $html = ' <a href="" class="btn btn-success waves-effect waves-light">'
                            . ' <i class="bx bx-transfer-alt align-middle me-2 font-size-18"></i>Persetujuan</a>';
                        return $html;
                    } elseif ($object->status_pemesanan == 'Disetujui') {
                        $html = ' <button type="button" class="btn btn-info waves-effect waves-light btn-detail" data-bs-toggle="modal" data-bs-target="#detailModal">
                        <i class="bx bx-detail font-size-18 align-middle me-2"></i> Detail</button>';
                        $html .= ' <a href="" class="btn btn-primary waves-effect waves-light">'
                            . ' <i class="bx bxs-package align-middle me-2 font-size-18"></i> Pesan</a>';
                        return $html;
                    } else {
                        $html = ' <button type="button" class="btn btn-info waves-effect waves-light btn-detail" data-bs-toggle="modal" data-bs-target="#detailModal">
                        <i class="bx bx-detail font-size-18 align-middle me-2"></i> Detail</button>';
                        return $html;
                    }
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('pages.pemesanan.index', compact('user'));
    }

    public function hitung()
    {
        $bulan_tahun = DB::table('penjualans')
            ->selectRaw('DATE_FORMAT(MAX(tanggal_penjualan),"%m-%Y") as bulan')
            ->whereRaw('DATE_FORMAT(tanggal_penjualan, "%m-%Y") < DATE_FORMAT(now(), "%m-%Y")')
            ->first();
        $barang_id = "B00002";
        $data = DB::table('detail_penjualans as dp')
            ->join('penjualans as p', 'dp.penjualan_id', '=', 'p.penjualan_id')
            ->join('barang_counters as bc', 'dp.barang_counter_id', '=', 'bc.barang_counter_id')
            ->join('barangs as b', 'bc.barang_id', '=', 'b.barang_id')
            ->selectRaw('max(dp.quantity) as max, round(avg(dp.quantity)) as avg, sum(dp.quantity) as total')
            ->whereRaw("b.barang_id = '" . $barang_id . "' AND DATE_FORMAT(p.tanggal_penjualan, '%m-%Y') = '" . $bulan_tahun->bulan . "'")->first();
        $barang = DB::table('barangs')->where('barang_id', $barang_id)->first();
        $s = 5000;
        $h = $barang->biaya_penyimpanan;
        $eoq = round(sqrt((2 * $data->total * $s) / $h));


        dd($eoq);
    }

    public function createNewPersediaan(Request $request)
    {

        // $query = "SELECT a.barang_id, a.slug,a.nama_barang, a.harga_barang, a.biaya_penyimpanan, a.rop,((SELECT SUM(stok_masuk)-SUM(stok_keluar) FROM barang_gudangs WHERE barang_id = a.barang_id GROUP BY barang_id) + (SELECT SUM(stok_masuk)-SUM(stok_keluar) FROM barang_counters WHERE barang_id = a.barang_id GROUP BY barang_id)) as qty_total
        //         FROM barangs as a
        //         JOIN barang_gudangs as b on a.barang_id = b.barang_id
        //         JOIN barang_counters as c on a.barang_id = c.barang_id
        //         WHERE ((SELECT SUM(stok_masuk)-SUM(stok_keluar) FROM barang_gudangs WHERE barang_id = a.barang_id GROUP BY barang_id) + (SELECT SUM(stok_masuk)-SUM(stok_keluar) FROM barang_counters WHERE barang_id = a.barang_id GROUP BY barang_id)) < 1
        //         GROUP BY a.barang_id, a.nama_barang, a.harga_barang, a.biaya_penyimpanan, a.rop ORDER BY a.barang_id ASC;";
        // $barangs = DB::select($query);
        // dd($barangs);


        if ($request->ajax()) {
            $query = "SELECT a.barang_id, a.slug,a.nama_barang, a.harga_barang, a.biaya_penyimpanan, a.rop,((SELECT SUM(stok_masuk)-SUM(stok_keluar) FROM barang_gudangs WHERE barang_id = a.barang_id GROUP BY barang_id) + (SELECT SUM(stok_masuk)-SUM(stok_keluar) FROM barang_counters WHERE barang_id = a.barang_id GROUP BY barang_id)) as qty_total
                FROM barangs as a
                JOIN barang_gudangs as b on a.barang_id = b.barang_id
                JOIN barang_counters as c on a.barang_id = c.barang_id
                WHERE ((SELECT SUM(stok_masuk)-SUM(stok_keluar) FROM barang_gudangs WHERE barang_id = a.barang_id GROUP BY barang_id) + (SELECT SUM(stok_masuk)-SUM(stok_keluar) FROM barang_counters WHERE barang_id = a.barang_id GROUP BY barang_id)) < 1
                GROUP BY a.barang_id, a.nama_barang, a.harga_barang, a.biaya_penyimpanan, a.rop ORDER BY a.barang_id ASC;";
            $barangs = DB::select($query);
            return DataTables::of($barangs)
                ->addColumn('action', function ($object) {
                    $html = '<button class="btn btn-success waves-effect waves-light btn-add" data-bs-toggle="modal"' .
                        'data-bs-target="#jumlahModal"><i class="bx bx-plus-circle align-middle font-size-18"></i></button>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('pages.pemesanan.create-new-persediaan', compact('user'));
    }

    public function storeNewPersediaan(Request $request)
    {
        # code...
    }

    public function create(Request $request)
    {
        $user = $this->userAuth();
        if ($request->ajax()) {
            $query = "SELECT a.barang_id, a.slug,a.nama_barang, a.harga_barang, a.biaya_penyimpanan, a.rop,((SELECT SUM(stok_masuk)-SUM(stok_keluar) FROM barang_gudangs WHERE barang_id = a.barang_id GROUP BY barang_id) + (SELECT SUM(stok_masuk)-SUM(stok_keluar) FROM barang_counters WHERE barang_id = a.barang_id GROUP BY barang_id)) as qty_total
            FROM barangs as a
            JOIN barang_gudangs as b on a.barang_id = b.barang_id
            JOIN barang_counters as c on a.barang_id = c.barang_id
            GROUP BY a.barang_id, a.nama_barang, a.harga_barang, a.biaya_penyimpanan, a.rop ORDER BY ((SELECT SUM(stok_masuk)-SUM(stok_keluar) FROM barang_gudangs WHERE barang_id = a.barang_id GROUP BY barang_id) + (SELECT SUM(stok_masuk)-SUM(stok_keluar) FROM barang_counters WHERE barang_id = a.barang_id GROUP BY barang_id)) <= a.rop desc, a.barang_id asc";
            $barangs = DB::select($query);
            return DataTables::of($barangs)
                ->addColumn('action', function ($object) {
                    $html = '<button class="btn btn-success waves-effect waves-light btn-add"' .
                        '><i class="bx bx-plus-circle align-middle font-size-18"></i></button>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.pemesanan.create', compact('user'));
    }

    public function hitungEOQ(Request $request)
    {
        $bulan_tahun = DB::table('penjualans')
            ->selectRaw('DATE_FORMAT(MAX(tanggal_penjualan),"%m-%Y") as bulan')
            ->whereRaw('DATE_FORMAT(tanggal_penjualan, "%m-%Y") < DATE_FORMAT(now(), "%m-%Y")')
            ->first();
        $hasil_hitung = [];
        $pemesanans = json_decode($request->pemesanan);
        $s = $request->biaya;
        $no = 1;

        foreach ($pemesanans as $pemesanan) {
            $data = DB::table('detail_penjualans as dp')
                ->join('penjualans as p', 'dp.penjualan_id', '=', 'p.penjualan_id')
                ->join('barang_counters as bc', 'dp.barang_counter_id', '=', 'bc.barang_counter_id')
                ->join('barangs as b', 'bc.barang_id', '=', 'b.barang_id')
                ->selectRaw('max(dp.quantity) as max, round(avg(dp.quantity)) as avg, sum(dp.quantity) as total')
                ->whereRaw("b.barang_id = '" . $pemesanan->id_barang . "' AND DATE_FORMAT(p.tanggal_penjualan, '%m-%Y') = '" . $bulan_tahun->bulan . "'")->first();
            $barang = DB::table('barangs')->where('barang_id', $pemesanan->id_barang)->first();

            $h = $barang->biaya_penyimpanan;
            $eoq = $data->total > 0 ? round(sqrt((2 * $data->total * $s) / $h)) : 0;
            $hasil_eoq = [
                'no' => $no++,
                'id_barang' => $pemesanan->id_barang,
                'nama_barang' => $pemesanan->nama_barang,
                'eoq' => $eoq,
                'jumlah' => 0
            ];

            array_push($hasil_hitung, $hasil_eoq);
        }
        return response()->json(['pemesanan' => $hasil_hitung], 200);
    }

    public function store(Request $request)
    {
        $details = json_decode($request->pemesanan);
        $biaya_pemesanan = $request->biaya;

        DB::beginTransaction();
        try {
            $pemesanan_id = Pemesanan::generatePemesananId();
            $pemesanan = new Pemesanan;
            $pemesanan->pemesanan_id = $pemesanan_id;
            $pemesanan->slug = Str::random(16);
            $pemesanan->status_pemesanan = 'Menunggu Persetujuan';
            $pemesanan->tanggal_pemesanan = Carbon::now();
            $pemesanan->biaya_pemesanan = $biaya_pemesanan;
            $pemesanan->save();
            foreach ($details as $detail) {
                $detail_pemesanan = new DetailPemesanan;
                $detail_pemesanan->pemesanan_id = $pemesanan_id;
                $detail_pemesanan->barang_id = $detail->id_barang;
                $detail_pemesanan->eoq = $detail->eoq;
                $detail_pemesanan->jumlah_pemesanan = $detail->jumlah;
                $detail_pemesanan->save();
            }
            DB::commit();
            return response()->json(200);
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json($ex->getMessage(), 400);
        }
    }
}

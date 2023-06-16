<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\BarangCounter;
use App\Models\Admin\BarangGudang;
use App\Models\Admin\DetailPengirimanCounter;
use App\Models\Admin\DetailPermintaanCounter;
use App\Models\Admin\PengirimanCounter;
use Illuminate\Http\Request;
use App\Models\Admin\PermintaanCounter;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;

class PermintaanCounterController extends Controller
{

    public function userAuth()
    {
        $user = Auth::guard('user')->user();
        return $user;
    }

    public function index(Request $request)
    {
        $user = $this->userAuth();
        $path = 'permintaan-counter';
        if ($request->ajax()) {
            if ($user->role == 'counter') {
                $counters = DB::table('counters')->where('user_id', $user->user_id)->first();
                $counter_id = $counters->counter_id;
                $permintaans = DB::table('permintaan_counters as a')
                    ->join('counters as b', 'a.counter_id', '=', 'b.counter_id')
                    ->join('users as c', 'c.user_id', '=', 'b.user_id')
                    ->select('a.permintaan_counter_id as permintaan_id', 'c.name', 'a.status', 'a.tanggal_permintaan', 'a.slug')
                    ->where('a.counter_id', $counter_id)
                    ->where(function ($q) {
                        $q->where('a.status', 'Pending')
                            ->orWhere('a.status', 'Dikirim');
                    })
                    ->orderByDesc('tanggal_permintaan')
                    ->get();

                return DataTables::of($permintaans)
                    ->addColumn('action', function ($object) use ($path) {
                        $html = ' <button class="btn btn-success waves-effect waves-light btn-detail"' . ($object->status == 'Pending' ? ' data-bs-toggle="modal" data-bs-target="#detailModal ' : '') . ' ">'
                            . ' <i class="bx bx-detail align-middle me-2 font-size-18"></i>Detail</button>';
                        return $html;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            } elseif ($user->role == 'gudang') {
                $permintaans = DB::table('permintaan_counters as a')
                    ->join('counters as b', 'a.counter_id', '=', 'b.counter_id')
                    ->join('users as c', 'c.user_id', '=', 'b.user_id')
                    ->select('a.permintaan_counter_id as permintaan_id', 'c.name', 'a.status', 'a.tanggal_permintaan', 'a.slug')
                    ->where(function ($q) {
                        $q->where('a.status', 'Pending')
                            ->orWhere('a.status', 'Dikirim');
                    })
                    ->orderByDesc('tanggal_permintaan')
                    ->get();
                $path = 'permintaan-counter';
                return DataTables::of($permintaans)
                    ->addColumn('action', function ($object) use ($path) {
                        // ' . route($path . ".confirm", ["slug" => $object->slug]) . '
                        $html = '';
                        if ($object->status == 'Pending') {
                            $html = ' <a href="' . route($path . '.detailByGudang', ["slug" => $object->slug]) . '" class="btn btn-success waves-effect waves-light">'
                                . ' <i class="bx bx-transfer-alt align-middle me-2 font-size-18"></i>Proses</a>';
                        } elseif ($object->status == 'Dikirim') {
                            $html = ' <a href="' . route('pengiriman-counter.detail', ["slug" => $object->slug]) . '" class="btn btn-success waves-effect waves-light">'
                                . ' <i class="bx bx-detail font-size-18 align-middle me-2"></i>Detail</a>';
                        }

                        return $html;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
        }
        return view('pages.permintaan.index', compact('user'));
    }

    public function create(Request $request)
    {
        $barangs = DB::table('barangs')->get();
        $user = $this->userAuth();
        if ($request->ajax()) {
            $barangs = DB::table('barangs')->get();
            return DataTables::of($barangs)
                ->addColumn('action', function ($object) {
                    $html = '<button class="btn btn-success waves-effect waves-light btn-add" data-bs-toggle="modal"' .
                        'data-bs-target="#jumlahModal"><i class="bx bx-plus-circle align-middle font-size-18"></i></button>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.permintaan.create', compact('user'));
    }

    public function store(Request $request)
    {
        $user = $this->userAuth();
        $counters = DB::table('counters')->where('user_id', $user->user_id)->first();
        $counter_id = $counters->counter_id;
        $permintaan_id = PermintaanCounter::generatePermintaanCounterId($counter_id);
        $list_permintaans = json_decode($request->list_permintaans);
        DB::beginTransaction();
        try {
            $permintaan = new PermintaanCounter;
            $permintaan->permintaan_counter_id = $permintaan_id;
            $permintaan->slug = Str::random(16);
            $permintaan->counter_id = $counter_id;
            $permintaan->status = 'Pending';
            $permintaan->tanggal_permintaan = Carbon::now();
            $permintaan->save();
            foreach ($list_permintaans as $list) {
                $detail_permintaan = new DetailPermintaanCounter;
                $detail_permintaan->permintaan_counter_id = $permintaan_id;
                $detail_permintaan->barang_id = $list->id_barang;
                $detail_permintaan->jumlah_permintaan = $list->jumlah;
                $detail_permintaan->save();
            }
            DB::commit();
            return response()->json([], 200);
        } catch (\Exception $ex) {
            //throw $th;
            echo $ex->getMessage();
            DB::rollBack();
            return response()->json([], 400);
        }
    }

    public function detail(Request $request)
    {
        $slug = $request->slug;
        $details = DB::table('permintaan_counters as a')
            ->join('detail_permintaan_counters as b', 'a.permintaan_counter_id', '=', 'b.permintaan_counter_id')
            ->join('barangs as c', 'b.barang_id', '=', 'c.barang_id')
            ->select('c.nama_barang', 'jumlah_permintaan as quantity')
            ->where('a.slug', $slug)
            ->get();

        return DataTables::of($details)->make(true);
        // dd($details);
    }

    public function detailByGudang($slug)
    {
        $user = $this->userAuth();

        $temporary_persetujuan = session("temporary_persetujuan");
        $details = DB::table('permintaan_counters as a')
            ->join('detail_permintaan_counters as b', 'a.permintaan_counter_id', '=', 'b.permintaan_counter_id')
            ->join('barangs as c', 'b.barang_id', '=', 'c.barang_id')
            ->select('a.permintaan_counter_id', 'c.barang_id', 'b.id', 'a.slug', 'c.nama_barang as nama', 'jumlah_permintaan as quantity')
            ->where('a.slug', $slug)
            ->get();
        // dd($details);
        $permintaans = DB::table('permintaan_counters')->where('slug', $slug)->first();
        // session()->forget("temporary_persetujuan");
        $count_detail = count($details);
        $count_tmp = (array)$temporary_persetujuan;
        $count_tmp = count($count_tmp);
        // dd(session("temporary_persetujuan"));

        return view('pages.permintaan.detail', compact('details', 'user', 'permintaans', 'temporary_persetujuan', 'count_detail', 'count_tmp'));
    }

    public function createPersetujuan($slug, $id)
    {
        $user = $this->userAuth();
        $permintaan = DB::table('permintaan_counters as a')
            ->join('detail_permintaan_counters as b', 'a.permintaan_counter_id', '=', 'b.permintaan_counter_id')
            ->join('barangs as c', 'b.barang_id', '=', 'c.barang_id')
            ->select('b.id', 'a.slug', 'c.barang_id', 'c.nama_barang', 'jumlah_permintaan', 'a.counter_id')
            ->where(['a.slug' => $slug, 'b.id' => $id])
            ->first();

        $barang_counter = DB::table('barang_counters as a')
            ->join('counters as b', 'a.counter_id', '=', 'b.counter_id')
            ->join('users as c', 'c.user_id', '=', 'b.user_id')
            ->where(['a.counter_id' => $permintaan->counter_id, 'a.barang_id' => $permintaan->barang_id])
            ->first();

        $gudang = DB::table('users')
            ->where('role', 'gudang')
            ->first();

        $query = 'SELECT DISTINCT u.name as nama,CASE u.name 
        WHEN "' . $gudang->name . '" THEN
        (bg.stok_masuk - bg.stok_keluar)
        ELSE
        (bc.stok_masuk - bc.stok_keluar)
        END as quantity,
        CASE u.name
        WHEN "' . $gudang->name . '" THEN
        g.gudang_id
        ELSE
        c.counter_id
        END as sumber_id
        ,b.nama_barang
        FROM barangs as b
        JOIN barang_gudangs as bg on b.barang_id = bg.barang_id
        JOIN barang_counters as bc on b.barang_id = bc.barang_id
        JOIN gudangs as g on bg.gudang_id = g.gudang_id
        JOIN counters as c on bc.counter_id = c.counter_id
        JOIN users as u on g.user_id = u.user_id OR c.user_id = u.user_id WHERE b.barang_id = "' . $permintaan->barang_id . '" AND (bg.stok_masuk - bg.stok_keluar >= "' . $permintaan->jumlah_permintaan . '" or bc.stok_masuk - bc.stok_keluar >= "' . $permintaan->jumlah_permintaan . '") and c.counter_id <> "' . $permintaan->counter_id . '"
        GROUP BY nama, quantity, sumber_id, nama_barang
        ';
        $sumbers = DB::select($query);

        // dd($sumbers);
        return view('pages.permintaan.persetujuan', compact('barang_counter', 'permintaan', 'user', 'sumbers'));
    }

    public function temporaryPersetujuan(Request $request)
    {
        $persetujuan = $request->persetujuan;
        $slug = $request->slug;
        if ($persetujuan == 'Setuju' && !empty($request->sumber) && !empty($request->barang_id)) {
            $permintaan = DB::table('permintaan_counters')
                ->where('slug', $slug)
                ->first();

            $permintaan_id = $permintaan->permintaan_counter_id;
            $counter_id = $permintaan->counter_id;
            $barang_id = $request->barang_id;
            $jumlah_pengiriman = $request->jumlah_pengiriman;
            $id_sumber = $request->sumber;
            $catatan = "-";
            $kode_session = $permintaan_id . '/' . $barang_id;

            $temporary_persetujuan = session("temporary_persetujuan");
            $temporary_persetujuan[$kode_session] = [
                "permintaan_id" => $permintaan_id,
                "counter_id" => $counter_id,
                "barang_id" => $barang_id,
                "persetujuan" => $persetujuan,
                "jumlah_pengiriman" => $jumlah_pengiriman,
                "id_sumber" => $id_sumber,
                "catatan" => $catatan
            ];

            session(["temporary_persetujuan" => $temporary_persetujuan]);

            // session()->flush();
            return redirect()->route('permintaan-counter.detailByGudang', ["slug" => $slug]);
        } else if ($persetujuan == 'Tidak Setuju' && !empty($request->catatan)) {
            $permintaan = DB::table('permintaan_counters')
                ->where('slug', $slug)
                ->first();

            $permintaan_id = $permintaan->permintaan_counter_id;
            $counter_id = $permintaan->counter_id;
            $barang_id = $request->barang_id;
            $jumlah_pengiriman = $request->jumlah_pengiriman;
            $catatan = $request->catatan;
            $kode_session = $permintaan_id . '/' . $barang_id;

            $temporary_persetujuan = session("temporary_persetujuan");
            $temporary_persetujuan[$kode_session] = [
                "permintaan_id" => $permintaan_id,
                "counter_id" => $counter_id,
                "barang_id" => $barang_id,
                "persetujuan" => $persetujuan,
                "catatan" => $catatan
            ];

            session(["temporary_persetujuan" => $temporary_persetujuan]);
            return redirect()->route('permintaan-counter.detailByGudang', ["slug" => $slug]);
        } else {
            return redirect()->back()->with("msg", "Mohon selesaikan persetujuan dengan benar");
        }
    }

    public function storePengiriman($slug)
    {
        $temporary_persetujuan = session("temporary_persetujuan");
        $permintaan = DB::table('permintaan_counters')
            ->where('slug', $slug)
            ->first();
        $count_ditolak = 0;
        $count_tmp = count($temporary_persetujuan);

        foreach ($temporary_persetujuan as $temp) {
            if ($temp['persetujuan'] == 'Tidak Setuju') {
                $count_ditolak++;
            }
        }

        $pengiriman_id = PengirimanCounter::generatePengirimanCounterId($permintaan->counter_id);
        $tanggal_pengiriman = Carbon::now();
        DB::beginTransaction();
        try {
            $pengiriman = new PengirimanCounter;
            $pengiriman->pengiriman_counter_id = $pengiriman_id;
            $pengiriman->slug = Str::random(16);
            $pengiriman->permintaan_counter_id = $permintaan->permintaan_counter_id;
            $pengiriman->tanggal_pengiriman = $tanggal_pengiriman;
            $pengiriman->save();
            foreach ($temporary_persetujuan as $temp) {
                if ($temp['persetujuan'] == 'Setuju') {
                    if ($temp['id_sumber'] == 'G00001') {
                        $detail = new DetailPengirimanCounter;
                        $detail->pengiriman_counter_id = $pengiriman_id;
                        $detail->barang_id = $temp['barang_id'];
                        $detail->persetujuan = $temp['persetujuan'];
                        $detail->jumlah_pengiriman = $temp['jumlah_pengiriman'];
                        $detail->gudang_id = $temp['id_sumber'];
                        $detail->catatan = $temp['catatan'];
                        $detail->save();
                        $barang_gudang = BarangGudang::where('barang_id', $temp['barang_id'])->first();
                        $barang_gudang->stok_keluar += $temp['jumlah_pengiriman'];
                        $barang_gudang->save();
                    } else {
                        $detail = new DetailPengirimanCounter;
                        $detail->pengiriman_counter_id = $pengiriman_id;
                        $detail->barang_id = $temp['barang_id'];
                        $detail->persetujuan = $temp['persetujuan'];
                        $detail->jumlah_pengiriman = $temp['jumlah_pengiriman'];
                        $detail->counter_id = $temp['id_sumber'];
                        $detail->catatan = $temp['catatan'];
                        $detail->save();
                        $barang_counter = BarangCounter::where(['barang_id' => $temp['barang_id'], 'counter_id' => $temp['id_sumber']])->first();
                        $barang_counter->stok_keluar += $temp['jumlah_pengiriman'];
                        $barang_counter->save();
                    }
                } else {
                    $detail = new DetailPengirimanCounter;
                    $detail->pengiriman_counter_id = $pengiriman_id;
                    $detail->barang_id = $temp['barang_id'];
                    $detail->persetujuan = $temp['persetujuan'];
                    $detail->catatan = $temp['catatan'];
                    $detail->save();
                }

                if ($count_ditolak == $count_tmp) {
                    $update_permintaan = PermintaanCounter::where('permintaan_counter_id', $permintaan->permintaan_counter_id)->first();
                    $update_permintaan->status = 'Ditolak';
                    $update_permintaan->save();
                } else {
                    $update_permintaan = PermintaanCounter::where('permintaan_counter_id', $permintaan->permintaan_counter_id)->first();
                    $update_permintaan->status = 'Dikirim';
                    $update_permintaan->save();
                }
            }
            DB::commit();
        } catch (\Exception $ex) {
            //throw $th;
            echo $ex->getMessage();
            DB::rollBack();
        }
        session()->forget("temporary_persetujuan");
        return redirect()->route('permintaan-counter');
    }

    public function indexHistory(Request $request)
    {
        $user = $this->userAuth();
        $path = "permintaans";
        if ($request->ajax()) {
            if ($user->role == 'gudang' || $user->role == 'owner') {
                $permintaans = DB::table('permintaan_counters as p')
                    ->join('counters as c', 'p.counter_id', '=', 'c.counter_id')
                    ->join('users as u', 'c.user_id', '=', 'u.user_id')
                    ->selectRaw('p.permintaan_counter_id, u.name, p.tanggal_permintaan, p.slug, p.status')
                    ->where('p.status', 'Diterima/Selesai')
                    ->orderBy('p.tanggal_permintaan', 'desc')
                    ->get();
                return DataTables::of($permintaans)
                    ->addColumn('action', function ($object) use ($path) {
                        $html = ' <button class="btn btn-info waves-effect waves-light btn-detail" data-bs-toggle="modal" data-bs-target="#detailModal">'
                            . '  <i class="bx bx-detail font-size-18 align-middle me-2"></i>Detail</button>';
                        $html .= ' <a href="" class="btn btn-primary waves-effect waves-light">'
                            . ' <i class="bx bxs-printer align-middle me-2 font-size-18"></i>Cetak PDF</a>';
                        return $html;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            } else {
                $counter = DB::table('counters')
                    ->where('user_id', $user->user_id)
                    ->first();
                $permintaans = DB::table('permintaan_counters as p')
                    ->join('counters as c', 'p.counter_id', '=', 'c.counter_id')
                    ->join('users as u', 'c.user_id', '=', 'u.user_id')
                    ->selectRaw('p.permintaan_counter_id, u.name, p.tanggal_permintaan, p.slug, p.status')
                    ->where('c.counter_id', $counter->counter_id)
                    ->orderBy('p.tanggal_permintaan', 'desc')
                    ->get();
                return DataTables::of($permintaans)
                    ->addColumn('action', function ($object) use ($path) {
                        $html = ' <button class="btn btn-info waves-effect waves-light btn-detail" data-bs-toggle="modal" data-bs-target="#detailModal">'
                            . '  <i class="bx bx-detail font-size-18 align-middle me-2"></i>Detail</button>';
                        $html .= ' <a href="" class="btn btn-primary waves-effect waves-light">'
                            . ' <i class="bx bxs-printer align-middle me-2 font-size-18"></i>Cetak PDF</a>';
                        return $html;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
        }

        return view('pages.history.permintaan-counter', compact('user'));
    }

    // public function detailHistory(Request $request)
    // {
    //     $permintaan = DB::table('detail_permintaan_counters')
    //     ->join('')
    // }
}

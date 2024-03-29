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
use Barryvdh\DomPDF\Facade\Pdf;

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

                if ($counters) { // Check if counters record exists
                    $counter_id = $counters->counter_id;
                    $permintaans = DB::table('permintaan_counters as a')
                        ->join('counters as b', 'a.counter_id', '=', 'b.counter_id')
                        ->join('users as c', 'c.user_id', '=', 'b.user_id')
                        ->select('a.permintaan_counter_id as permintaan_id', 'c.name', 'a.status_permintaan', 'a.tanggal_permintaan', 'a.slug')
                        ->where('a.counter_id', $counter_id)
                        ->whereIn('a.status_permintaan', ['Processing', 'Delivered'])
                        ->orderByDesc('tanggal_permintaan')
                        ->get();

                    return DataTables::of($permintaans)
                        ->addColumn('action', function ($object) {
                            $html = '<button class="btn btn-success waves-effect waves-light btn-detail"';
                            if ($object->status == 'Processing') {
                                $html .= ' data-bs-toggle="modal" data-bs-target="#detailModal"';
                            }
                            $html .= '><i class="bx bx-detail align-middle me-2 font-size-18"></i>Detail</button>';
                            return $html;
                        })
                        ->rawColumns(['action'])
                        ->make(true);
                }
            } elseif ($user->role == 'gudang') {
                $permintaans = DB::table('permintaan_counters as a')
                    ->join('counters as b', 'a.counter_id', '=', 'b.counter_id')
                    ->join('users as c', 'c.user_id', '=', 'b.user_id')
                    ->select('a.permintaan_counter_id as permintaan_id', 'c.name', 'a.status_permintaan', 'a.tanggal_permintaan', 'a.slug')
                    ->whereIn('a.status_permintaan', ['Processing', 'Delivered'])
                    ->orderByDesc('tanggal_permintaan')
                    ->get();

                return DataTables::of($permintaans)
                    ->addColumn('action', function ($object) use ($path) {
                        $html = '';
                        if ($object->status == 'Processing') {
                            $html = '<a href="' . route($path . '.detailByGudang', ["slug" => $object->slug]) . '" class="btn btn-success waves-effect waves-light">';
                            $html .= '<i class="bx bx-transfer-alt align-middle me-2 font-size-18"></i>Proses</a>';
                        } elseif ($object->status == 'Delivered') {
                            $html = '<a href="' . route('pengiriman-counter.detail', ["slug" => $object->slug]) . '" class="btn btn-success waves-effect waves-light">';
                            $html .= '<i class="bx bx-detail font-size-18 align-middle me-2"></i>Detail</a>';
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
            $permintaan->status = 'Processing';
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
            ->select('c.nama_barang', 'b.jumlah_permintaan as quantity')
            ->where('a.slug', $slug)
            ->get();

        return DataTables::of($details)->toJson();
    }

    public function detailByGudang($slug)
    {
        $user = $this->userAuth();

        $temporary_persetujuan = session("temporary_persetujuan", []);
        $details = DB::table('permintaan_counters as a')
            ->join('detail_permintaan_counters as b', 'a.permintaan_counter_id', '=', 'b.permintaan_counter_id')
            ->join('barangs as c', 'b.barang_id', '=', 'c.barang_id')
            ->select('a.permintaan_counter_id', 'c.barang_id', 'b.id', 'a.slug', 'c.nama_barang as nama', 'b.jumlah_permintaan as quantity')
            ->where('a.slug', $slug)
            ->get();

        $permintaans = DB::table('permintaan_counters')->where('slug', $slug)->first();
        $count_detail = $details->count();
        $count_tmp = count($temporary_persetujuan);

        return view('pages.permintaan.detail', compact('details', 'user', 'permintaans', 'temporary_persetujuan', 'count_detail', 'count_tmp'));
    }

    public function createPersetujuan($slug, $id)
    {
        $user = $this->userAuth();
        $permintaan = DB::table('permintaan_counters as a')
            ->join('detail_permintaan_counters as b', 'a.permintaan_counter_id', '=', 'b.permintaan_counter_id')
            ->join('barangs as c', 'b.barang_id', '=', 'c.barang_id')
            ->select('b.id', 'a.slug', 'c.barang_id', 'c.nama_barang', 'b.jumlah_permintaan', 'a.counter_id')
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

        $query = DB::table('barangs as b')
            ->select('u.name as nama')
            ->selectRaw('CASE u.name
                            WHEN ? THEN (bg.stok_masuk - bg.stok_keluar)
                            ELSE (bc.stok_masuk - bc.stok_keluar)
                        END as quantity', [$gudang->name])
            ->selectRaw('CASE u.name
                            WHEN ? THEN g.gudang_id
                            ELSE c.counter_id
                        END as sumber_id', [$gudang->name])
            ->select('b.nama_barang')
            ->join('barang_gudangs as bg', 'b.barang_id', '=', 'bg.barang_id')
            ->join('barang_counters as bc', 'b.barang_id', '=', 'bc.barang_id')
            ->join('gudangs as g', 'bg.gudang_id', '=', 'g.gudang_id')
            ->join('counters as c', 'bc.counter_id', '=', 'c.counter_id')
            ->join('users as u', function ($join) {
                $join->on('g.user_id', '=', 'u.user_id')
                    ->orWhere('c.user_id', '=', 'u.user_id');
            })
            ->where('b.barang_id', $permintaan->barang_id)
            ->where(function ($query) use ($permintaan) {
                $query->whereRaw('(bg.stok_masuk - bg.stok_keluar) >= ?', [$permintaan->jumlah_permintaan])
                    ->orWhereRaw('(bc.stok_masuk - bc.stok_keluar) >= ?', [$permintaan->jumlah_permintaan]);
            })
            ->where('c.counter_id', '<>', $permintaan->counter_id)
            ->groupBy('nama', 'quantity', 'sumber_id', 'nama_barang')
            ->get();


        $sumbers = DB::select($query);

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

            $temporary_persetujuan = session("temporary_persetujuan", []);

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

            return redirect()->route('permintaan-counter.detailByGudang', ["slug" => $slug]);
        } elseif ($persetujuan == 'Tidak Setuju' && !empty($request->catatan)) {
            $permintaan = DB::table('permintaan_counters')
                ->where('slug', $slug)
                ->first();

            $permintaan_id = $permintaan->permintaan_counter_id;
            $counter_id = $permintaan->counter_id;
            $barang_id = $request->barang_id;
            $jumlah_pengiriman = $request->jumlah_pengiriman;
            $catatan = $request->catatan;
            $kode_session = $permintaan_id . '/' . $barang_id;

            $temporary_persetujuan = session("temporary_persetujuan", []);

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
                $detail = new DetailPengirimanCounter;
                $detail->pengiriman_counter_id = $pengiriman_id;
                $detail->barang_id = $temp['barang_id'];
                $detail->persetujuan = $temp['persetujuan'];
                $detail->jumlah_pengiriman = $temp['jumlah_pengiriman'];
                $detail->catatan = $temp['catatan'];

                if ($temp['persetujuan'] == 'Setuju') {
                    if ($temp['id_sumber'] == 'G00001') {
                        $detail->gudang_id = $temp['id_sumber'];
                        $detail->status_pengiriman = 'Delivered';
                        $barang_gudang = BarangGudang::where('barang_id', $temp['barang_id'])->first();
                        $barang_gudang->stok_keluar += $temp['jumlah_pengiriman'];
                        $barang_gudang->save();
                    } else {
                        $detail->counter_id = $temp['id_sumber'];
                        $detail->status_pengiriman = 'Waiting Delivered';
                        $barang_counter = BarangCounter::where(['barang_id' => $temp['barang_id'], 'counter_id' => $temp['id_sumber']])->first();
                        $barang_counter->stok_keluar += $temp['jumlah_pengiriman'];
                        $barang_counter->save();
                    }
                } else {
                    $count_ditolak++;
                }
                $detail->save();
            }

            $update_permintaan = PermintaanCounter::where('permintaan_counter_id', $permintaan->permintaan_counter_id)->first();
            if ($count_ditolak == $count_tmp) {
                $update_permintaan->status = 'Cancelled';
            } else {
                $update_permintaan->status = 'Delivered';
            }
            $update_permintaan->save();
            DB::commit();
        } catch (\Exception $ex) {
            echo $ex->getMessage();
            DB::rollBack();
        }

        session()->forget("temporary_persetujuan");
        return redirect()->route('permintaan-counter');
    }
    public function indexHistory(Request $request)
    {
        $user = $this->userAuth();
        $path = "permintaan-counter";

        if ($request->ajax()) {
            $query = DB::table('permintaan_counters as p')
                ->join('counters as c', 'p.counter_id', '=', 'c.counter_id')
                ->join('users as u', 'c.user_id', '=', 'u.user_id')
                ->select('p.permintaan_counter_id', 'u.name', 'p.tanggal_permintaan', 'p.slug', 'p.status_permintaan')
                ->orderBy('p.tanggal_permintaan', 'desc');

            if ($user->role == 'gudang' || $user->role == 'owner') {
                $query->where('p.status_permintaan', 'Diterima/Selesai');
            } else {
                $counter = DB::table('counters')
                    ->where('user_id', $user->user_id)
                    ->first();

                $query->where('p.counter_id', $counter->counter_id);
            }

            $permintaans = $query->get();

            return DataTables::of($permintaans)
                ->addColumn('action', function ($object) use ($path) {
                    $html = '<button class="btn btn-info waves-effect waves-light btn-detail" data-bs-toggle="modal" data-bs-target="#detailModal">'
                        . '<i class="bx bx-detail font-size-18 align-middle me-2"></i>Detail</button>';
                    $html .= '<a href="' . route($path . '.exportPDF', ["slug" => $object->slug]) . '" class="btn btn-primary waves-effect waves-light">'
                        . '<i class="bx bxs-printer align-middle me-2 font-size-18"></i>Cetak PDF</a>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.history.permintaan-counter', compact('user'));
    }


    public function checkMinta()
    {
        $user = $this->userAuth();

        $checkMinta = DB::table('permintaan_counters')
            ->join('counters', 'counters.counter_id', '=', 'permintaan_counters.counter_id')
            ->where('counters.user_id', $user->user_id)
            ->where(function ($q) {
                $q->where('permintaan_counters.status', 'Processing')
                    ->orWhere('permintaan_counters.status', 'Delivered');
            })
            ->get();

        $result = [
            'checkMinta' => $checkMinta
        ];

        $result = (object) $result;

        return response()->json($result, 200);
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

        $permintaan = DB::table('permintaan_counters as p')
            ->join('counters as c', 'p.counter_id', '=', 'c.counter_id')
            ->join('users as u', 'c.user_id', '=', 'u.user_id')
            ->selectRaw('DATE_FORMAT(p.tanggal_permintaan, "%d-%m-%Y") as tanggal_permintaan, u.name')
            ->where('p.slug', $slug)
            ->first();

        $details = DB::table('permintaan_counters as a')
            ->join('detail_permintaan_counters as b', 'a.permintaan_counter_id', '=', 'b.permintaan_counter_id')
            ->join('barangs as c', 'b.barang_id', '=', 'c.barang_id')
            ->select('c.nama_barang', 'b.jumlah_permintaan as quantity')
            ->where('a.slug', $slug)
            ->get();

        $day = substr($permintaan->tanggal_permintaan, 0, 2);
        $months = substr($permintaan->tanggal_permintaan, 3, 2);
        $year = substr($permintaan->tanggal_permintaan, 6, 4);
        $counter = $permintaan->name;

        $title = 'Laporan Permintaan ' . $counter . ' Tanggal ' . $day . ' ' . $month[$months] . ' ' . $year;

        $pdf = PDF::loadView('pages.export.permintaan-counter', compact('details', 'title', 'tanggal', 'permintaan'));
        return $pdf->download($title . ".pdf");
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\DetailPermintaanCounter;
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
        if ($request->ajax()) {
            if ($user->role == 'counter') {
                $counters = DB::table('counters')->where('user_id', $user->user_id)->first();
                $counter_id = $counters->counter_id;
                $permintaans = DB::table('permintaan_counters as a')
                    ->join('counters as b', 'a.counter_id', '=', 'b.counter_id')
                    ->join('users as c', 'c.user_id', '=', 'b.user_id')
                    ->select('a.permintaan_counter_id as permintaan_id', 'c.name', 'a.status', 'a.tanggal_permintaan', 'a.slug')
                    ->where('a.counter_id', $counter_id)
                    ->get();
                $path = 'permintaan-counter';
                return DataTables::of($permintaans)
                    ->addColumn('action', function ($object) use ($path) {
                        // ' . route($path . ".detail", ["slug" => $object->slug]) . '
                        $html = ' <a href="" class="btn btn-success waves-effect waves-light">'
                            . ' <i class="bx bx-detail align-middle me-2 font-size-18"></i>Detail</a>';
                        return $html;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            } elseif ($user->role == 'gudang') {
                $permintaans = DB::table('permintaan_counters as a')
                    ->join('counters as b', 'a.counter_id', '=', 'b.counter_id')
                    ->join('users as c', 'c.user_id', '=', 'b.user_id')
                    ->select('a.permintaan_counter_id as permintaan_id', 'c.name', 'a.status', 'a.tanggal_permintaan', 'a.slug')
                    ->get();
                $path = 'permintaan-counter';
                return DataTables::of($permintaans)
                    ->addColumn('action', function ($object) use ($path) {
                        // ' . route($path . ".confirm", ["slug" => $object->slug]) . '
                        $html = ' <a href="" class="btn btn-success waves-effect waves-light">'
                            . ' <i class="bx bx-transfer-alt align-middle me-2 font-size-18"></i>Proses</a>';
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
        }
    }
}

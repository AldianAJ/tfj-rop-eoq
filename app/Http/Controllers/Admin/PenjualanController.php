<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Penjualan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class PenjualanController extends Controller
{

    public function userAuth()
    {
        $user = Auth::guard('user')->user();
        return $user;
    }

    public function index(Request $request)
    {
        $user = $this->userAuth();
        $path = 'penjualan';
        if ($request->ajax() && empty($request->type)) {
            if ($user->role == 'gudang' || $user->role == 'owner') {
                $penjualan = DB::table('penjualans as p')
                    ->join('counters as c', 'p.counter_id', '=', 'c.counter_id')
                    ->join('users as u', 'c.user_id', '=', 'u.user_id')
                    ->select('p.penjualan_id', 'p.slug', 'u.name', 'p.grand_total', 'p.tanggal_penjualan')
                    ->orderByDesc('p.tanggal_penjualan')
                    ->get();
                return DataTables::of($penjualan)->addColumn('action', function ($object) use ($path) {
                    $html = ' <a href="" class="btn btn-info waves-effect waves-light">'
                        . '  <i class="bx bx-detail font-size-18 align-middle me-2"></i>Detail</a>';
                    return $html;
                })
                    ->rawColumns(['action'])
                    ->make(true);
            } elseif ($user->role == 'counter') {
                $counter = DB::table('counters')->where('user_id', $user->user_id)->first();
                $penjualan = DB::table('penjualans as p')
                    ->join('counters as c', 'p.counter_id', '=', 'c.counter_id')
                    ->join('users as u', 'c.user_id', '=', 'u.user_id')
                    ->select('p.penjualan_id', 'p.slug', 'u.name', 'p.grand_total', 'p.tanggal_penjualan')
                    ->where('p.counter_id', $counter->counter_id)
                    ->orderByDesc('p.tanggal_penjualan')
                    ->get();
                return DataTables::of($penjualan)->addColumn('action', function ($object) use ($path) {
                    $html = ' <a href="" class="btn btn-info waves-effect waves-light">'
                        . '  <i class="bx bx-detail font-size-18 align-middle me-2"></i>Detail</a>';
                    return $html;
                })
                    ->rawColumns(['action'])
                    ->make(true);
            }
        } elseif ($request->ajax() && !empty($request->type)) {
            if ($user->role == 'gudang' || $user->role == 'owner') {
                $penjualan = DB::table('penjualans as p')
                    ->join('detail_penjualans as dp', 'p.penjualan_id', '=', 'dp.penjualan_id')
                    ->join('barang_counters as bc', 'dp.barang_counter_id', '=', 'bc.barang_counter_id')
                    ->join('barangs as b', 'bc.barang_id', '=', 'b.barang_id')
                    ->join('counters as c', 'p.counter_id', '=', 'c.counter_id')
                    ->join('users as u', 'c.user_id', '=', 'u.user_id')
                    ->select('p.penjualan_id', 'p.slug', 'u.name', 'p.grand_total', 'p.tanggal_penjualan', 'b.nama_barang', 'dp.quantity', 'dp.subtotal')
                    ->orderByDesc('p.tanggal_penjualan')
                    ->get();
                return DataTables::of($penjualan)->addColumn('action', function ($object) use ($path) {
                    $html = ' <a href="" class="btn btn-info waves-effect waves-light">'
                        . '  <i class="bx bx-detail font-size-18 align-middle me-2"></i>Detail</a>';
                    return $html;
                })
                    ->rawColumns(['action'])
                    ->make(true);
            } elseif ($user->role == 'counter') {
                $counter = DB::table('counters')->where('user_id', $user->user_id)->first();
                $penjualan = DB::table('penjualans as p')
                    ->join('detail_penjualans as dp', 'p.penjualan_id', '=', 'dp.penjualan_id')
                    ->join('barang_counters as bc', 'dp.barang_counter_id', '=', 'bc.barang_counter_id')
                    ->join('barangs as b', 'bc.barang_id', '=', 'b.barang_id')
                    ->join('counters as c', 'p.counter_id', '=', 'c.counter_id')
                    ->join('users as u', 'c.user_id', '=', 'u.user_id')
                    ->select('p.penjualan_id', 'p.slug', 'u.name', 'p.grand_total', 'p.tanggal_penjualan', 'b.nama_barang', 'dp.quantity', 'dp.subtotal')
                    ->where('p.counter_id', $counter->counter_id)
                    ->orderByDesc('p.tanggal_penjualan')
                    ->get();
                return DataTables::of($penjualan)->addColumn('action', function ($object) use ($path) {
                    $html = ' <a href="" class="btn btn-info waves-effect waves-light">'
                        . '  <i class="bx bx-detail font-size-18 align-middle me-2"></i>Detail</a>';
                    return $html;
                })
                    ->rawColumns(['action'])
                    ->make(true);
            }
        }
        // dd($penjualan_id);

        return view('pages.history.penjualan', compact('user'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Barang;
use App\Models\Admin\BarangGudang;
use App\Models\Admin\BarangCounter;
use App\Models\Admin\Supplier;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class BarangController extends Controller
{
    public function userAuth()
    {
        $user = Auth::guard('user')->user();
        return $user;
    }

    public function index(Request $request)
    {
        $user = $this->userAuth();
        $path = 'barang';

        $suppliers = Supplier::all();

        if ($user->role == 'gudang' || $user->role == 'owner') {
            if ($request->ajax() && empty($request->target)) {
                $barangs = DB::table('barangs as a')
                    ->join('barang_gudangs as b', 'a.barang_id', '=', 'b.barang_id')
                    ->join('barang_counters as c', 'a.barang_id', '=', 'c.barang_id')
                    ->select(
                        'a.barang_id',
                        'a.slug',
                        'a.nama_barang',
                        'a.harga_barang',
                        'a.supplier_id',
                        DB::raw('(SELECT SUM(stok_awal) + SUM(stok_masuk) - SUM(stok_keluar) FROM barang_gudangs WHERE barang_id = a.barang_id GROUP BY barang_id) + (SELECT SUM(stok_awal) + SUM(stok_masuk) - SUM(stok_keluar) FROM barang_counters WHERE barang_id = a.barang_id GROUP BY barang_id) as qty_total'),
                        'a.quantity_satuan',
                        'a.konversi_quantity',
                        'a.konversi_satuan',
                        'a.biaya_penyimpanan'
                    )
                    ->groupBy('a.barang_id', 'a.slug', 'a.nama_barang', 'a.harga_barang', 'a.supplier_id', 'a.quantity_satuan', 'a.konversi_quantity', 'a.konversi_satuan', 'a.biaya_penyimpanan')
                    ->orderBy('a.barang_id', 'ASC')
                    ->get();


                return DataTables::of($barangs)
                    ->addColumn('action', function ($object) use ($path, $user) {
                        $html = '';
                        if ($user->role == 'gudang') {
                            $html .= ' <a href="' . route($path . ".edit", ["slug" => $object->slug]) . '" class="btn btn-warning waves-effect waves-light">'
                                . ' <i class="bx bx-edit align-middle me-2 font-size-18"></i></a>';
                            $html .= ' <a href="' . route($path . ".destroy", ["slug" => $object->slug]) . '" class="btn btn-danger waves-effect waves-light">'
                                . ' <i class="bx bx-trash align-middle me-2 font-size-18"></i></a>';
                            $html .= ' <button type="button" class="btn btn-secondary waves-effect waves-light btn-detail" data-bs-toggle="modal" data-bs-target="#detailModal">
                            <i class="bx bx-detail font-size-18 align-middle me-2"></i></button>';
                        } else {
                            $html .= '<button type="button" class="btn btn-info waves-effect waves-light btn-detail" data-bs-toggle="modal" data-bs-target="#detailModal">
                        <i class="bx bx-detail font-size-18 align-middle me-2"></i></button>';
                        }
                        return $html;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            } else if ($request->ajax() && !empty($request->target)) {
                $data = '';
                if ($request->target == 'gudang') {
                    $data = DB::table('barangs as a')
                        ->join('barang_gudangs as b', 'a.barang_id', '=', 'b.barang_id')
                        ->select(
                            'b.barang_gudang_id as barang_id',
                            'b.slug',
                            'a.nama_barang',
                            'a.harga_barang',
                            DB::raw('(SELECT (SUM(stok_masuk) - SUM(stok_keluar)) FROM barang_gudangs WHERE barang_id = b.barang_id) as quantity'),
                            'a.quantity_satuan',
                            'a.konversi_quantity',
                            'a.konversi_satuan'
                        )
                        ->groupBy('b.barang_gudang_id', 'b.slug', 'a.nama_barang', 'a.harga_barang', 'a.quantity_satuan', 'a.konversi_quantity', 'a.konversi_satuan')
                        ->orderBy('b.barang_gudang_id', 'ASC')
                        ->get();

                    return DataTables::of($data)->make(true);
                } else if ($request->target == 'counter') {
                    $data = DB::table('barangs as a')
                        ->join('barang_counters as b', 'a.barang_id', '=', 'b.barang_id')
                        ->select(
                            'b.barang_counter_id as barang_id',
                            'b.slug',
                            'a.nama_barang',
                            'a.harga_barang',
                            DB::raw('(SUM(b.stok_masuk) - SUM(b.stok_keluar)) as quantity'),
                            'a.konversi_satuan'
                        )
                        ->groupBy('b.barang_counter_id', 'b.slug', 'a.nama_barang', 'a.harga_barang', 'a.konversi_satuan')
                        ->orderBy('b.barang_counter_id', 'ASC')
                        ->get();
                    return DataTables::of($data)->make(true);
                }
            }
        } elseif ($user->role == 'counter') {
            $counters = DB::table('counters')
                ->select('counter_id')
                ->where('user_id', $user->user_id)
                ->first();
            if ($request->ajax()) {
                $barangs = DB::table('barang_counters as a')
                    ->join('barangs as b', 'a.barang_id', '=', 'b.barang_id')
                    ->select(
                        'a.barang_counter_id as barang_id',
                        'b.nama_barang',
                        'b.harga_barang',
                        'a.slug',
                        DB::raw('(a.stok_masuk - a.stok_keluar) as quantity')
                    )
                    ->where('a.counter_id', $counters->counter_id)
                    ->orderBy('a.barang_counter_id', 'ASC')
                    ->get();
                return DataTables::of($barangs)->make(true);
            }
        }

        return view('pages.barang.index', compact('user', 'suppliers'));
    }

    public function create()
    {
        $user = $this->userAuth();
        $barang_id = Barang::generateBarangId();
        return view('pages.barang.create', compact('barang_id', 'user'));
    }

    public function validatorHelper($request, $slug = null)
    {
        if (!empty($slug)) {
            $barangs = Barang::where('slug', $slug)->first();
            $check_barangs = Barang::where('nama_barang', $request['nama_barang'])
                ->where('nama_barang', '<>', $barangs->nama_barang)->count();
            if (empty($request['nama_barang']) || empty($request['harga_barang']) || empty($request['supplier_id']) || empty($request['quantity_satuan']) || empty($request['konversi_quantity']) || empty($request['konversi_satuan'])) {
                $msg = (object) [
                    "message" => "Tidak boleh ada field yang kosong !!",
                    "response" => "warning"
                ];
                return $msg;
            } elseif ($check_barangs > 0) {
                $msg = (object) [
                    "message" => "Nama barang tersebut sudah ada !!",
                    "response" => "warning"
                ];
                return $msg;
            }
        } else {
            $barangs = Barang::where('nama_barang', $request['nama_barang'])->first();
            if (empty($request['nama_barang']) || empty($request['harga_barang']) || empty($request['supplier_id']) || empty($request['quantity_satuan']) || empty($request['konversi_quantity']) || empty($request['konversi_satuan'])) {
                $msg = (object) [
                    "message" => "Tidak boleh ada field yang kosong !!",
                    "response" => "warning"
                ];
                return $msg;
            } elseif (!empty($barangs)) {
                $msg = (object) [
                    "message" => "Nama barang tersebut sudah ada !!",
                    "response" => "warning"
                ];
                return $msg;
            }
        }
    }

    public function store(Request $request)
    {

        // Validasi input
        $validator = $this->validatorHelper($request->all());
        if (!empty($validator)) {
            return redirect()->back()->with(['msg' => $validator->message]);
        }


        // Memulai transaksi database
        DB::beginTransaction();
        try {
            // Simpan data barang baru
            $barang_id = Barang::generateBarangId();
            $barangs = new Barang;
            $barangs->barang_id = $barang_id;
            $barangs->slug = Str::random(16);
            $barangs->nama_barang = $request->nama_barang;
            $barangs->harga_barang = $request->harga_barang;
            $barangs->supplier_id = $request->supplier_id;
            $barangs->quantity_satuan = $request->quantity_satuan;
            $barangs->konversi_quantity = $request->konversi_quantity;
            $barangs->konversi_satuan = $request->konversi_satuan;
            $barangs->save();

            // Simpan data barang di gudang
            $barang_gudang_id = BarangGudang::generateBarangGudangId($barang_id);
            $barang_gudangs = new BarangGudang;
            $barang_gudangs->barang_gudang_id = $barang_gudang_id;
            $barang_gudangs->slug = Str::random(16);
            $barang_gudangs->gudang_id = 'G00001'; // Asumsi gudang id tetap
            $barang_gudangs->barang_id = $barang_id;
            $barang_gudangs->save();

            // Simpan data barang di setiap counter
            $counters = DB::table('counters')->get();
            foreach ($counters as $counter) {
                $barang_counter_id = BarangCounter::generateBarangCounterId($counter->counter_id, $barang_id);
                $barang_counters = new BarangCounter;
                $barang_counters->barang_counter_id = $barang_counter_id;
                $barang_counters->slug = Str::random(16);
                $barang_counters->counter_id = $counter->counter_id;
                $barang_counters->barang_id = $barang_id;
                $barang_counters->save();
            }

            // Commit transaksi jika tidak ada masalah
            DB::commit();
            return redirect()->route('barang')->with('msg', 'Data barang baru berhasil ditambahkan');
        } catch (\Exception $ex) {
            // Rollback transaksi jika terjadi kesalahan
            echo $ex->getMessage();
            DB::rollBack();
        }
    }


    public function edit($slug)
    {
        $user = $this->userAuth();
        $barangs = Barang::where('slug', $slug)->first();
        return view('pages.barang.edit', compact('barangs', 'user'));
    }

    public function update(Request $request, $slug)
    {

        $validator = $this->validatorHelper($request->all(), $slug);

        if (!empty($validator)) {
            return redirect()->back()->with(['msg' => $validator->message]);
        }

        DB::beginTransaction();
        try {
            $barangs = Barang::where('slug', $slug)->first();
            $barangs->nama_barang = $request->nama_barang;
            $barangs->harga_barang = $request->harga_barang;
            $barangs->supplier_id = $request->supplier_id;
            $barangs->quantity_satuan = $request->quantity_satuan;
            $barangs->konversi_quantity = $request->konversi_quantity;
            $barangs->konversi_satuan = $request->konversi_satuan;
            $barangs->save();
            DB::commit();
            return redirect()->route('barang')->with('msg', 'Data barang berhasil di ubah');
        } catch (\Exception $ex) {
            echo $ex->getMessage();
            DB::rollBack();
        }
    }

    public function destroy($slug)
    {
        DB::beginTransaction();
        try {
            Barang::where('slug', $slug)->delete();
            DB::commit();
            return redirect(route('barang'))->with('msg', 'Data barang berhasil dihapus');
        } catch (\Exception $ex) {
            echo $ex->getMessage();
            DB::rollBack();
        }
    }

    public function biayaPenyimpanan(Request $request)
    {
        try {
            $qtyTotal = DB::table('barangs as a')
                ->join('barang_gudangs as b', 'a.barang_id', '=', 'b.barang_id')
                ->join('barang_counters as c', 'a.barang_id', '=', 'c.barang_id')
                ->selectRaw('(SUM(b.stok_masuk) - SUM(b.stok_keluar) + SUM(c.stok_masuk) - SUM(c.stok_keluar)) as qty_total')
                ->limit(1)
                ->get()[0]->qty_total;

            $biaya_penyimpanan_perunit = $request->total_biaya / $qtyTotal;

            DB::beginTransaction();
            DB::table('barangs')->update(['biaya_penyimpanan' => $biaya_penyimpanan_perunit]);
            DB::commit();

            return response()->json([], 200);
        } catch (\Exception $ex) {
            echo $ex->getMessage();
            DB::rollBack();
        }
    }





    public function checkROP()
    {
        $barangs = DB::table('barangs as b')
            ->join('barang_gudangs as bg', 'b.barang_id', '=', 'bg.barang_id')
            ->join('barang_counters as bc', 'b.barang_id', '=', 'bc.barang_id')
            ->selectRaw("b.barang_id, b.slug, b.nama_barang, b.harga_barang, b.biaya_penyimpanan, b.rop, (SUM(bg.stok_masuk) - SUM(bg.stok_keluar) + SUM(bc.stok_masuk) - SUM(bc.stok_keluar)) as qty_total")
            ->groupByRaw("b.barang_id, b.slug, b.nama_barang, b.harga_barang, b.biaya_penyimpanan, b.rop")
            ->havingRaw("(SUM(bg.stok_masuk) - SUM(bg.stok_keluar) + SUM(bc.stok_masuk) - SUM(bc.stok_keluar)) <= b.rop")
            ->orderByRaw("qty_total asc")
            ->get();

        $jumlah_barang = $barangs->count();

        $result = [
            'barangs' => $barangs,
            'jumlah' => $jumlah_barang
        ];

        return response()->json($result, 200);
    }
}

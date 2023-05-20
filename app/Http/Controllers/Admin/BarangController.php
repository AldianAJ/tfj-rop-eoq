<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Barang;
use App\Models\Admin\BarangGudang;
use App\Models\Admin\BarangCounter;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BarangController extends Controller
{

    public function index(Request $request)
    {
        $path = 'barang';

        if ($request->ajax() && empty($request->target)) {
            $query = "SELECT a.barang_id, a.slug,a.nama_barang, a.harga_barang, a.biaya_penyimpanan, a.rop,((SELECT SUM(stok_masuk)-SUM(stok_keluar) FROM barang_gudangs WHERE barang_id = a.barang_id GROUP BY barang_id) + (SELECT SUM(stok_masuk)-SUM(stok_keluar) FROM barang_counters WHERE barang_id = a.barang_id GROUP BY barang_id)) as qty_total
            FROM barangs as a
            JOIN barang_gudangs as b on a.barang_id = b.barang_id
            JOIN barang_counters as c on a.barang_id = c.barang_id
            GROUP BY a.barang_id, a.nama_barang, a.harga_barang, a.biaya_penyimpanan, a.rop ORDER BY a.barang_id ASC;";
            $barangs = DB::select($query);

            return DataTables::of($barangs)
                ->addColumn('action', function ($object) use ($path) {
                    $html = ' <a href="' . route($path . ".edit", ["slug" => $object->slug]) . '" class="btn btn-success waves-effect waves-light">'
                        . ' <i class="bx bx-edit align-middle me-2 font-size-18"></i>Edit</a>';
                    $html .= ' <a href="' . route($path . ".destroy", ["slug" => $object->slug]) . '" class="btn btn-danger waves-effect waves-light">'
                        . ' <i class="bx bx-trash align-middle me-2 font-size-18"></i>Hapus</a>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        } else if ($request->ajax() && !empty($request->target)) {
            $data = '';
            if ($request->target == 'gudang') {
                $query = "SELECT b.barang_gudang_id as barang_id, b.slug,a.nama_barang, a.harga_barang, (SELECT (SUM(stok_masuk) - SUM(stok_keluar)) FROM barang_gudangs WHERE barang_id = b.barang_id GROUP BY barang_id) as quantity
                FROM barangs as a
                JOIN barang_gudangs as b on a.barang_id = b.barang_id
                GROUP BY b.barang_gudang_id, b.slug,a.nama_barang, a.harga_barang
                ORDER BY b.barang_gudang_id ASC";
                $data = DB::select($query);
                return DataTables::of($data)->make(true);
            } else if ($request->target == 'counter') {
                $query = "SELECT b.barang_counter_id as barang_id, b.slug,a.nama_barang, a.harga_barang, (SELECT (SUM(stok_masuk) - SUM(stok_keluar)) FROM barang_counters WHERE barang_id = b.barang_id GROUP BY barang_id) as quantity
                FROM barangs as a
                JOIN barang_counters as b on a.barang_id = b.barang_id
                GROUP BY b.barang_counter_id, b.slug,a.nama_barang, a.harga_barang
                ORDER BY b.barang_counter_id ASC";
                $data = DB::select($query);
                return DataTables::of($data)->make(true);
            }
            // return DataTables::of($datas)->make(true);
        }
        return view('pages.barang.index');
    }

    public function create()
    {
        $barang_id = Barang::generateBarangId();
        return view('pages.barang.create', compact('barang_id'));
    }

    public function validatorHelper($request, $slug = null)
    {
        if (!empty($slug)) {
            $barangs = Barang::where('slug', $slug)->first();
            $check_barangs = Barang::where('nama_barang', $request['nama_barang'])
                ->where('nama_barang', '<>', $barangs->nama_barang)->count();
            if (empty($request['nama_barang']) || empty($request['harga_barang'])) {
                # tidak boleh ada field yang kosong
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
            if (empty($request['nama_barang']) || empty($request['harga_barang'])) {
                # tidak boleh ada field yang kosong
                $msg = (object) [
                    "message" => "Tidak boleh ada field yang kosong !!",
                    "response" => "warning"
                ];
                return $msg;
            } elseif (!empty($barangs)) {
                # nama barang sudah ada
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
        $validator = $this->validatorHelper($request->all());

        if (!empty($validator)) {
            return redirect()->back()->with(['msg' => $validator->message]);
        }

        $counters = DB::table('counters')->get();
        DB::beginTransaction();
        try {
            $barang_id = Barang::generateBarangId();
            $barangs = new Barang;
            $barangs->barang_id = $barang_id;
            $barangs->slug = Str::random(16);
            $barangs->nama_barang = $request->nama_barang;
            $barangs->harga_barang = $request->harga_barang;
            $barangs->save();

            $barang_gudang_id = BarangGudang::generateBarangGudangId($barang_id);
            $barang_gudangs = new BarangGudang;
            $barang_gudangs->barang_gudang_id = $barang_gudang_id;
            $barang_gudangs->slug = Str::random(16);
            $barang_gudangs->barang_id = $barang_id;
            $barang_gudangs->save();

            foreach ($counters as $counter) {
                $barang_counter_id = BarangCounter::generateBarangCounterId($counter->counter_id, $barang_id);
                $barang_counters = new BarangCounter;
                $barang_counters->barang_counter_id = $barang_counter_id;
                $barang_counters->slug = Str::random(16);
                $barang_counters->counter_id = $counter->counter_id;
                $barang_counters->barang_id = $barang_id;
                $barang_counters->save();
            }

            DB::commit();
            return redirect()->route('barang')->with('msg', 'Data barang baru berhasil ditambahkan');
        } catch (\Exception $ex) {
            //throw $th;
            echo $ex->getMessage();
        }
    }

    public function edit($slug)
    {
        $barangs = Barang::where('slug', $slug)->first();
        return view('pages.barang.edit', compact('barangs'));
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
            $barangs->save();
            DB::commit();
            return redirect()->route('barang')->with('msg', 'Data barang berhasil di ubah');
        } catch (\Exception $ex) {
            //throw $th;
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
            //throw $th;
            echo $ex->getMessage();
            DB::rollBack();
        }
    }
}
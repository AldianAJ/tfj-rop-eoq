<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Supplier;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;


class SupplierController extends Controller
{
    public function userAuth()
    {
        $user = Auth::guard('user')->user();
        return $user;
    }

    public function index(Request $request)
    {
        $user = $this->userAuth();
        $path = 'supplier';
        if ($request->ajax()) {
            $suppliers = DB::table('suppliers as a')
                ->select('a.supplier_id', 'a.slug', 'a.nama_supplier', 'a.alamat', 'a.telp', 'a.biaya_pemesanan')
                ->get();
            return DataTables::of($suppliers)
                ->addColumn('action', function ($object) use ($path) {
                    $html = ' <a href="' . route($path . ".edit", ["slug" => $object->slug]) . '" class="btn btn-warning waves-effect waves-light">'
                        . ' <i class="bx bx-edit align-middle me-2 font-size-18"></i>Edit</a>';
                    $html .= ' <a href="' . route($path . ".destroy", ["slug" => $object->slug]) . '" class="btn btn-danger waves-effect waves-light">'
                        . ' <i class="bx bx-trash align-middle me-2 font-size-18"></i>Delete</a>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.supplier.index', compact('user'));
    }

    public function create()
    {
        $user = $this->userAuth();
        $supplier_id = Supplier::generateSupplierId();
        return view('pages.supplier.create', compact('supplier_id', 'user'));
    }

    public function validatorHelper($request, $slug = null)
    {
        if (!empty($slug)) {
            $suppliers = Supplier::where('slug', $slug)->first();
            $check_suppliers = Supplier::where('nama_supplier', $request['nama_supplier'])
                ->where('nama_supplier', '<>', $suppliers->nama_supplier)->count();
            if (empty($request['nama_supplier']) || empty($request['alamat']) || empty($request['telp']) || empty($request['biaya_pemesanan'])) {
                $msg = (object) [
                    "message" => "Tidak boleh ada field yang kosong !!",
                    "response" => "warning"
                ];
                return $msg;
            } elseif ($check_suppliers > 0) {
                $msg = (object) [
                    "message" => "Nama Supplier tersebut sudah ada !!",
                    "response" => "warning"
                ];
                return $msg;
            }
        } else {
            $suppliers = Supplier::where('nama_supplier', $request['nama_supplier'])->first();
            if (empty($request['nama_supplier']) || empty($request['alamat']) || empty($request['telp']) || empty($request['biaya_pemesanan'])) {
                $msg = (object) [
                    "message" => "Tidak boleh ada field yang kosong !!",
                    "response" => "warning"
                ];
                return $msg;
            } elseif (!empty($suppliers)) {
                $msg = (object) [
                    "message" => "Nama supplier tersebut sudah ada !!",
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

        $suppliers = DB::table('suppliers')->get();
        DB::beginTransaction();
        try {
            $supplier_id = Supplier::generateSupplierId();
            $suppliers = new Supplier;
            $suppliers->supplier_id = $supplier_id;
            $suppliers->slug = Str::random(16);
            $suppliers->nama_supplier = $request->nama_supplier;
            $suppliers->alamat = $request->alamat;
            $suppliers->telp = $request->telp;
            $suppliers->biaya_pemesanan = $request->biaya_pemesanan;
            $suppliers->save();
            DB::commit();
            return redirect()->route('supplier')->with('msg', 'Data supplier baru berhasil ditambahkan');
        } catch (\Exception $ex) {
            echo $ex->getMessage();
            DB::rollBack();
        }
    }


    public function edit($slug)
    {
        $user = $this->userAuth();
        $suppliers = Supplier::where('slug', $slug)->first();
        return view('pages.supplier.edit', compact('suppliers', 'user'));
    }

    public function update(Request $request, $slug)
    {
        $validator = $this->validatorHelper($request->all(), $slug);

        if (!empty($validator)) {
            return redirect()->back()->with(['msg' => $validator->message]);
        }

        DB::beginTransaction();
        try {
            $suppliers = Supplier::where('slug', $slug)->first();
            $suppliers->nama_supplier = $request->nama_supplier;
            $suppliers->alamat = $request->alamat;
            $suppliers->telp = $request->telp;
            $suppliers->biaya_pemesanan = $request->biaya_pemesanan;
            $suppliers->save();
            DB::commit();
            return redirect()->route('supplier')->with('msg', 'Data supplier berhasil di ubah');
        } catch (\Exception $ex) {
            echo $ex->getMessage();
            DB::rollBack();
        }
    }

    public function destroy($slug)
    {
        DB::beginTransaction();
        try {
            Supplier::where('slug', $slug)->delete();
            DB::commit();
            return redirect(route('supplier'))->with('msg', 'Data supplier berhasil dihapus');
        } catch (\Exception $ex) {
            echo $ex->getMessage();
            DB::rollBack();
        }
    }
}

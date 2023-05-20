<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Gudang;
use App\Models\Admin\UserAuth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class GudangController extends Controller
{

    public function validatorHelper($request)
    {
        if (empty($request['nama_gudang']) || empty($request['alamat_gudang']) || empty($request['username'])) {
            # tidak boleh ada field yang kosong
            $msg = (object) [
                "message" => "Tidak boleh ada field yang kosong, kecuali password !!",
                "response" => "warning"
            ];
            return $msg;
        }
    }

    public function index(Request $request)
    {
        $path = 'gudang';
        if ($request->ajax()) {

            $query = 'SELECT a.gudang_id, b.name, b.address, b.username, a.slug
            FROM gudangs as a
            JOIN users as b on a.user_id = b.user_id;';
            $gudangs = DB::select($query);

            return DataTables::of($gudangs)
                ->addColumn('action', function ($object) use ($path) {
                    $html = ' <a href="' . route($path . ".edit", ["slug" => $object->slug]) . '" class="btn btn-success waves-effect waves-light">'
                        . ' <i class="bx bx-edit align-middle me-2 font-size-18"></i>Edit</a>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('pages.gudang.index');
    }

    public function edit($slug)
    {
        $gudangs = DB::table('gudangs as a')
            ->join('users as b', 'a.user_id', '=', 'b.user_id')
            ->select('a.gudang_id', 'b.name', 'b.address', 'b.username', 'a.slug')
            ->where('a.slug', $slug)->first();

        return view('pages.gudang.edit', compact('gudangs'));
    }

    public function update(Request $request, $slug)
    {
        $validator = $this->validatorHelper($request->all());

        if (!empty($validator)) {
            return redirect()->back()->with(['msg' => $validator->message]);
        }

        DB::beginTransaction();
        try {
            $gudangs = Gudang::where('slug', $slug)->first();
            $users = UserAuth::where('user_id', $gudangs->user_id)->first();
            $users->name = $request->nama_gudang;
            $users->address = $request->alamat_gudang;
            $users->username = $request->username;
            if (!empty($request->password)) {
                $users->password = $request->password;
            }
            $users->save();
            DB::commit();
            return redirect()->route('gudang')->with('msg', 'Data gudang berhasil di ubah');
        } catch (\Exception $ex) {
            echo $ex->getMessage();
            DB::rollBack();
        }
    }
}

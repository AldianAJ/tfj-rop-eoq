<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\BarangCounter;
use Illuminate\Http\Request;
use App\Models\Admin\Counter;
use App\Models\Admin\UserAuth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class CounterController extends Controller
{
    public function userAuth()
    {
        $user = Auth::guard('user')->user();
        return $user;
    }

    public function storeBarangCounter($counter_id)
    {
        $barangs = DB::table('barangs')->get();
        foreach ($barangs as $barang) {
            DB::beginTransaction();
            try {
                $barang_counters = new BarangCounter;
                $barang_counter_id = BarangCounter::generateBarangCounterId($counter_id, $barang->barang_id);
                $barang_counters->barang_counter_id = $barang_counter_id;
                $barang_counters->slug = Str::random(16);
                $barang_counters->counter_id = $counter_id;
                $barang_counters->barang_id = $barang->barang_id;
                $barang_counters->save();
                DB::commit();
            } catch (\Exception $ex) {
                echo $ex->getMessage();
                DB::rollBack();
            }
        }
    }

    public function index(Request $request)
    {
        $user = $this->userAuth();
        $path = 'counter';
        if ($request->ajax()) {
            $counters = DB::table('counters as a')
                ->join('users as b', 'a.user_id', '=', 'b.user_id')
                ->select('a.counter_id', 'a.slug', 'b.name', 'b.address', 'b.username')
                ->get();
            return DataTables::of($counters)
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

        return view('pages.counter.index', compact('user'));
    }

    public function validatorHelper($request, $slug = null)
    {
        if (!empty($slug)) {
            $counters = Counter::where('slug', $slug)->first();
            $users = UserAuth::where('user_id', $counters->user_id)->first();
            $check_name = UserAuth::where('name', $request['nama_counter'])
                ->where('name', '<>', $users->name)->count();
            $check_username = UserAuth::where('username', $request['username'])
                ->where('username', '<>', $users->username)->count();
            if (empty($request['nama_counter']) || empty($request['alamat_counter']) || empty($request['username'])) {
                $msg = (object) [
                    "message" => "Tidak boleh ada field yang kosong !!",
                    "response" => "warning"
                ];
                return $msg;
            } elseif ($check_name > 0) {
                $msg = (object) [
                    "message" => "Nama counter tersebut sudah ada !!",
                    "response" => "warning"
                ];
                return $msg;
            } elseif ($check_username > 0) {
                $msg = (object) [
                    "message" => "Username counter tersebut sudah ada !!",
                    "response" => "warning"
                ];
                return $msg;
            }
        } else {
            $check_name = UserAuth::where('name', $request['nama_counter'])->first();
            $check_username = UserAuth::where('username', $request['username'])->first();
            if (empty($request['counter_id']) || empty($request['nama_counter']) || empty($request['alamat_counter']) || empty($request['username']) || empty($request['password'])) {
                $msg = (object) [
                    "message" => "Tidak boleh ada field yang kosong !!",
                    "response" => "warning"
                ];
                return $msg;
            } elseif (!empty($check_name)) {
                $msg = (object) [
                    "message" => "Nama Counter tersebut sudah ada !!",
                    "response" => "warning"
                ];
                return $msg;
            } elseif (!empty($check_username)) {
                $msg = (object) [
                    "message" => "Username Counter tersebut sudah ada !!",
                    "response" => "warning"
                ];
                return $msg;
            }
        }
    }

    public function create()
    {
        $user = $this->userAuth();
        $counter_id = Counter::generateCounterId();
        return view('pages.counter.create', compact('counter_id', 'user'));
    }

    public function store(Request $request)
    {
        $validator = $this->validatorHelper($request->all());

        if (!empty($validator)) {
            return redirect()->back()->with(['msg' => $validator->message]);
        }

        DB::beginTransaction();
        try {
            $users = new UserAuth;
            $counters = new Counter;
            $user_id = UserAuth::generateUserId();
            $users->user_id = $user_id;
            $users->slug = Str::random(16);
            $users->name = $request->nama_counter;
            $users->username = $request->username;
            $users->address = $request->alamat_counter;
            $users->password = bcrypt($request->password);
            $users->status = 'Active';
            $users->role = 'counter';
            $users->save();

            $counters->counter_id = $request->counter_id;
            $counters->slug = Str::random(16);
            $counters->user_id = $user_id;
            $counters->save();
            DB::commit();

            $this->storeBarangCounter($request->counter_id);

            return redirect()->route('counter')->with('msg', 'Data counter baru berhasil ditambahkan');
        } catch (\Exception $ex) {
            echo $ex->getMessage();
            DB::rollBack();
        }
    }

    public function edit($slug)
    {
        $user = $this->userAuth();
        $counters = DB::table('counters as a')
            ->join('users as b', 'a.user_id', '=', 'b.user_id')
            ->select('a.counter_id', 'a.slug', 'b.name', 'b.address', 'b.username')
            ->where('a.slug', $slug)->first();

        return view('pages.counter.edit', compact('counters', 'user'));
    }

    public function update(Request $request, $slug)
    {
        $validator = $this->validatorHelper($request->all(), $slug);

        if (!empty($validator)) {
            return redirect()->back()->with(['msg' => $validator->message]);
        }

        DB::beginTransaction();
        try {
            $counters = Counter::where('slug', $slug)->first();
            $users = UserAuth::where('user_id', $counters->user_id)->first();
            $users->name = $request->nama_counter;
            $users->username = $request->username;
            $users->address = $request->alamat_counter;
            if (!empty($request->password)) {
                $users->password = bcrypt($request->password);
            }
            $users->save();
            DB::commit();

            return redirect()->route('counter')->with('msg', 'Data counter berhasil diubah');
        } catch (\Exception $ex) {
            echo $ex->getMessage();
            DB::rollBack();
        }
    }

    public function destroy($slug)
    {
        DB::beginTransaction();
        try {
            $counters = Counter::where('slug', $slug)->first();
            UserAuth::where('user_id', $counters->user_id)->delete();
            DB::commit();

            return redirect()->route('counter')->with('msg', 'Data counter berhasil dihapus');
        } catch (\Exception $ex) {
            echo $ex->getMessage();
            DB::rollBack();
        }
    }

}

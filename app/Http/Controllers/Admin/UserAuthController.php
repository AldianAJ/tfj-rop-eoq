<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\UserAuth;
use Illuminate\Support\Facades\Auth;

class UserAuthController extends Controller
{
    public function validatorHelper($request)
    {
        if (empty($request['username']) || empty($request['password'])) {
            $msg = (object) [
                "message" => "Tidak boleh ada field yang kosong !!",
                "response" => "warning"
            ];
            return $msg;
        }
    }

    public function index()
    {
        return view('pages.auth.index');
    }

    public function login(Request $request)
    {
        $validator = $this->validatorHelper($request->all());

        if (!empty($validator)) {
            return redirect()->back()->with(['msg' => $validator->message]);
        }

        if (Auth::guard('user')->attempt(['username' => $request->username, 'password' => $request->password])) {
            if (Auth::guard('user')->user()->role == 'counter') {
                return redirect()->route('barang');
            } else {
                return redirect()->intended('/');
            }
        } else {
            return redirect()->route('auth')->with('msg', 'Akun tidak ditemukan, periksa kembali username/password Anda');
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('user')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();
        return redirect(route('auth'));
    }
}

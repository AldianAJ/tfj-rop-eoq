<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Penjualan;
use Illuminate\Support\Facades\Auth;

class PenjualanController extends Controller
{

    public function userAuth()
    {
        $user = Auth::guard('user')->user();
        return $user;
    }

    public function index()
    {
        $user = $this->userAuth();
        $penjualan_id = Penjualan::generatePenjualanCounterId('C00001');

        // dd($penjualan_id);

        return view('pages.history.penjualan', compact('user'));
    }
}

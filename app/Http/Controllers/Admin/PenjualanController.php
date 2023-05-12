<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Penjualan;

class PenjualanController extends Controller
{
    public function index()
    {
        $penjualan_id = Penjualan::generatePenjualanCounterId('C00001');

        dd($penjualan_id);
    }
}

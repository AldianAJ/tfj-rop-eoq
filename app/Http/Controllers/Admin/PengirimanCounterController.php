<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\PengirimanCounter;

class PengirimanCounterController extends Controller
{
    public function index()
    {
        $pengiriman_counter_id = PengirimanCounter::generatePengirimanCounterId('C00002');
        dd($pengiriman_counter_id);
    }
}

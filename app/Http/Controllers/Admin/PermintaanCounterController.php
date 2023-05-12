<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\PermintaanCounter;

class PermintaanCounterController extends Controller
{
    public function index()
    {
        $permintaan_counter_id = PermintaanCounter::generatePermintaanCounterId('C00001');
        dd($permintaan_counter_id);
    }
}

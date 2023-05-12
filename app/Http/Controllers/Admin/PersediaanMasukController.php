<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\PersediaanMasuk;

class PersediaanMasukController extends Controller
{
    public function index()
    {
        $persediaan_masuk_id = PersediaanMasuk::generatePersediaanMasukId();
        dd($persediaan_masuk_id);
    }
}

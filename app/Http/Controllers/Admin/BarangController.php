<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Barang;
use App\Models\Admin\BarangGudang;


class BarangController extends Controller
{
    public function index(Request $request)
    {
        return view('pages.barang.index');
    }

    public function create()
    {
        return view('pages.barang.create');
    }

    public function edit($slug)
    {
        return view('pages.barang.edit');
    }
}

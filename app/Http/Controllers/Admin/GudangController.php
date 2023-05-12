<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Gudang;

class GudangController extends Controller
{
    public function index()
    {
        return view('pages.gudang.index');
    }

    public function edit($slug)
    {
        return view('pages.gudang.edit');
    }
}

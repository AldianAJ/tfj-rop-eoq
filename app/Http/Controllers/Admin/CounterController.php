<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Counter;

class CounterController extends Controller
{
    public function index()
    {
        return view('pages.counter.index');
    }

    public function create()
    {
        return view('pages.counter.create');
    }

    public function edit($slug)
    {
        return view('pages.counter.edit');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DataTables;

class KasirController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // $data = file_get_contents('../database/seeders/json/Barang.json');
            // $data = json_decode($data);

            // return DataTables::of($data)
            //     ->addColumn('action', function ($object) {
            //         $html = '<button class="btn btn-success waves-effect waves-light btn-add" data-bs-toggle="modal"' .
            //             'data-bs-target="#quantityModal"><i class="bx bxs-cart align-middle font-size-18"></i></button>';
            //         return $html;
            //     })
            //     ->rawColumns(['action'])
            //     ->make(true);
        }
        return view('pages.kasir.index');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\UserAuth;

class UserAuthController extends Controller
{
    public function index()
    {
        return view('pages.auth.index');
    }
}

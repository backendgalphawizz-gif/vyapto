<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return view('portal.index');
    }
}

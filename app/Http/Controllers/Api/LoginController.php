<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(Request $request)
    {
		$request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);
		
		
	print_r($request->all());die;
		
		
	}
}

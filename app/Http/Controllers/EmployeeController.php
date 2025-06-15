<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmployeeController extends Controller
{
     public function index()
    {
        // Later: fetch employee data here
        return view('employee.dashboard');
    }
}

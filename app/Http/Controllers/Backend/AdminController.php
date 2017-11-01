<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Models\AdminLoginHistory;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function index(Request $request)
    {
    	return view('Backend.index');
    }
}

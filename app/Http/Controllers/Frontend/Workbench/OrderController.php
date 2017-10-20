<?php

namespace App\Http\Controllers\Frontend\Workbench;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function index()
    {
        return view('frontend.workbench.order.index');
    }
}

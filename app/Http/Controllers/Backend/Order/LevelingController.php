<?php

namespace App\Http\Controllers\Backend\Order;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LevelingController extends Controller
{
    public function index(Request $request )
    {
    	$startDate = $request->start_date;
    	$endDate = $request->end_date;
    	
    	return view('backend.order.leveling.index', compact('startDate', 'endDate'));
    }
}

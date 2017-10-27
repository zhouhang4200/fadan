<?php

namespace App\Http\Controllers\Backend;

use Auth;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * Class OrderController
 * @package App\Http\Controllers\Backend
 */
class OrderController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
    	$startDate = $request->startDate;

    	$endDate = $request->endDate;

    	$status = $request->status;

    	$source = $request->source;

    	$filters = compact('startDate', 'endDate', 'status', 'source');

        $orders = Order::filter($filters)->latest('created_at')->paginate(config('backend.page'));

        return view('backend.order.index', compact('orders', 'status', 'startDate', 'endDate', 'source'));
    }
}
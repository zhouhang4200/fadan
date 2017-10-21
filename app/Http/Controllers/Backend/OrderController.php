<?php

namespace App\Http\Controllers\Backend;

use Auth;
use Illuminate\Http\Request;
use App\Models\Order;
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
    public function index()
    {
        $orders = Order::where('id', 1)->paginate(20);

        //$orderInfo->goodsTemplateValue->pluck('field_value','field_name');

        return view('backend.order.index', compact('orders'));
    }
}
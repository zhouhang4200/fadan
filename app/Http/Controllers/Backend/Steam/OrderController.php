<?php

namespace App\Http\Controllers\Backend\Steam;

use App\Models\Order;
use App\Models\SteamOrder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

/**
 * Class OrderController
 * @package App\Http\Controllers\Backend
 */
class OrderController extends Controller
{

    public function index(Request $request)
    {
        //多条件查找
        $where = function ($query) use ($request) {
            if ($request->has('orderNo') and $request->orderNo != '') {
                $orderNo = "%" . $request->orderNo . "%";
                $query->where('no', 'like', $orderNo);
            }

            if ($request->has('user_id') and $request->user_id != '') {
                $query->where('user_id', $request->user_id);
            }

            if ($request->has('status') and $request->status != '-1') {
                $query->where('status', $request->status);
            }

        };
        $orders = SteamOrder::with('goodses')->where($where)->orderBy('id', 'desc')->paginate(config('backend.page'));
        return view('backend.steam.order.index', compact('orders'));


    }

}

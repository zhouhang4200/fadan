<?php

namespace App\Http\Controllers\Frontend\Steam;

use App\Models\SteamOrder;
use Excel;
use App\Models\Order;
use App\Models\GoodsTemplate;
use App\Repositories\Frontend\OrderRepository;
use \Exception;
use App\Models\Goods;
use App\Repositories\Frontend\GameRepository;
use App\Repositories\Frontend\ServiceRepository;
use App\Repositories\Frontend\UserGoodsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

/**
 * Class OrderController
 * @package App\Http\Controllers\Frontend\Goods
 */
class OrderController extends Controller
{
    /**
     * 订单列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        //多条件查找
        $where = function ($query) use ($request) {
            if ($request->has('orderNo') and $request->orderNo != '') {
                $orderNo = "%" . $request->orderNo . "%";
                $query->where('no', 'like', $orderNo);
            }
            $query->where('user_id', Auth::user()->id);
        };

        $orders = SteamOrder::with('goodses')->where($where)->orderBy('id','desc')->paginate(config('backend.page'));
        return view('frontend.order.index',compact('orders'));
    }

}

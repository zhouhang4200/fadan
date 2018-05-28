<?php

namespace App\Http\Controllers\Backend\Order;

use DB;
use App\Models\Order;
use App\Models\OrderApiNotice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderApiNoticeController extends Controller
{
	/**
	 * 接口失败报警订单
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
    public function index(Request $request)
    {
    	$orderNo = $request->order_no;
    	$status = $request->status;
    	$startDate = $request->start_date;
    	$endDate = $request->end_date;

    	$filters = compact('orderNo', 'status', 'startDate', 'endDate');

    	$orders = OrderApiNotice::filter($filters)
    		->paginate(10);

    	if ($request->ajax()) {
    		return response()->json(view()->make('backend.order.notice.list', compact('orderNo', 'status', 'startDate', 'endDate', 'orders'))->render());
    	}

    	return view('backend.order.notice.index', compact('orders', 'orderNo', 'status', 'startDate', 'endDate'));
    }

    /**
     * 重发
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function repeat(Request $request)
    {
    	$orderApiNotice = OrderApiNotice::find($request->id);

    	if (isset($orderApiNotice) && isset($orderApiNotice->third) && isset($orderApiNotice->orderNo) && isset($orderApiNotice->functionName) && $orderApiNotice->status == 1) {
	    	$datas = Order::orderAndDetailAndConsult($orderApiNotice->orderNo);

	    	$bool = call_user_func_array([config('leveling.controller')[$orderApiNotice->third], config('leveling.action')[$orderApiNotice->functionName]], [$datas]);

	    	// 删除记录
	    	if ($bool) {
	    		OrderApiNotice::destroy($request->id);
	    	}

	    	return response()->ajax(1, '重发成功');
    	}

    	return response()->ajax(0, '重发失败，数据缺失');
    }

    /**
     * 删除
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function delete(Request $request)
    {
    	OrderApiNotice::destroy($request->id);

    	return response()->ajax(1, '删除成功');
    }

    /**
     * 删除所有
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function deleteAll(Request $request)
    {
    	DB::select("delete from order_api_notices");

    	return response()->ajax(1, '删除成功');
    }
}

<?php

namespace App\Http\Controllers\Backend\Order;

use DB;
use Redis;
use Exception;
use Carbon\Carbon;
use App\Models\Order;
use GuzzleHttp\Client;
use App\Models\OrderApiNotice;
use Illuminate\Http\Request;
use App\Exceptions\DailianException;
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

    	// 将值写入数据库
    	$name = "order:order-api-notices";
    	$orderNotices = Redis::hGetAll($name);
    	$ss = OrderApiNotice::get();

    	if ($orderNotices) {
    		foreach ($orderNotices as $key => $order) {
    			$order = json_decode($order, true);
    			$third = explode('-', $key)[1] ?? 0;
    			$functionName = explode('-', $key)[2] ?? 0;
                $orderModel = Order::where('no', $order['datas']['order_no'])->first();
    			
				$arr                     = [];
				$arr['order_no']         = $order['datas']['order_no'] ?? 0;
				$arr['source_order_no']  = $order['datas']['source_order_no'] ?? 0;
				$arr['status']           = $orderModel->status ?? 0;
				$arr['operate']          = config('leveling.operate')[$functionName] ?? 0;
				$arr['third']            = $third ?? 0;
				$arr['reason']           = $order['datas']['notice_reason'] ?? 0;
				$arr['order_created_at'] = $order['datas']['order_created_at'] ?? 0;
				$arr['function_name']    = $functionName ?? 0;
				$arr['created_at']       = Carbon::now()->toDateTimeString();
				$arr['updated_at']       = Carbon::now()->toDateTimeString();

		    	$res = OrderApiNotice::updateOrCreate(['order_no' => $order['datas']['order_no'], 'third' => $third, 'function_name' => $functionName], $arr);
		    	Redis::hDel($name, $key);
    		}
    	}
    	$orders = OrderApiNotice::filter($filters)
            ->latest('id')
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
    	try {
	    	$orderApiNotice = OrderApiNotice::find($request->id);

	    	if (isset($orderApiNotice) && isset($orderApiNotice->third) && isset($orderApiNotice->order_no) && isset($orderApiNotice->function_name) && in_array($orderApiNotice->status, [1, 22, 24])) {
		    	$datas = Order::orderAndDetailAndConsult($orderApiNotice->order_no);

		    	$bool = call_user_func_array([config('leveling.controller')[$orderApiNotice->third], config('leveling.action')[$orderApiNotice->function_name]], [$datas]);

		    	// 删除记录
		    	if ($bool) {
		    		OrderApiNotice::destroy($request->id);
		    	}

		    	return response()->ajax(1, '重发成功');
	    	}
    	} catch (DailianException $e) {
	    	return response()->ajax(0, $e->getMessage());
	    } catch (Exception $e) {
	    	return response()->ajax(0, '本地错误');
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
    	$orders = OrderApiNotice::get();

    	$orders->map(function ($order) {
    		$order->delete();
    	});

    	return response()->ajax(1, '删除成功');
    }
}

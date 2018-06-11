<?php

namespace App\Http\Controllers\Backend\Order;

use DB;
use Auth;
use Redis;
use Exception;
use Carbon\Carbon;
use App\Models\Order;
use GuzzleHttp\Client;
use App\Models\OrderApiNotice;
use Illuminate\Http\Request;
use App\Exceptions\DailianException;
use App\Http\Controllers\Controller;
use App\Models\OrderHistory;
use App\Extensions\Dailian\Controllers\DailianFactory;

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
    			
                $arr                      = [];
                $arr['order_no']          = $order['datas']['order_no'] ?? 0;
                $arr['source_order_no']   = $orderModel->foreign_order_no ?? 0;
                $arr['status']            = $orderModel->status ?? 0;
                $arr['operate']           = config('leveling.operate')[$functionName] ?? 0;
                $arr['third']             = $third ?? 0;
                $arr['reason']            = $order['datas']['notice_reason'] ?? 0;
                $arr['order_created_at']  = $orderModel->created_at ?? 0;
                $arr['function_name']     = $functionName ?? 0;
                $arr['user_id']           = $orderModel->creator_user_id;
                $arr['notice_created_at'] = $order['datas']['notice_created_at'] ?? Carbon::now()->toDateTimeString();
                $arr['created_at']        = Carbon::now()->toDateTimeString();
                $arr['updated_at']        = Carbon::now()->toDateTimeString();

		    	$res = OrderApiNotice::updateOrCreate(['order_no' => $order['datas']['order_no'], 'third' => $third, 'function_name' => $functionName], $arr);
		    	Redis::hDel($name, $key);
    		}
    	}
    	$orders = OrderApiNotice::filter($filters)
            ->latest('notice_created_at')
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

    /**
     * 完成
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function complete(Request $request)
    {
        try {
            $order = Order::where('no', $request->input('order_no', 0))->first();
            DailianFactory::choose('complete')->run($order->no, $order->creator_user_id, 0);

            // 写记录
            $data = [];
            $data['order_no'] = $order->no;
            $data['user_id'] = creator_user_id;
            $data['admin_user_id'] = Auth::user()->id;
            $data['type'] = 0;
            $data['name'] = '补充';
            $data['description'] = '此单为后台管理员手动操作的接口报警订单,操作类型为【完成验收】';
            $data['before'] = '';
            $data['after'] = '';
            $data['created_at'] = Carbon::now()->toDateTimeString();
            $data['creator_primary_user_id'] = $order->creator_primary_user_id;

            OrderHistory::create($data);
        } catch (DailianException $e) {
            myLog('complete-order-api-notice', ['no' => $order->no ?? '', 'message' => $e->getMessage()]);
        } catch (Exception $e) {
            myLog('complete-order-api-notice', ['no' => $order->no ?? '', 'message' => $e->getMessage()]);
        }
    }

    /**
     * 取消撤销
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function cancelRevoke(Request $request)
    {
        try {
            $order = Order::where('no', $request->input('order_no', 0))->first();
            DailianFactory::choose('cancelRevoke')->run($order->no, $order->creator_user_id, 0);

            // 写记录
            $data = [];
            $data['order_no'] = $order->no;
            $data['user_id'] = creator_user_id;
            $data['admin_user_id'] = Auth::user()->id;
            $data['type'] = 0;
            $data['name'] = '补充';
            $data['description'] = '此单为后台管理员手动操作的接口报警订单,操作类型为【取消撤销】';
            $data['before'] = '';
            $data['after'] = '';
            $data['created_at'] = Carbon::now()->toDateTimeString();
            $data['creator_primary_user_id'] = $order->creator_primary_user_id;

            OrderHistory::create($data);
        } catch (DailianException $e) {
            myLog('cancel-revoke-order-api-notice', ['no' => $order->no ?? '', 'message' => $e->getMessage()]);
        } catch (Exception $e) {
            myLog('cancel-revoke-order-api-notice', ['no' => $order->no ?? '', 'message' => $e->getMessage()]);
        }
    }

    /**
     * 同意撤销
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function agreeRevoke(Request $request)
    {
        try {
            $order = Order::where('no', $request->input('order_no', 0))->first();
            DailianFactory::choose('agreeRevoke')->run($order->no, $order->creator_user_id, 0);

            // 写记录
            $data = [];
            $data['order_no'] = $order->no;
            $data['user_id'] = creator_user_id;
            $data['admin_user_id'] = Auth::user()->id;
            $data['type'] = 0;
            $data['name'] = '补充';
            $data['description'] = '此单为后台管理员手动操作的接口报警订单,操作类型为【同意撤销】';
            $data['before'] = '';
            $data['after'] = '';
            $data['created_at'] = Carbon::now()->toDateTimeString();
            $data['creator_primary_user_id'] = $order->creator_primary_user_id;

            OrderHistory::create($data);
        } catch (DailianException $e) {
            myLog('agree-revoke-order-api-notice', ['no' => $order->no ?? '', 'message' => $e->getMessage()]);
        } catch (Exception $e) {
            myLog('agree-revoke-order-api-notice', ['no' => $order->no ?? '', 'message' => $e->getMessage()]);
        }
    }

    /**
     * 不同意撤销
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function refuseRevoke(Request $request)
    {
        try {
            $order = Order::where('no', $request->input('order_no', 0))->first();
            DailianFactory::choose('refuseRevoke')->run($order->no, $order->creator_user_id, 0);

            // 写记录
            $data = [];
            $data['order_no'] = $order->no;
            $data['user_id'] = creator_user_id;
            $data['admin_user_id'] = Auth::user()->id;
            $data['type'] = 0;
            $data['name'] = '补充';
            $data['description'] = '此单为后台管理员手动操作的接口报警订单,操作类型为【不同意撤销】';
            $data['before'] = '';
            $data['after'] = '';
            $data['created_at'] = Carbon::now()->toDateTimeString();
            $data['creator_primary_user_id'] = $order->creator_primary_user_id;

            OrderHistory::create($data);
        } catch (DailianException $e) {
            myLog('refuse-revoke-order-api-notice', ['no' => $order->no ?? '', 'message' => $e->getMessage()]);
        } catch (Exception $e) {
            myLog('refuse-revoke-order-api-notice', ['no' => $order->no ?? '', 'message' => $e->getMessage()]);
        }
    }

    /**
     * 取消仲裁
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function cancelArbitration(Request $request)
    {
        try {
            $order = Order::where('no', $request->input('order_no', 0))->first();
            DailianFactory::choose('cancelArbitration')->run($order->no, $order->creator_user_id, 0);

            // 写记录
            $data = [];
            $data['order_no'] = $order->no;
            $data['user_id'] = creator_user_id;
            $data['admin_user_id'] = Auth::user()->id;
            $data['type'] = 0;
            $data['name'] = '补充';
            $data['description'] = '此单为后台管理员手动操作的接口报警订单,操作类型为【取消仲裁】';
            $data['before'] = '';
            $data['after'] = '';
            $data['created_at'] = Carbon::now()->toDateTimeString();
            $data['creator_primary_user_id'] = $order->creator_primary_user_id;

            OrderHistory::create($data);
        } catch (DailianException $e) {
            myLog('cancel-arbitration--order-api-notice', ['no' => $order->no ?? '', 'message' => $e->getMessage()]);
        } catch (Exception $e) {
            myLog('cancel-arbitration--order-api-notice', ['no' => $order->no ?? '', 'message' => $e->getMessage()]);
        }
    }
}

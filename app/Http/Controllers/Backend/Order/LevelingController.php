<?php

namespace App\Http\Controllers\Backend\Order;

use DB;
use Log;
use Excel;
use Auth;
use App\Models\Order;
use App\Models\OrderNotice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exceptions\OrderNoticeException;

/**
 * 订单报警
 */
class LevelingController extends Controller
{
    public function index(Request $request)
    {
    	try {       
	    	$startDate = $request->start_date;
	    	$endDate = $request->end_date;
	    	$fullUrl = $request->fullUrl();
	    	$third = $request->third;
	    	$filters = compact('third', 'startDate', 'endDate');
            $ourStatus = config('order.status_leveling');
	    	
	    	$query = OrderNotice::where('complete', 0)->filter($filters)->latest('updated_at');
            // 订单报警数据列表
	    	$paginateOrderNotices = $query->paginate(config('backend.page'));

	    	if ($request->export) {
	    		if ($paginateOrderNotices->count() < 1) {
	    			throw new OrderNoticeException('数据为空!');
	    		}
	            return $this->export($paginateOrderNotices->toArray()['data']);
	    	}
            // 特定的状态
            foreach ($ourStatus as $key => $status) {
                if (in_array($key, [1, 22, 24])) {
                    unset($ourStatus[$key]);
                }
            }
    	} catch (OrderNoticeException $e) {
    		Log::info($e->getMessage());
    	}
    	return view('backend.order.leveling.index', compact('startDate', 'endDate', 'fullUrl', 'third', 'ourStatus', 'paginateOrderNotices'));
    }

    /**
     * 导出
     * @param  [type] $paginateOrderNotices [description]
     * @return [type]                    [description]
     */
    public function export($paginateOrderNotices)
    {
    	$title = [
    		'订单号',
    		'千手状态',
    		'外部状态',
    		'接单平台',
            '接单平台操作',
    		'发布时间',
            '操作时间',
    	];

    	$chunkDatas = array_chunk($paginateOrderNotices, 50);

        Excel::create('订单报警', function ($excel) use ($chunkDatas, $title) {

            foreach ($chunkDatas as $chunkData) {
                // 内容
                $datas = [];
                foreach ($chunkData as $key => $data) {
                    $datas[] = [
                        $data['order_no'] ?? '--',
                        $data['status'] ? config("order.status_leveling")[$data['status']] : '--',
                        $data['child_third_status'] != 100 ? config("order.show91")[$data['third_status']].'('.config("order.show91")[$data['child_third_status']].')' : config("order.show91")[$data['third_status']],
                        $data['third'] ? config("order.third")[$data['third']] : '--',
                        $data['operate'] ?: '--',
                        $data['create_order_time'] ?? '--',
                        $data['created_at'] ?? '--',
                    ];
                }
                // 将标题加入到数组
                array_unshift($datas, $title);
                // 每页多少数据
                $excel->sheet("页数", function ($sheet) use ($datas) {
                    $sheet->rows($datas);
                });
            }
        })->export('xls');
    }

    /**
     * 后台订单报警，手动改订单状态,修改成功后，页面将不再显示修改成功的订单
     * 不能讲订单修改为，已撤销，已仲裁状态，因为接口回传代练费，回传双金，回传手续费需要找平台获取
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function changeStatus(Request $request)
    {
    	DB::beginTransaction();
    	try {
    		$order = Order::where('no', $request->orderNo)->first();

    		if (! $order) {
    			throw new OrderNoticeException('订单不存在!');
    		}
            //已撤销，已完成，已结算，强制撤销状态不允许手动改状态
    		if (in_array($request->status, [19, 20, 21, 23])) {
    			throw new OrderNoticeException('订单状态不可更改!');
    		}

    		if ($request->data) {
    			$data = $request->data;
    		} else {
    			$data = null;
    		}
            // 手动改状态
    		$order->handChangeStatus($request->status, Auth::user(), $data);
    	} catch (OrderNoticeException $e) {
    		DB::rollback();
    		Log::info($e->getMessage());
            return response()->ajax(0, $e->getMessage());
    	}
    	DB::commit();
    	return response()->ajax(1, '修改状态为【'.config('order.status_leveling')[$request->status].'】成功!');
    }

    /**
     * 软删除订单
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function destroy(Request $request)
    {
        $res = OrderNotice::destroy($request->orderId);

        if ($res) {
            return response()->ajax(1, '删除成功!');
        } else {
            return response()->ajax(0, '删除失败!');
        }
    }
}

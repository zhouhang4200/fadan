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

class LevelingController extends Controller
{
    public function index(Request $request )
    {
    	try {
	    	$startDate = $request->start_date;
	    	$endDate = $request->end_date;
	    	$fullUrl = $request->fullUrl();
	    	$third = $request->third;
	    	$filters = compact('third', 'startDate', 'endDate');
            $ourStatus = config('order.status_leveling');
	    	
	    	$query = OrderNotice::where('complete', 0)->filter($filters);
	    	$paginateOrderNotices = $query->with(['order' => function ($query) {
	    		$query->with(['orderDetails' => function ($query) {
	    			$query->where('field_name', 'security_deposit')->orwhere('field_name', 'efficiency_deposit');
	    		}]);
	    	}])->paginate(config('backend.page'));
	    	$excelOrderNotices = $query->get();

	    	if ($request->export) {
	    		if ($excelOrderNotices->count() < 1) {
	    			throw new OrderNoticeException('数据为空!');
	    		}
	            return $this->export($excelOrderNotices);
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

    public function export($excelOrderNotices)
    {
    	$title = [
    		'订单号',
    		'千手状态',
    		'外部状态',
    		'接单平台',
    		'发布时间',
    	];

    	$chunkDatas = array_chunk(array_reverse($excelOrderNotices->toArray()), 100);

        Excel::create('订单报警', function ($excel) use ($chunkDatas, $title, $totalData) {

            foreach ($chunkDatas as $chunkData) {
                // 内容
                $datas = [];
                foreach ($chunkData as $key => $data) {
                    $datas[] = [
                        $data['order_no'] ?? '--',
                        $data['status'] ? config("order.status_leveling")[$data['status']] : '--',
                        $data['third_status'] ? config("order.show91")[$data['third_status']] : '--',
                        $data['third'] ? config("order.third")[$data['third_status']] : '--',
                        $data['create_order_time'] ?? '--',
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

    public function changeStatus(Request $request)
    {
    	DB::beginTransaction();
    	try {
    		$order = Order::where('no', $request->orderNo)->first();

    		if (! $order) {
    			throw new OrderNoticeException('订单不存在!');
    		}

    		if (in_array($request->status, [19, 20, 21, 23])) {
    			throw new OrderNoticeException('订单状态不可更改!');
    		}

    		if ($request->data) {
    			$data = $request->data;
    		} else {
    			$data = null;
    		}
    		$order->handChangeStatus($request->status, Auth::user(), $data);
    	} catch (OrderNoticeException $e) {
    		DB::rollback();
    		return response()->ajax(0, $e->getMessage());
    		Log::info($e->getMessage());
    	}
    	DB::commit();
    	return response()->ajax(1, '修改状态为【'.config('order.status_leveling')[$request->status].'】成功!');
    }

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

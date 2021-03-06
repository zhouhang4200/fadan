<?php

namespace App\Http\Controllers\Backend\Order;

use DB;
use Log;
use Excel;
use Auth;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderNotice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Backend\OrderRepository;
use App\Exceptions\OrderNoticeException;
use App\Repositories\Frontend\GameRepository;
use App\Repositories\Backend\ServiceRepository;

/**
 * 订单报警
 */
class LevelingController extends Controller
{
    /**
     * OrderController constructor.
     * @param OrderRepository $orderRepository
     */
    public function __construct(OrderRepository $orderRepository)
    {
        $this->order = $orderRepository;
    }

    /**
     * @param Request $request
     * @param ServiceRepository $serviceRepository
     * @param GameRepository $gameRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, ServiceRepository $serviceRepository, GameRepository $gameRepository)
    {
        $startDate = $request->input('start_date', date('Y-m-d'));
        $endDate = $request->input('end_date');
        $source = $request->input('source');
        $status = $request->input('status');
        $serviceId = $request->input('service_id', 4);
        $gameId = $request->input('game_id');
        $creatorPrimaryUserId = $request->input('creator_primary_user_id');
        $gainerPrimaryUserId = $request->input('gainer_primary_user_id');
        $no = $request->input('no');
        $foreignOrderNo = $request->input('foreign_order_no');
        $thirdOrderNo = $request->input('third_order_no');
        $export = $request->input('export', 0);

        if ($request->gainer_primary_user_id) {
            $gainerPrimaryUserId = User::where('nickname', $request->gainer_primary_user_id)->value('id') ?: $request->gainer_primary_user_id;
        }

        $filters = compact('startDate', 'endDate', 'source', 'status', 'serviceId', 'gameId', 'creatorPrimaryUserId',
            'gainerPrimaryUserId', 'no', 'foreignOrderNo', 'thirdOrderNo');

        // 订单导出
        if ($export) {
            return $this->order->export($filters);
        }
        // 订单列表
        $orders = $this->order->dataList($filters);

        return view('backend.order.leveling.index')->with([
            'orders' => $orders,
            'services' => $serviceRepository->available(),
            'games' => $gameRepository->availableByServiceId(4),

            'startDate' => $startDate,
            'endDate' => $endDate,
            'source' => $source,
            'status' => $status,
            'serviceId' => $serviceId,
            'gameId' => $gameId,
            'creatorPrimaryUserId' => $creatorPrimaryUserId,
            'gainerPrimaryUserId' => $request->gainer_primary_user_id,
            'no' => $no,
            'thirdOrderNo' => $thirdOrderNo,
            'foreignOrderNo' => $foreignOrderNo,
            'fullUrl' => $request->fullUrl(),
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View|void
     */
    public function abnormal(Request $request)
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
	    	$paginateOrderNotices = $query->paginate(10);

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
    		myLog('admin-order-notice', ['message' => $e->getMessage()]);
    	}
        // 如果是ajax请求页面
        if ($request->ajax()) {
            return response()->json(view()->make('backend.order.leveling.list', [
                'paginateOrderNotices' => $paginateOrderNotices,
                'third' => $third,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'fullUrl' => $fullUrl,
                'ourStatus' => $ourStatus,
            ])->render());
        }
    	return view('backend.order.leveling.abnormal', compact('startDate', 'endDate', 'fullUrl', 'third', 'ourStatus', 'paginateOrderNotices'));
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
            '接单方操作',
            '发单方操作',
    		'发布时间',
            '操作时间',
    	];

    	$chunkDatas = array_chunk($paginateOrderNotices, 50);

        Excel::create('订单报警', function ($excel) use ($chunkDatas, $title) {

            foreach ($chunkDatas as $chunkData) {
                // 内容
                $datas = [];
                foreach ($chunkData as $key => $data) {
                    if ($data['third'] == 1) {
                        $thirdStatus = $data['child_third_status'] != 100 ? config("order.show91")[$data['third_status']].'('.config("order.show91")[$data['child_third_status']].')' : config("order.show91")[$data['third_status']];
                    } else {
                        $thirdStatus = $data['third_status'];
                    }
                    $datas[] = [
                        $data['order_no'] ?? '--',
                        $data['status'] ? config("order.status_leveling")[$data['status']] : '--',
                        $thirdStatus,
                        $data['third'] ? config("order.third")[$data['third']] : '--',
                        $data['operate'] ?: '--',
                        $data['our_operate'] ?: '--',
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
    		$order->handChangeStatus($request->status, auth('admin')->user(), $data);
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

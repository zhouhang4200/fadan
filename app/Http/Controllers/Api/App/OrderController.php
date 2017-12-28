<?php

namespace App\Http\Controllers\Api\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exceptions\CustomException;
use App\Repositories\Api\App\OrderRepository;
use Auth;
use Order;
use App\Extensions\Order\Operations\TurnBack;

class OrderController extends Controller
{
    /**
     * 订单列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $page = $request->params['page'] ?? 1;
        $perPage = $request->params['per_page'] ?? 20;
        $status = $request->params['status'] ?? 0;

        if (!in_array($status, [0, 3, 4, 5, 6, 7, 8])) {
            return response()->jsonReturn(0, '状态不允许');
        }

        $dataList = OrderRepository::dataList($status, $page, $perPage)->toArray();
        $data = [
            'current_page'   => $dataList['current_page'],
            'data'           => $dataList['data'],
            'last_page'      => $dataList['last_page'],
            'per_page'       => $dataList['per_page'],
            'total'          => $dataList['total'],
        ];

        return response()->jsonReturn(1, 'success', $data);
    }

    /**
     * 订单详情查看
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail(Request $request)
    {
        if (!isset($request->params['order_no'])) {
            return response()->jsonReturn(0, '参数不正确');
        }

        $order = OrderRepository::detail($request->params['order_no']);

        if (empty($order)) {
            return response()->jsonReturn(0, '订单不存在');
        }

        return response()->jsonReturn(1, 'success', ['order' => $order]);
    }

    /**
     * 退回订单集市
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function turnBack(Request $request)
    {
        $orderNo = $request->params['order_no'];
        $remark  = $request->params['remark'];

        try {
            // 调用退回
            Order::handle(new TurnBack($orderNo, Auth::guard('api')->user()->id, $remark));
        } catch (CustomException $e) {
            return response()->jsonReturn(0, $e->getMessage());
        }

        // 返回操作成功
        return response()->jsonReturn(1, 'success');
    }
}

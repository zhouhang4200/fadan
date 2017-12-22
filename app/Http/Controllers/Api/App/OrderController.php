<?php

namespace App\Http\Controllers\Api\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Api\App\OrderRepository;

class OrderController extends Controller
{
    /**
     * @param Request $request
     * @param OrderRepository $orderRepository
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, OrderRepository $orderRepository)
    {
        $page = $request->params['page'] ?? 1;
        $perPage = $request->params['perPage'] ?? 20;
        $status = $request->params['status'] ?? 3;
        if (!in_array($status, [3, 4, 5, 6, 7, 8])) {
            return response()->jsonReturn(0, '状态不允许');
        }

        $dataList = $orderRepository->dataList($status, $page, $perPage)->toArray();
        $data = [
            'current_page'   => $dataList['current_page'],
            'data'           => $dataList['data'],
            'last_page'      => $dataList['last_page'],
            'per_page'       => $dataList['per_page'],
            'total'          => $dataList['total'],
        ];

        return response()->jsonReturn(1, 'success', $data);
    }
}

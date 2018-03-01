<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * 房卡充值
 * Class RoomCardRecharge
 * @package App\Http\Controllers\Api
 */
class RoomCardRecharge extends Controller
{
    /**
     * 获取订单
     * @param Request $request
     */
    public function index(Request $request)
    {

    }

    /**
     * 更新状态
     * @param Request $request
     */
    public function update(Request $request)
    {
        // 1 成功 2 失败
        if(in_array($request->status, [1, 2]) && strlen($request->no) == 22) {
            if ($request->status == 1) {

            } elseif ($request->status == 2) {

            }
        }
    }
}

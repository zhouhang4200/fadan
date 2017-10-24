<?php
namespace App\Http\Controllers\Frontend\Workbench;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

/**
 * Class OrderOperationController
 * @package App\Http\Controllers\Frontend\Workbench
 */
class OrderOperationController extends Controller
{
    /**
     * 接单
     * @param $orderNo
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function receiving($orderNo)
    {
        // 获取当前用户ID
        $currentUserId = Auth::user()->id;
        // 获取主账号
        $primaryUserId = Auth::user()->getPrimaryUserId();
        // 检测是否已接单
        if (receivingRecordExist($primaryUserId, $orderNo)) {
            // 提示用户：您已经接过该单
            return response()->ajax(0, '您已经接过该单');
        }
        // 接单后，将当前接单用户的ID写入相关的订单号的队列中
        receiving($currentUserId, $orderNo);
        // 接单成功，将主账号ID与订单关联写入redis 防止用户多次接单
        receivingRecord($primaryUserId, $orderNo);
        // 提示用户：接单成功等待系统分配
        return response()->ajax();
    }

    /**
     * 退回订单集市
     * @param $orderNo
     */
    public function return($orderNo)
    {

    }

}


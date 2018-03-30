<?php

namespace App\Http\Controllers\Backend\Finance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Backend\UserRechargeOrderRepository;
use App\Models\UserWithdrawOrder;
use Asset;
use Auth;
use App\Exceptions\CustomException as Exception;
use App\Extensions\Asset\Unfreeze;

// 加款单
class UserRechargeOrderController extends Controller
{
    public function index(Request $request)
    {
        $userId    = $request->user_id;
        $timeStart = $request->time_start;
        $timeEnd   = $request->time_end;
        $no        = $request->no;
        $type      = $request->type;

        $dataList = UserRechargeOrderRepository::getList($timeStart, $timeEnd, $userId, $no, $type);
        $config = config('addmoney');

        return view('backend.finance.user-recharge-order.index', compact('dataList', 'userId', 'timeStart', 'timeEnd', 'no', 'type', 'config'));
    }
}

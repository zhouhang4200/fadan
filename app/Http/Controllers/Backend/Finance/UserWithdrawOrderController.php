<?php

namespace App\Http\Controllers\Backend\Finance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Backend\UserWithdrawOrderRepository;
use App\Models\UserWithdrawOrder;
use Asset;
use Auth;
use App\Exceptions\CustomException as Exception;
use App\Extensions\Asset\Unfreeze;

class UserWithdrawOrderController extends Controller
{
    public function index(Request $request, UserWithdrawOrderRepository $userWithdrawRepository)
    {
        $userId    = $request->user_id;
        $timeStart = $request->time_start;
        $timeEnd   = $request->time_end;
        $no        = $request->no;
        $status    = $request->status;
        $adminRemark    = $request->admin_remark;

        if ($request->export == 1) {
            $userWithdrawRepository->export($timeStart, $timeEnd, $userId, $no, $status, $adminRemark);
        }

        $dataList = $userWithdrawRepository->getList($timeStart, $timeEnd, $userId, $no, $status, $adminRemark);

        return view('backend.finance.user-withdraw-order.index', compact('dataList', 'userId', 'timeStart', 'timeEnd', 'no', 'status', 'adminRemark'));
    }

    public function complete(UserWithdrawOrder $userWithdrawOrder, Request $request)
    {
        try {
            $userWithdrawOrder->complete($request->remark);
        }
        catch (Exception $e) {
            return response()->ajax(0, $e->getMessage());
        }

        return response()->ajax();
    }

    public function refuse(UserWithdrawOrder $userWithdrawOrder)
    {
        try {
            $userWithdrawOrder->refuse();
        }
        catch (Exception $e) {
            return response()->ajax(0, $e->getMessage());
        }

        return response()->ajax();
    }
}

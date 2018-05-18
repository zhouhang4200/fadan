<?php

namespace App\Http\Controllers\Frontend\Finance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Repositories\Frontend\UserWithdrawOrderRepository;
use App\Exceptions\CustomException as Exception;
use App\Events\Punish;

class WithdrawOrderController extends Controller
{
    public function index(Request $request, UserWithdrawOrderRepository $repository)
    {
        $timeStart = $request->time_start;
        $timeEnd   = $request->time_end;
        $status    = $request->status;

        $dataList = $repository->getList($timeStart, $timeEnd, $status);

        return view('frontend.v1.finance.withdraw.index', compact('dataList', 'timeStart', 'timeEnd', 'status'));
    }

    public function store(Request $request, UserWithdrawOrderRepository $repository)
    {
        $bool = event(new Punish(Auth::user()->getPrimaryUserId()));

        if ($bool) {
            return response()->json(['status' => 0, 'message' => '您还有罚单没有交清，请先交清罚单哦!']);
        }

        $this->validate($request, [
            'fee'    => 'bail|required|numeric|min:1',
            'remark' => 'string|nullable|max:100',
        ], [
            'fee.required' => '请填写金额',
            'fee.numeric'  => '金额必须是数字',
            'fee.min'      => '最少提现1元',
            'remark.max'   => '备注不能超过100个字',
        ]);

        try {
            $repository->store($request->fee, trim($request->remark) ?: config('withdraw.status')[1]);
        }
        catch (Exception $e) {
            return response()->ajax(0, $e->getMessage());
        }

        return response()->ajax();
    }
}

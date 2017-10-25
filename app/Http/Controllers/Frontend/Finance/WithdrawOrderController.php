<?php

namespace App\Http\Controllers\Frontend\Finance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Repositories\Frontend\UserWithdrawOrderRepository;
use App\Exceptions\CustomException as Exception;

class WithdrawOrderController extends Controller
{
    public function index(Request $request, UserWithdrawOrderRepository $repository)
    {
        $timeStart = $request->time_start;
        $timeEnd   = $request->time_end;
        $status    = $request->status;

        $dataList = $repository->getList($timeStart, $timeEnd, $status);

        return view('frontend.finance.withdraw.index', compact('dataList', 'timeStart', 'timeEnd', 'status'));
    }

    public function store(Request $request, UserWithdrawOrderRepository $repository)
    {
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
            $repository->store($request->fee, trim($request->remark));
        }
        catch (Exception $e) {
            return response()->ajax(0, $e->getMessage());
        }

        return response()->ajax();
    }
}

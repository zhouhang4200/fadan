<?php

namespace App\Http\Controllers\Backend\Finance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exceptions\CustomException;
use App\Repositories\Backend\DepositRepository;

// 押金管理
class DepositController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $dataList = DepositRepository::getList($request->no, $request->user_id, $request->type, $request->status);
        $config = config('deposit');

        return view('backend.finance.deposit.index', compact('dataList', 'config'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'bail|required|integer|min:0',
            'type'    => 'bail|required|integer|min:0',
            'amount'  => 'bail|required|numeric|min:0',
            'remark'  => 'bail|nullable|string|max:200',
        ]);

        try {
            DepositRepository::store($request->user_id, $request->type, $request->amount, $request->remark);
        } catch (CustomException $e) {
            return response()->ajax(0, $e->getMessage());
        }

        return response()->ajax(1);
    }

    // 扣款审核
    public function deductAudit($id)
    {
        try {
            DepositRepository::deductAudite($id);
        } catch(CustomException $e) {
            return response()->ajax(0, $e->getMessage());
        }

        return response()->ajax(1);
    }

    // 退押金
    public function refund($id)
    {
        try {
            DepositRepository::refund($id);
        } catch(CustomException $e) {
            return response()->ajax(0, $e->getMessage());
        }

        return response()->ajax(1);
    }

    // 退押金审核
    public function refundAudit($id)
    {
        try {
            DepositRepository::refundAudit($id);
        } catch(CustomException $e) {
            return response()->ajax(0, $e->getMessage());
        }

        return response()->ajax(1);
    }

    // 取消扣款
    public function deductCancel($id)
    {
        try {
            DepositRepository::deductCancel($id);
        } catch(CustomException $e) {
            return response()->ajax(0, $e->getMessage());
        }

        return response()->ajax(1);
    }

    // 取消退款
    public function refundCancel($id)
    {
        try {
            DepositRepository::refundCancel($id);
        } catch(CustomException $e) {
            return response()->ajax(0, $e->getMessage());
        }

        return response()->ajax(1);
    }
}

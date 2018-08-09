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
use Storage;

class UserWithdrawOrderController extends Controller
{
    public function index(Request $request, UserWithdrawOrderRepository $userWithdrawRepository)
    {
        if ($request->export == 1) {
            $userWithdrawRepository->export(
                $request->time_start,
                $request->time_end,
                $request->user_id,
                $request->no,
                $request->type,
                $request->status,
                $request->admin_remark
            );
        }

        $dataList = $userWithdrawRepository->getList(
            $request->time_start,
            $request->time_end,
            $request->user_id,
            $request->no,
            $request->type,
            $request->status,
            $request->admin_remark
        );

        $config = config('withdraw');

        return view('backend.finance.user-withdraw-order.index', compact('dataList', 'config'));
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

    // 设置已发邮件
    public function setSendEmail(Request $request)
    {
        try {
            UserWithdrawOrderRepository::setStatus($request->id, 1, 4);
        }
        catch (Exception $e) {
            return response()->ajax(0, $e->getMessage());
        }

        return response()->ajax();
    }

    // 上传附件
    public function upload(Request $request)
    {
        if (!$request->file('image')->isValid()) {
            return response()->ajax(0, '上传失败');
        }

        $diskPath = $request->file('image')->store('withdraw');

        try {
            \DB::beginTransaction();
            UserWithdrawOrderRepository::setStatus($request->id, 4, 5);
            UserWithdrawOrderRepository::setAttach($request->id, $diskPath);
            \DB::commit();
        }
        catch (CustomException $e) {
            Storage::delete($diskPath); // 删除图片
            return response()->ajax(0, $e->getMessage());
        }

        return response()->ajax(1);
    }

    // 获取图片
    public function attach(Request $request)
    {
        $path = Storage::path($request->attach);
        return response()->file($path);
    }

    // 自动办款
    public function auto(Request $request)
    {
        try {
            UserWithdrawOrderRepository::auto($request->id, $request->remark);
        } catch (Exception $e) {
            return response()->ajax(0, $e->getMessage());
        }

        return response()->ajax();
    }
}

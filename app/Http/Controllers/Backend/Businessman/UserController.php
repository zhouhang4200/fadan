<?php

namespace App\Http\Controllers\Backend\Businessman;

use App\Exceptions\AssetException;
use App\Exceptions\CustomException;
use App\Extensions\Asset\Consume;
use App\Models\CautionMoney;
use App\Models\RealNameIdent;
use App\Models\UserTransferAccountInfo;
use App\Repositories\Api\UserRechargeOrderRepository;
use Illuminate\Http\Request;

use Asset, Auth, View, DB;
use App\Models\User;
use App\Extensions\Asset\Recharge;
use App\Http\Controllers\Controller;

/**
 * 前台 用户管理
 */
class UserController extends Controller
{

    /**
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        $id = $request->id;
        $name = $request->name;
        $nickname = $request->nickname;

    	$users = User::where('parent_id', 0)->filter(['id' => $id, 'name' => $name, 'nickname' => $nickname])
            ->with('asset')
            ->orderBy('id', 'desc')
            ->paginate(config('frontend.page'));

    	return view('backend.businessman.index', compact('users', 'name', 'nickname', 'id'));
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        return view('backend.businessman.show')->with([
            'user' => User::find($id),
        ]);
    }

    /**
     * @param Request $request
     */
    public function edit(Request $request)
    {
        try {
            User::where('id', $request->id)->update([
                'type' => $request->type,
                'nickname' => $request->nickname,
                'remark' => $request->remark,
            ]);
            return response()->ajax(1, '更新成功');
        } catch (CustomException $exception) {
            return response()->ajax(1, $exception->getMessage());
        }
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function authentication($userId)
    {
        return view('backend.businessman.authentication')->with([
            'authentication' => RealNameIdent::where('user_id', $userId)->first(),
        ]);
    }

    /**
     * @param Request $request
     * @param UserRechargeOrderRepository $userRechargeOrderRepository
     * @return mixed
     */
    public function recharge(Request $request, UserRechargeOrderRepository $userRechargeOrderRepository)
    {
        try {
            $userRechargeOrderRepository->store($request->amount, $request->id, '手动加款', generateOrderNo(), '', false);
            return response()->ajax(1, '加款成功');
        } catch (CustomException $exception) {
            return response()->ajax(0, $exception->getMessage());
        }
    }

    /**
     * 商户对应的转账信息
     * 商户转账到指定账号后自动加款
     * @param Request $request
     * @return View
     */
    public function transferAccountInfo(Request $request, $userId)
    {
        $transferInfo = UserTransferAccountInfo::where('user_id', $userId)->first();

        return view('backend.businessman.transfer-account-info', compact('transferInfo'));
    }

    /**
     * 更新商户的转账信息
     * @param Request $request
     * @return mixed
     */
    public function transferAccountInfoUpdate(Request $request)
    {
        UserTransferAccountInfo::updateOrCreate(['user_id' =>  $request->id], [
           'user_id' => $request->id,
           'name' => $request->name,
           'bank_name' => $request->bank_name,
           'bank_card' => $request->bank_card,
           'admin_user_id' => Auth::user()->id,
        ]);
        return response()->ajax(1, '修改成功');
    }

    /**
     * 扣保证金
     * @param Request $request
     */
    public function cautionMoney(Request $request)
    {
        $exist = CautionMoney::where('user_id', $request->user_id)
            ->where('type', $request->type)
            ->where('status', 1)
            ->first();
        if ($exist) {
            return response()->ajax(0, '该商户已经扣过保证金');
        }
        DB::beginTransaction();
        try {
            $no = generateOrderNo();
            Asset::handle(new Consume($request->amount, 5, $no, config('cautionmoney.type')[$request->type], $request->user_id, Auth::user()->id));

            CautionMoney::create([
               'no' => $no,
               'user_id' => $request->user_id,
               'amount' => $request->amount,
               'type' => $request->type,
            ]);
        } catch (CustomException $customException) {
            DB::rollback();
            return response()->ajax(0, '扣款失败');
        } catch (AssetException $assetException) {
            DB::rollback();
            return response()->ajax(0, $assetException->getMessage());
        }
        DB::commit();
        return response()->ajax(1, '扣款成功');
    }
}

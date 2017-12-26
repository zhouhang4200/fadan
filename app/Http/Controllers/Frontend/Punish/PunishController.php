<?php

namespace App\Http\Controllers\Frontend\Punish;

use Auth;
use Asset;
use App\Models\User;
use App\Models\PunishOrReward;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Extensions\Asset\Consume;
use App\Extensions\Asset\Expend;
use App\Extensions\Asset\Income;
use App\Http\Controllers\Controller;
use App\Exceptions\CustomException as Exception;
use DB;

class PunishController extends Controller
{
    /**
     * 奖惩列表
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function index(Request $request)
    {
    	$startDate = $request->startDate;
    	$endDate = $request->endDate;
    	$type = $request->type;
        $status = $request->status;
        // 生成数组
    	$filters = compact('startDate', 'endDate', 'type', 'status');

    	$punishes = PunishOrReward::homeFilter($filters)->where('user_id', Auth::id())->paginate(config('frontend.page'));

        $punishType = config('punish.type');
        $punishStatus = config('punish.status');

    	return view('frontend.punish.index', compact('startDate', 'endDate', 'punishes', 'type', 'status', 'punishType', 'punishStatus'));
    }

    /**
     * 确认扣款
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function payment(Request $request)
    {
    	try {
            DB::beginTransaction();
            $punish = PunishOrReward::lockForUpdate()->find($request->id);
            if ($punish->confirm == 1) {
                throw new Exception('该单据已处理过');
            }

            switch ($punish->type) {
                case 2:
                    $hasMoney = User::find($punish->user_id)->userAsset ? User::find($punish->user_id)->userAsset->balance : 0;

                    if ($hasMoney <= 0) {
                        throw new Exception('账户余额不足，请充值!');
                    }

        		    Asset::handle(new Consume($punish->sub_money, 2, $punish->no, '违规扣款', $punish->user_id));

                    $punish->status = 2;
                    $punish->confirm = 1;
                    if (!$punish->save()) {
                        DB::rollback();
                        throw new Exception('操作失败');
                    }

                    // 写多态关联
                    if (!$punish->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                        DB::rollback();
                        throw new Exception('操作失败');
                    }

                    if (!$punish->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                        DB::rollback();
                        throw new Exception('操作失败');
                    }

                    $message = '已交罚款'. number_format($punish->sub_money, 2) .',请到个人资金流水查看扣款记录!';
                    break;
                case 4: // 减权重
                    $punish->status = 2;
                    $punish->confirm = 1;
                    if (!$punish->save()) {
                        DB::rollback();
                        throw new Exception('操作失败');
                    }

                    $message = '已确认减少权重!';
                    break;
                case 5: // 禁止接单
                    $punish->confirm = 1;
                    if (!$punish->save()) {
                        DB::rollback();
                        throw new Exception('操作失败');
                    }
                    $message = '已确认停止接单一天!';
                    break;
                case 6: // 订单退款
                    $order = Order::where('no', $punish->order_no)->first();
                    if (empty($order)) {
                        throw new Exception('订单不存在');
                    }

                    if ($punish->sub_money <= 0) {
                        throw new Exception('金额不正确');
                    }

                    $punish->status = 2;
                    $punish->confirm = 1;
                    if (!$punish->save()) {
                        DB::rollback();
                        throw new Exception('操作失败');
                    }

                    Asset::handle(new Expend($punish->sub_money, 2, $order->no, '订单售后扣款', $punish->user_id));
                    // 写多态关联
                    if (!$punish->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                        DB::rollback();
                        throw new Exception('操作失败');
                    }

                    if (!$punish->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                        DB::rollback();
                        throw new Exception('操作失败');
                    }

                    Asset::handle(new Income($punish->sub_money, 5, $order->no, '订单售后退款', $order->creator_primary_user_id));
                    // 写多态关联
                    if (!$punish->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                        DB::rollback();
                        throw new Exception('操作失败');
                    }

                    if (!$punish->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                        DB::rollback();
                        throw new Exception('操作失败');
                    }

                    $message = '操作成功';
                    break;
                case 1:
                case 3:
                    $punish->confirm = 1;
                    if (!$punish->save()) {
                        DB::rollback();
                        throw new Exception('操作失败');
                    }
                    $message = '已确认奖励!';
                    break;
                default: // 其他奖励
                    DB::rollback();
                    throw new Exception('类型错误');
                    break;
            }

            DB::commit();
            return response()->ajax(1, $message);
        } catch (Exception $exception) {
            return response()->ajax(0, $exception->getMessage());
        }
    }

    /**
     * 申诉操作
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function complain(Request $request)
    {
        try {
            $punishOrReward = PunishOrReward::find($request->id);

            if (empty($punishOrReward)) {
                throw new Exception('申诉单不存在');
            }

            if (!in_array($punishOrReward->type, [2, 4])) {
                throw new Exception('当前单据不能申诉');
            }

            if ($punishOrReward->status != 1) {
                throw new Exception('当前状态不能申诉');
            }

            $punishOrReward->status = 9;
            if (!$punishOrReward->save()) {
                throw new Exception('申诉失败');
            }

            return response()->ajax(1);

        } catch (Exception $exception) {
            return response()->ajax(0, $exception->getMessage());
        }
    }
}

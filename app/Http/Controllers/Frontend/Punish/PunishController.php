<?php

namespace App\Http\Controllers\Frontend\Punish;

use Auth;
use Asset;
use App\Models\User;
use App\Models\PunishOrReward;
use Illuminate\Http\Request;
use App\Extensions\Asset\Consume;
use App\Http\Controllers\Controller;

class PunishController extends Controller
{
    public function index(Request $request) 
    {
    	$startDate = $request->startDate;
    	$endDate = $request->endDate;
    	$type = $request->type;
        $status = $request->status;

    	$filters = compact('startDate', 'endDate', 'type', 'status');

    	$punishes = PunishOrReward::homeFilter($filters)->where('user_id', Auth::id())->paginate(config('frontend.page'));

    	return view('frontend.punish.index', compact('startDate', 'endDate', 'punishes', 'type', 'status'));
    }

    public function payment(Request $request)
    {
    	try {
            $punish = PunishOrReward::find($request->id);

            if ($punish->type == 2) {

                $hasMoney = User::find($punish->user_id)->userAsset ? User::find($punish->user_id)->userAsset->balance : 0;

                if ($hasMoney <= 0) {
                    return response()->json(['code' => 2, 'message' => '账户余额不足，请充值!']);
                }

    		    $bool = Asset::handle(new Consume($punish->sub_money, 2, $punish->order_no, '违规扣款', $punish->user_id));

                if ($bool) {
                    PunishOrReward::where('id', $request->id)->update(['status' => 4, 'confirm' => 1]);
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

                return response()->json(['code' => 1, 'message' => '已交罚款'. number_format($punish->sub_money, 2) .',请到个人资金流水查看扣款记录!']);

            } elseif ($punish->type == 4) {

                PunishOrReward::where('id', $request->id)->update(['status' => 8, 'confirm' => 1]);

                return response()->json(['code' => 1, 'message' => '已确认减少权重!']);

            } elseif ($punish->type == 5) {

                PunishOrReward::where('id', $request->id)->update(['confirm' => 1]);

                return response()->json(['code' => 1, 'message' => '已确认停止接单一天!']);

            } else {
                PunishOrReward::where('id', $request->id)->update(['confirm' => 1]);

                return response()->json(['code' => 1, 'message' => '已确认奖励!']);
            }

        } catch (Exception $exception) {

            
        }
    }

    public function complain(Request $request)
    {
        try {
            $punish = PunishOrReward::where('id', $request->id)->update(['status' => 9]);

            return response()->json(['code' => 1, 'message' => '申诉成功!']);

        } catch (Exception $exception) {

            
        }
    }
}

<?php

namespace App\Http\Controllers\Backend\Punish;

use Redis;
use Asset;
use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\UserWeight;
use Illuminate\Http\Request;
use App\Models\PunishOrReward;
use App\Extensions\Asset\Refund;
use App\Extensions\Asset\Consume;
use App\Extensions\Asset\Recharge;
use App\Http\Controllers\Controller;

class ExecuteController extends Controller
{
	/**
	 * 惩罚扣钱
	 * @param  Request $request [description]
	 * @return json
	 */
    public function subMoney(Request $request)
    {
    	try {
	        $data['type'] = 2;
	        $data['status'] = 3;
	    	$data['sub_money'] = $request->data['money'];
	    	$data['remark'] = $request->data['remark'];
	        $data['order_no'] = static::createOrderId();
	    	$data['order_id'] = $request->data['order_id'];
	    	$order = Order::where('no', $data['order_id'])->first();
	    	$data['user_id'] = $order->creator_primary_user_id;
	        $data['deadline'] = Carbon::now()->addDays(1)->startOfDay()->addHours(18)->toDateTimeString();
	    	$data['voucher'] = $request->data['voucher'] ?? '';

	    	if (! $order) {

	    		return response()->json(['code' => 0, 'message' => '订单不存在!']);
	    	}
	        $punish = PunishOrReward::create($data);

	        if ($punish) {

	        	return response()->json(['code' => 1, 'message' => '记录写入成功!']);
	        }
	        return response()->json(['code' => 0, 'message' => '记录写入失败!']);

    	} catch (Exception $e) {
    		
    	}
    }

    /**
     * 获取订单号
     * @return string
     */
    public static function createOrderId()
    {
        // 14位长度当前的时间 20150709105750
        $orderdate = date('YmdHis');
        // 今日订单数量
        $orderquantity = Redis::incr('market:order:punish:' . date('Ymd'));

        return $orderdate . str_pad($orderquantity, 9, 0, STR_PAD_LEFT);
    }

    /**
     * 奖励加钱
     * @param  Request $request [description]
     * @return json
     */
    public function addMoney(Request $request)
    {
    	try {
	        $data['type'] = 1;
	        $data['status'] = 1;
	    	$data['add_money'] = $request->data['money'];
	    	$data['remark'] = $request->data['remark'];
	        $data['order_no'] = static::createOrderId();
	    	$data['order_id'] = $request->data['order_id'];
	    	$order = Order::where('no', $data['order_id'])->first();
	    	$data['user_id'] = $order->creator_primary_user_id;
	    	$data['voucher'] = $request->data['voucher'] ?? '';

	    	if (! $order) {
	    		return response()->json(['code' => 0, 'message' => '订单不存在!']);
	    	}

	        $punish = PunishOrReward::create($data);

	        if ($punish) {

	        	$bool = Asset::handle(new Recharge($punish->add_money, 3, $punish->order_no, '奖励加款', $punish->user_id));

	        	if ($bool) {

	        		$punish->status = 2;

	        		$punish->save();
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
	        	return response()->json(['code' => 1, 'message' => '记录写入成功并奖励加款!']);
	        }
	        return response()->json(['code' => 0, 'message' => '记录写入失败!']);

    	} catch (Exception $e) {
    		
    	}
    }

     /**
     * 加权重
     * @param  Request $request [description]
     * @return json
     */
    public function addWeight(Request $request)
    {
    	try {
	        $data['type'] = 3;
	        $data['status'] = 6;
	    	$data['ratio'] = $request->data['ratio'] ?? 0;
	    	$data['remark'] = $request->data['remark'];
	        $data['order_no'] = static::createOrderId();
	    	$data['order_id'] = $request->data['order_id'];
	    	$order = Order::where('no', $data['order_id'])->first();
	    	$data['user_id'] = $order->creator_primary_user_id;
	    	$data['before_weight_value']= UserWeight::where('user_id', $data['user_id'])->value('weight') ?? 0;
	    	$data['after_weight_value'] = round($data['before_weight_value'] + bcmul($data['before_weight_value'], bcdiv($request->data['ratio'], 100)));
	    	$data['start_time'] = $request->data['start_time'] ?? '';
	    	$data['end_time'] = isset($request->data['end_time']) ? $request->data['end_time'] . ' 23:59:59' : '';
	    	$data['voucher'] = $request->data['voucher'] ?? '';

	    	if (! $order) {
	    		return response()->json(['code' => 0, 'message' => '订单不存在!']);
	    	}
	        $punish = PunishOrReward::create($data);

	        if ($punish) {
	        	
	        	return response()->json(['code' => 1, 'message' => '记录写入成功并已给用户增加权重!']);
	        }
	        return response()->json(['code' => 0, 'message' => '记录写入失败!']);

    	} catch (Exception $e) {
    		
    	}
    }

    /**
     * 减权重
     * @param  Request $request [description]
     * @return json
     */
    public function subWeight(Request $request)
    {
    	try {
	        $data['type'] = 4;
	        $data['status'] = 7;
	    	$data['ratio'] = $request->data['ratio'] ?? 0;
	    	$data['remark'] = $request->data['remark'];
	        $data['order_no'] = static::createOrderId();
	    	$data['order_id'] = $request->data['order_id'];
	    	$order = Order::where('no', $data['order_id'])->first();
	    	$data['user_id'] = $order->creator_primary_user_id;
	    	$data['before_weight_value'] = UserWeight::where('user_id', $data['user_id'])->value('weight') ?? 0;
	    	$data['after_weight_value'] = round($data['before_weight_value'] + bcmul($data['before_weight_value'], bcdiv($request->data['ratio'], 100)));
	    	$data['start_time'] = $request->data['start_time'] ?? '';
	    	$data['end_time'] = isset($request->data['end_time']) ? $request->data['end_time'] . ' 23:59:59' : '';
	    	$data['voucher'] = $request->data['voucher'] ?? '';
	    	$data['deadline'] = Carbon::now()->addDays(1)->startOfDay()->addHours(18)->toDateTimeString();

	    	if (! $order) {
	    		return response()->json(['code' => 0, 'message' => '订单不存在!']);
	    	}
	        $punish = PunishOrReward::create($data);

	        if ($punish) {
	        	
	        	return response()->json(['code' => 1, 'message' => '记录写入成功!']);
	        }
	        return response()->json(['code' => 0, 'message' => '记录写入失败!']);

    	} catch (Exception $e) {
    		
    	}
    }

    /**
     * 禁止接单一天
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function forbidden(Request $request)
    {
    	try {
    		$data['type'] = 5;
	        $data['status'] = 0;
    		$data['order_no'] = static::createOrderId();
	    	$data['order_id'] = $request->data['order_id'];
	    	$order = Order::where('no', $data['order_id'])->first();
	    	$data['user_id'] = $order->creator_primary_user_id;
	    	$data['deadline'] = Carbon::now()->addDays(1)->endOfDay()->toDateTimeString();
	    	$data['remark'] = $request->data['remark'];
	    	$data['voucher'] = $request->data['voucher'] ?? '';
	    	$data['confirm'] = 1;

	    	if (! $order) {
	    		return response()->json(['code' => 0, 'message' => '订单不存在!']);
	    	}
	        $punish = PunishOrReward::create($data);

	        if ($punish) {
	        	
	        	return response()->json(['code' => 1, 'message' => '记录写入成功!']);
	        }
	        return response()->json(['code' => 0, 'message' => '记录写入失败!']);

    	} catch (Exception $e) {

    	}
    }

    /**
     * 同意申诉
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function pass(Request $request)
    {
    	try {
	    	PunishOrReward::where('id', $request->data['id'])->update(['status' => 11]);

	    	$punish = PunishOrReward::find($request->data['id']);
	    	// 软删除
	    	$punish->delete(); 	

	    	return response()->json(['code' => 1, 'message' => '同意申诉!']);

	    } catch (Exception $e) {

	    }
    }

    /**
     * 驳回申诉
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function refuse(Request $request) 
    {
    	try {

	    	$punish = PunishOrReward::find($request->data['id']);

	    	// 如果是罚款则我们主动罚款
	    	if ($punish->type == 2 && in_array($punish->status, ['3', '9'])) {
	    		// 检查账户余额
	    		$hasMoney = User::find($punish->user_id)->userAsset ? User::find($punish->user_id)->userAsset->balance : 0;

                if ($hasMoney <= 0) {
                    return response()->json(['code' => 1, 'message' => '账户余额不足，提醒他充值!']);
                }

	    		$bool = Asset::handle(new Consume($punish->sub_money, 2, $punish->order_no, '违规扣款', $punish->user_id));

                if ($bool) {
                    PunishOrReward::where('id', $request->data['id'])->update(['status' => 10, 'confirm' => 1]);
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
                return response()->json(['code' => 1, 'message' => '申诉驳回，并已对该商家罚款' . number_format($punish->sub_money, 2) . '元!']);

	    	} elseif ($punish->type == 4 && in_array($punish->status, ['7', '9'])) {

	    		PunishOrReward::where('id', $request->data['id'])->update(['status' => 10, 'confirm' => 1]);

                return response()->json(['code' => 1, 'message' => '申诉驳回，并已对商家进行权重处罚!']);
	    	}

	    	return response()->json(['code' => 1, 'message' => '驳回申诉!']);
    		
    	} catch (Exception $e) {

    	}
    }
}

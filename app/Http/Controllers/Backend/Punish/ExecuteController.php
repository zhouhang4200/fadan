<?php

namespace App\Http\Controllers\Backend\Punish;

use DB;
use Auth;
use Redis;
use Asset;
use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\AdminUser;
use App\Models\UserWeight;
use Illuminate\Http\Request;
use App\Models\PunishOrReward;
use App\Extensions\Asset\Refund;
use App\Extensions\Asset\Consume;
use App\Extensions\Asset\Recharge;
use App\Http\Controllers\Controller;

/**
 * 后台订单列表里面奖惩操作
 */
class ExecuteController extends Controller
{
	/**
	 * 罚款
	 * @param  Request $request [description]
	 * @return json
	 */
    public function subMoney(Request $request)
    {
    	try {
	        $data['type'] = 2;
	        $data['status'] = 1;
	    	$data['sub_money'] = $request->data['money'];
	    	$data['remark'] = $request->data['remark'];
	        $data['no'] = static::createOrderId();
	    	$data['order_no'] = $request->data['order_no'];
	    	$order = Order::where('no', $data['order_no'])->first();
	    	$data['user_id'] = $order->creator_primary_user_id;
	        $data['deadline'] = Carbon::now()->addDays(1)->startOfDay()->addHours(18)->toDateTimeString();
	    	$data['voucher'] = $request->data['voucher'] ?? '';

            if ($request->data['money'] == 'original_amount') {
                $data['sub_money'] = bcmul($order->original_amount, 0.01);
            }
            // 判断订单是否存在
	    	if (! $order) {
	    		return response()->json(['code' => 2, 'message' => '订单不存在!']);
	    	}
            // 奖惩记录
	        $punish = PunishOrReward::create($data);

	        if ($punish) {
	        	return response()->json(['code' => 1, 'message' => '成功创建一条罚单!']);
	        }
	        return response()->json(['code' => 2, 'message' => '罚单创建失败!']);
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

        return $orderdate . str_pad($orderquantity, 8, 0, STR_PAD_LEFT);
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
	        $data['no'] = static::createOrderId();
	    	$data['order_no'] = $request->data['order_no'];
	    	$order = Order::where('no', $data['order_no'])->first();
	    	$data['user_id'] = $order->creator_primary_user_id;
	    	$data['voucher'] = $request->data['voucher'] ?? '';
            // 判断订单存不存在
	    	if (! $order) {
	    		return response()->json(['code' => 2, 'message' => '订单不存在!']);
	    	}
            // 奖惩记录
	        $punish = PunishOrReward::create($data);

	        if ($punish) {
	        	$bool = Asset::handle(new Recharge($punish->add_money, 3, $punish->no, '奖励加款', $punish->user_id));
                // 状态改为已加钱
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
	        return response()->json(['code' => 2, 'message' => '记录写入失败!']);

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
	        $data['status'] = 2;
	    	$data['ratio'] = $request->data['ratio'] ?? 0;
	    	$data['remark'] = $request->data['remark'];
	        $data['no'] = static::createOrderId();
	    	$data['order_no'] = $request->data['order_no'];
	    	$order = Order::where('no', $data['order_no'])->first();
	    	$data['user_id'] = $order->creator_primary_user_id;
	    	$data['before_weight_value']= UserWeight::where('user_id', $data['user_id'])->value('weight') ?? 0;
	    	$data['after_weight_value'] = round($data['before_weight_value'] + bcmul($data['before_weight_value'], bcdiv($request->data['ratio'], 100)));
	    	$data['start_time'] = $request->data['start_time'] ?? '';
	    	$data['end_time'] = isset($request->data['end_time']) ? $request->data['end_time'] . ' 23:59:59' : '';
	    	$data['voucher'] = $request->data['voucher'] ?? '';
            // 判断订单存不存在
	    	if (! $order) {
	    		return response()->json(['code' => 2, 'message' => '订单不存在!']);
	    	}
            // 奖惩记录
	        $punish = PunishOrReward::create($data);

	        if ($punish) {
	        	return response()->json(['code' => 1, 'message' => '记录写入成功并已给用户增加权重!']);
	        }
	        return response()->json(['code' => 2, 'message' => '记录写入失败!']);

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
	        $data['status'] = 1;
	    	$data['ratio'] = $request->data['ratio'] ?? 0;
	    	$data['remark'] = $request->data['remark'];
	        $data['no'] = static::createOrderId();
	    	$data['order_no'] = $request->data['order_no'];
	    	$order = Order::where('no', $data['order_no'])->first();
	    	$data['user_id'] = $order->creator_primary_user_id;
	    	$data['before_weight_value'] = UserWeight::where('user_id', $data['user_id'])->value('weight') ?? 0;
	    	$data['after_weight_value'] = round($data['before_weight_value'] + bcmul($data['before_weight_value'], bcdiv($request->data['ratio'], 100)));
	    	$data['start_time'] = $request->data['start_time'] ?? '';
	    	$data['end_time'] = isset($request->data['end_time']) ? $request->data['end_time'] . ' 23:59:59' : '';
	    	$data['voucher'] = $request->data['voucher'] ?? '';
	    	$data['deadline'] = $data['start_time'];
            // 判断订单存不存在
	    	if (! $order) {
	    		return response()->json(['code' => 2, 'message' => '订单不存在!']);
	    	}
            // 奖惩记录
	        $punish = PunishOrReward::create($data);

	        if ($punish) {
	        	return response()->json(['code' => 1, 'message' => '记录写入成功!']);
	        }
	        return response()->json(['code' => 2, 'message' => '记录写入失败!']);

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
    		$data['no'] = static::createOrderId();
	    	$data['order_no'] = $request->data['order_no'];
	    	$order = Order::where('no', $data['order_no'])->first();
	    	$data['user_id'] = $order->creator_primary_user_id;
	    	$data['deadline'] = Carbon::now()->addDays(1)->endOfDay()->toDateTimeString();
	    	$data['remark'] = $request->data['remark'];
	    	$data['voucher'] = $request->data['voucher'] ?? '';
	    	$data['confirm'] = 1;
            // 判断订单存不存在
	    	if (! $order) {
	    		return response()->json(['code' => 2, 'message' => '订单不存在!']);
	    	}
            // 奖惩记录
	        $punish = PunishOrReward::create($data);

	        if ($punish) {
	        	return response()->json(['code' => 1, 'message' => '记录写入成功!']);
	        }
	        return response()->json(['code' => 2, 'message' => '记录写入失败!']);

    	} catch (Exception $e) {

    	}
    }

    /**
     * 同意申诉，此时记录不会删除，因为用户发起的申诉，
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function pass(Request $request)
    {
    	try {
	    	PunishOrReward::where('id', $request->data['id'])->update(['status' => 11, 'confirm' => 1]);

	    	$punish = PunishOrReward::find($request->data['id']);
	    	// 操作日志
            $data = [
            	[
                	'punish_or_reward_id' => $punish->id,
                    'operate_style' => 'status',
                    'punish_or_reward_no' => $punish->no,
                    'order_no' => $punish->order_no,
                    'before_value' => 9,
                    'after_value' => 11,
                    'admin_user_name' => '管理员： ' . AdminUser::where('id', Auth::id())->value('name') ?? '系统',
                    'created_at' => new \DateTime(),
                	'updated_at' => new \DateTime(),
            	],
            	[
                	'punish_or_reward_id' => $punish->id,
                    'operate_style' => 'confirm',
                    'punish_or_reward_no' => $punish->no,
                    'order_no' => $punish->order_no,
                    'before_value' => 0,
                    'after_value' => 1,
                    'admin_user_name' => '管理员： ' . AdminUser::where('id', Auth::id())->value('name') ?? '系统',
                    'created_at' => new \DateTime(),
                	'updated_at' => new \DateTime(),
            	],
            ];
            // 操作日志状态改为撤销
            DB::table('punish_or_reward_revisions')->insert($data);
	    	return response()->json(['code' => 1, 'message' => '同意申诉并撤销该条记录!']);
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
                    return response()->json(['code' => 2, 'message' => '账户余额不足，提醒他充值!']);
                }
                // 扣款
	    		$bool = Asset::handle(new Consume($punish->sub_money, 2, $punish->no, '违规扣款', $punish->user_id));

                if ($bool) {
                    // PunishOrReward::where('id', $request->data['id'])->update(['status' => 10, 'confirm' => 1]);
                    $punish->status = 10;
                    $punish->confirm = 1;
                    $punish->save();
                    // 操作日志
                    $data = [
                    	[
                        	'punish_or_reward_id' => $punish->id,
    	                    'operate_style' => 'status',
    	                    'punish_or_reward_no' => $punish->no,
                            'order_no' => $punish->order_no,
    	                    'before_value' => 9,
    	                    'after_value' => 10,
    	                    'admin_user_name' => '管理员： ' . AdminUser::where('id', Auth::id())->value('name') ?? '系统',
    	                    'created_at' => new \DateTime(),
                    		'updated_at' => new \DateTime(),
                    	],
                    	[
                        	'punish_or_reward_id' => $punish->id,
    	                    'operate_style' => 'confirm',
    	                    'punish_or_reward_no' => $punish->no,
                            'order_no' => $punish->order_no,
    	                    'before_value' => 0,
    	                    'after_value' => 1,
    	                    'admin_user_name' => '管理员： ' . AdminUser::where('id', Auth::id())->value('name') ?? '系统',
    	                    'created_at' => new \DateTime(),
                    		'updated_at' => new \DateTime(),
                    	],
                    ];
                    // 奖惩日志
                    DB::table('punish_or_reward_revisions')->insert($data);
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
	    		// PunishOrReward::where('id', $request->data['id'])->update(['status' => 10, 'confirm' => 1]);                   
                $punish->status = 10;
                $punish->confirm = 1;
                $punish->save();
                // 操作日志
                $data = [
                	[
                    	'punish_or_reward_id' => $punish->id,
	                    'operate_style' => 'status',
	                    'punish_or_reward_no' => $punish->no,
                        'order_no' => $punish->order_no,
	                    'before_value' => 9,
	                    'after_value' => 10,
	                    'admin_user_name' => '管理员： ' . AdminUser::where('id', Auth::id())->value('name') ?? '系统',
	                    'created_at' => new \DateTime(),
                    	'updated_at' => new \DateTime(),
                	],
                	[
                    	'punish_or_reward_id' => $punish->id,
	                    'operate_style' => 'confirm',
	                    'punish_or_reward_no' => $punish->no,
                        'order_no' => $punish->order_no,
	                    'before_value' => 0,
	                    'after_value' => 1,
	                    'admin_user_name' => '管理员： ' . AdminUser::where('id', Auth::id())->value('name') ?? '系统',
	                    'created_at' => new \DateTime(),
                    	'updated_at' => new \DateTime(),
                	],
                ];
                // 奖惩日志
                DB::table('punish_or_reward_revisions')->insert($data);
                return response()->json(['code' => 1, 'message' => '申诉驳回，并已对商家进行权重处罚!']);
	    	}
    		 return response()->json(['code' => 2, 'message' => '系统错误!']);
    	} catch (Exception $e) {

    	}
    }
}

<?php

namespace App\Extensions\Dailian\Controllers;

use DB;
use Asset;
use Redis;
use App\Models\User;
use App\Models\Order; 
use App\Models\OrderDetail;
use App\Models\OrderHistory; 
use App\Extensions\Asset\Income;
use App\Extensions\Asset\Expend;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\DailianException; 

/**
 * 代练操作构造类
 */
abstract class DailianAbstract
{
    protected $orderNo;             // 订单编号
    protected $userId = null;       // 操作用户id
    protected $adminUserId = null;  // 操作管理员id
    protected $order;               // 订单对象
    protected $orderDetail;         // 订单详情数组
    protected $orderHistory;        // 订单操作记录对象
    protected $type;                // 操作类型 1 => '未结单',2 => '代练中',3 => '待验收',4 => '撤销中',5 => '仲裁中',6 => '异常',
    								// 7 => '锁定',8 => '已撤销',9 => '已结算',10 => '已仲裁',11 => '已下架',12 => '强制撤销',
    protected $acceptableStatus;    // 可接受的状态
    protected $beforeHandleStatus;  // 操作前状态
    protected $handledStatus;       // 操作后状态
    protected $description;         // 操作说明
    protected $runAfter = false;

    /**
     * [getObject description]
     * @return [type] [description]
     */
    public function getObject()
    {
        // 获取订单
        $this->order = Order::where('no', $this->orderNo)->lockForUpdate()->first();


        if (empty($this->order)) {
            throw new DailianException('订单不存在!');
        }

        $this->orderDetail = $this->order->detail->pluck('field_value', 'field_name');

        if (!in_array($this->order->status, $this->acceptableStatus)) {
            \Log::alert('订单：' .$this->order->no . '订单状态不允许,原状态：' . $this->order->status . ' 更改为：' . $this->handledStatus);
            throw new DailianException('订单状态不允许更改');
        }
    }
    // 创建操作前的订单
    public function createLogObject()
    {
        $user = User::where('id', $this->userId)->first();
        $this->orderHistory = new OrderHistory;
        $this->orderHistory->user_id       = $this->userId;
        // $this->orderHistory->creator_primary_user_id  = $user->getPrimaryUserId();
        $this->orderHistory->creator_primary_user_id  = $this->order->creator_primary_user_id;
        $this->orderHistory->admin_user_id = $this->adminUserId;
        $this->orderHistory->type          = $this->type;
        $this->orderHistory->name          = config('order.operation_type')[$this->type];
        $this->orderHistory->before        = serialize($this->order->toArray());
        $this->orderHistory->created_at    = date('Y-m-d H:i:s');

        // 更新订单详情里面订单前一个状态
        $this->updateOrderPreviousStatus($this->order);
    }
    // 设置订单属性
    public function setAttributes() {}

    // 保存更改状态后的订单
    public function save()
    {
    	$this->order->status = $this->handledStatus;
        if (!$this->order->save()) {
            throw new DailianException('订单操作失败');
        }

        return $this->order;
    }

    // 更新平台资产
    public function updateAsset(){}

    // 订单日志描述
    public function setDescription()
    {
    	// 操作后的状态
    	$statusName = config('order.status_leveling')[$this->handledStatus];
    	// 操作前的状态
    	$beforeStatusName = config('order.status_leveling')[$this->beforeHandleStatus];
        $username = User::find($this->userId) ? User::find($this->userId)->username : '';
    	// 详情
        $this->description = "用户[$username]将订单从[$beforeStatusName]设置为[$statusName]状态！";
    }

    // 保存操作日志
    public function saveLog()
    {
    	$this->orderHistory->order_no    = $this->order->no;
        $this->orderHistory->after       = serialize($this->order->toArray());
        $this->orderHistory->description = $this->description;

        if (!$this->orderHistory->save()) {
            throw new DailianException('操作记录失败');
        }
    }

    // 获取订单
    public function getOrder()
    {
        return $this->order;
    }

    public function after() {}

    public function checkThirdClientOrder($order)
    {
        $orderDetails = OrderDetail::where('order_no', $order->no)
            ->pluck('field_value', 'field_name')
            ->toArray();

        if(! $orderDetails) {
            throw new DailianException('订单号不存在!');
        }

        // if (! $orderDetails['show91_order_no'] && ! $orderDetails['dailianmama_order_no']) {
        //     throw new DailianException('第三方订单号不存在!');
        // }
        return $orderDetails;
    }

    /**
     * 订单数量
     */
    public function orderCount()
    {
        // 写入待验收数量角标
        // 接单人
        orderStatusCount($this->order->gainer_primary_user_id, $this->handledStatus);
        orderStatusCount($this->order->gainer_primary_user_id, $this->beforeHandleStatus, 4);
        // 发单人
        orderStatusCount($this->order->creator_primary_user_id, $this->handledStatus);
        orderStatusCount($this->order->creator_primary_user_id, $this->beforeHandleStatus, 4);
    }

    /**
     * 我们平台触发的订单报警
     * @param $order object 订单
     * @param $operate integer 我们的操作类型ID
     * @return mixed
     */
    public function addOperateFailOrderToRedis($order, $operate)
    {
        // 获得订单详情
        $orderDetails = $this->checkThirdClientOrder($order);
        // 订单属于哪个平台
        switch ($orderDetails['third']) {
            case 1: // show91
                // 组合一段字符串 = 第三方平台号 + 我们平台的操作代号 + 我们平台的状态代号
                $redisValue = '1-'.$operate.'-'.$order->status;
                break;
            case 2: // 代练妈妈
                // 组合一段字符串 = 第三方平台号 + 我们平台的操作代号 + 我们平台的状态代号
                $redisValue = '2-'.$operate.'-'.$order->status;
                break;
            case 3: // 蚂蚁代练
                // 组合一段字符串 = 第三方平台号 + 我们平台的操作代号 + 我们平台的状态代号
                $redisValue = '3-'.$operate.'-'.$order->status;
                break;
            case 4: // dd373
                // 组合一段字符串 = 第三方平台号 + 我们平台的操作代号 + 我们平台的状态代号
                $redisValue = '4-'.$operate.'-'.$order->status;
                break;
        }

        // 写入redis哈希 = (表名， 键， 值)
        $result = Redis::hSet('our_notice_orders', $order->no, $redisValue);
        // 错误日志提示主题
        $message = $result == 1 ? '写入成功，新纪录' : ($result == 0 ? '写入成功，新值被覆盖' : '写入失败');
                    
        myLog('our-order-notice', ['order_no' => $order->no, 'third_order_no' => $orderDetails['third_order_no'], 'message' => '我们的平台操作失败了，报警订单正在写入redis，写入结果：' . $message]);

        return $order;
    }

    /**
     * 每次成功操作，看看redis里面是否还留有报警订单，有就删除
     * @param  [type] $orderNo [description]
     * @return [type]          [description]
     */
    public function deleteOperateSuccessOrderFromRedis($orderNo, $hashName = 'our_notice_orders')
    {
        Redis::hDel($hashName, $orderNo);
    }

    /**
     * 获取订单，订单详情，协商仲裁信息
     * @param  [type] $orderNo [description]
     * @return [type]          [description]
     */
    public function getOrderAndOrderDetailAndLevelingConsult($orderNo)
    {
        $collectionArr =  DB::select("
            SELECT a.order_no, 
                MAX(CASE WHEN a.field_name='region' THEN a.field_value ELSE '' END) AS region,
                MAX(CASE WHEN a.field_name='serve' THEN a.field_value ELSE '' END) AS serve,
                MAX(CASE WHEN a.field_name='account' THEN a.field_value ELSE '' END) AS account,
                MAX(CASE WHEN a.field_name='password' THEN a.field_value ELSE '' END) AS password,
                MAX(CASE WHEN a.field_name='source_order_no' THEN a.field_value ELSE '' END) AS source_order_no,
                MAX(CASE WHEN a.field_name='role' THEN a.field_value ELSE '' END) AS role,
                MAX(CASE WHEN a.field_name='game_leveling_type' THEN a.field_value ELSE '' END) AS game_leveling_type,
                MAX(CASE WHEN a.field_name='game_leveling_title' THEN a.field_value ELSE '' END) AS game_leveling_title,
                MAX(CASE WHEN a.field_name='game_leveling_instructions' THEN a.field_value ELSE '' END) AS game_leveling_instructions,
                MAX(CASE WHEN a.field_name='game_leveling_requirements' THEN a.field_value ELSE '' END) AS game_leveling_requirements,
                MAX(CASE WHEN a.field_name='auto_unshelve_time' THEN a.field_value ELSE '' END) AS auto_unshelve_time,
                MAX(CASE WHEN a.field_name='game_leveling_amount' THEN a.field_value ELSE '' END) AS game_leveling_amount,
                MAX(CASE WHEN a.field_name='game_leveling_day' THEN a.field_value ELSE '' END) AS game_leveling_day,
                MAX(CASE WHEN a.field_name='game_leveling_hour' THEN a.field_value ELSE '' END) AS game_leveling_hour,
                MAX(CASE WHEN a.field_name='security_deposit' THEN a.field_value ELSE '' END) AS security_deposit,
                MAX(CASE WHEN a.field_name='efficiency_deposit' THEN a.field_value ELSE '' END) AS efficiency_deposit,
                MAX(CASE WHEN a.field_name='user_phone' THEN a.field_value ELSE '' END) AS user_phone,
                MAX(CASE WHEN a.field_name='user_qq' THEN a.field_value ELSE '' END) AS user_qq,
                MAX(CASE WHEN a.field_name='source_price' THEN a.field_value ELSE '' END) AS source_price,
                MAX(CASE WHEN a.field_name='client_name' THEN a.field_value ELSE '' END) AS client_name,
                MAX(CASE WHEN a.field_name='client_phone' THEN a.field_value ELSE '' END) AS client_phone,
                MAX(CASE WHEN a.field_name='client_qq' THEN a.field_value ELSE '' END) AS client_qq,
                MAX(CASE WHEN a.field_name='client_wang_wang' THEN a.field_value ELSE '' END) AS client_wang_wang,
                MAX(CASE WHEN a.field_name='game_leveling_require_day' THEN a.field_value ELSE '' END) AS game_leveling_require_day,
                MAX(CASE WHEN a.field_name='game_leveling_require_hour' THEN a.field_value ELSE '' END) AS game_leveling_require_hour,
                MAX(CASE WHEN a.field_name='customer_service_remark' THEN a.field_value ELSE '' END) AS customer_service_remark,
                MAX(CASE WHEN a.field_name='receiving_time' THEN a.field_value ELSE '' END) AS receiving_time,
                MAX(CASE WHEN a.field_name='checkout_time' THEN a.field_value ELSE '' END) AS checkout_time,
                MAX(CASE WHEN a.field_name='customer_service_name' THEN a.field_value ELSE '' END) AS customer_service_name,
                MAX(CASE WHEN a.field_name='third_order_no' THEN a.field_value ELSE '' END) AS third_order_no,
                MAX(CASE WHEN a.field_name='third' THEN a.field_value ELSE '' END) AS third,
                MAX(CASE WHEN a.field_name='poundage' THEN a.field_value ELSE '' END) AS poundage,
                MAX(CASE WHEN a.field_name='price_markup' THEN a.field_value ELSE '' END) AS price_markup,
                MAX(CASE WHEN a.field_name='show91_order_no' THEN a.field_value ELSE '' END) AS show91_order_no,
                MAX(CASE WHEN a.field_name='dailianmama_order_no' THEN a.field_value ELSE '' END) AS dailianmama_order_no,
                MAX(CASE WHEN a.field_name='mayi_order_no' THEN a.field_value ELSE '' END) AS mayi_order_no,
                MAX(CASE WHEN a.field_name='dd373_order_no' THEN a.field_value ELSE '' END) AS dd373_order_no,
                MAX(CASE WHEN a.field_name='wanzi_order_no' THEN a.field_value ELSE '' END) AS wanzi_order_no,
                MAX(CASE WHEN a.field_name='hatchet_man_qq' THEN a.field_value ELSE '' END) AS hatchet_man_qq,
                MAX(CASE WHEN a.field_name='hatchet_man_phone' THEN a.field_value ELSE '' END) AS hatchet_man_phone,
                MAX(CASE WHEN a.field_name='game_leveling_requirements_template' THEN a.field_value ELSE '' END) AS game_leveling_requirements_template,
                b.no,
                b.status as order_status,
                b.created_at as order_created_at,
                b.amount,
                b.creator_user_id, 
                b.creator_primary_user_id, 
                b.game_id, 
                b.gainer_user_id, 
                b.gainer_primary_user_id,
                c.order_no as consult_order_no,
                c.user_id,
                c.amount AS pay_amount,
                c.deposit,
                c.api_amount,
                c.api_deposit,
                c.api_service,
                c.status,
                c.consult,
                c.complain,
                c.complete,
                c.remark,
                c.revoke_message,
                c.complain_message
            FROM order_details a
            LEFT JOIN orders b
            ON a.order_no = b.no
            LEFT JOIN leveling_consults c
            ON a.order_no = c.order_no
            WHERE a.order_no='$orderNo'");
    
        if (! isset($collectionArr) || ! is_array($collectionArr)) {
            throw new DailianException('查询结果错误');
        }
        
        $collection = is_array($collectionArr) ? $collectionArr[0] : '';

        if (empty($collection) || ! $collection->no) {
            throw new DailianException('订单号错误');
        }

        return (array) $collection;
    }

    /**
     * 更新订单详情里面订单前一个状态
     * @param  [type] $order [description]
     * @return [type]        [description]
     */
    public function updateOrderPreviousStatus($order)
    {
        $beforeStatus = OrderDetail::where('order_no', $order->no)
            ->where('field_name', 'order_previous_status')
            ->first();
        // 获取上一条操作记录，如果上一条为仲裁中，则取除了仲裁中和撤销中的最早的一条状态
        if (! $beforeStatus) {
            throw new DailianException('订单不存在');
        }
        
        if (in_array($order->status, [13, 14, 15, 18])) {
            if ($order->status == 15 || $order->status == 18) {
                $orderDetail = OrderDetail::where('order_no', $order->no)
                    ->where('field_name', 'order_previous_status')
                    ->first();

                // 混合状态， 当申请撤销再申请仲裁的时候，特例 13|15
                $previousStatus = $orderDetail->field_value."|".$order->status;

                OrderDetail::where('order_no', $order->no)
                    ->where('field_name', 'order_previous_status')
                    ->update(['field_value' =>  $previousStatus]);
            } else {
                OrderDetail::where('order_no', $order->no)
                    ->where('field_name', 'order_previous_status')
                    ->update(['field_value' =>  $order->status]);
            }
        }
    }

    /**
     * 执行事件
     */
    public function runEvent()
    {
        try {
            $classNameArr = explode('\\', get_class($this));
            myLog('run-event', [end($classNameArr), __CLASS__]);
            $class = 'App\Events\Order' . end($classNameArr);
            event(new $class($this->order));
        } catch (\Exception $exception) {
            myLog('run-event-ex', [$exception->getFile(), $exception->getMessage()]);
        }
    }
}

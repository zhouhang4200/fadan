<?php

namespace App\Extensions\Dailian\Controllers;

use DB;
use Asset;
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
    	$this->order = Order::where('no', $this->orderNo)->lockForUpdate()->first();

        if (empty($this->order)) {
            throw new DailianException('订单不存在');
        }

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
    	// 详情
        $this->description = "用户[{$this->userId}]将订单从[$beforeStatusName]设置为[$statusName]状态！";
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

        if (! $orderDetails['show91_order_no'] && ! $orderDetails['dailianmama_order_no']) {
            throw new DailianException('第三方订单号不存在!');
        }
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
}

<?php

namespace App\Extensions\Dailian\Controllers;

use DB;
use Asset;
use Income;
use Exception; 
use App\Models\Order; // 代练模型
use App\Models\OrderHistory; // 操作日志

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
    protected $runAfter = false;    // 是否执行after方法

    // 获取订单对象
    public function getObject()
    {
    	$this->order = Order::where('no', $this->no)->lockForUpdate()->first();

        if (empty($this->order)) {
            throw new Exception('订单不存在');
        }

        if (!in_array($this->order->status, $this->acceptableStatus)) {
            \Log::alert('订单：' .$this->order->no . '订单状态不允许,原状态：' . $this->order->status . ' 更改为：' . $this->handledStatus);
            throw new Exception('订单状态不允许更改');
        }
    }
    // 创建操作前的订单
    public function createLogObject()
    {
    	$this->orderHistory = new OrderHistory;
        $this->orderHistory->user_id       = $this->userId;
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
            throw new Exception('订单操作失败');
        }

        return $this->order;
    }

    // 更新平台资产
    public function updateAsset(){}

    // 订单日志描述
    public function setDescription()
    {
    	// 操作后的状态
    	$statusName = config('dailian.status')[$this->handledStatus];
    	// 操作前的状态
    	$beforeStatusName = config('dailian.status')[$this->beforeHandledStatus];
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
            throw new Exception('操作记录失败');
        }
    }

    // 保存权重
    public function saveWeight() {}

    // 后续操作
    public function after() {}

    // 获取订单
    public function getOrder()
    {
        return $this->order;
    }
}

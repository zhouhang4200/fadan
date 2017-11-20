<?php
namespace App\Extensions\Order\Operations\Base;

use App\Exceptions\OrderException as Exception;
use App\Models\Order;
use App\Models\OrderHistory;

// 操作
abstract class Operation
{
    protected $orderNo;             // 订单编号
    protected $userId = null;       // 操作用户id
    protected $adminUserId = null;  // 操作管理员id
    protected $order;               // 订单对象
    protected $orderHistory;        // 订单操作记录对象
    protected $type;                // 操作类型 1.创建 2.接单 3.发货 4.置失败 5.申请售后 6.完成售后
    protected $acceptableStatus;    // 可接受的状态
    protected $handledStatus;       // 操作后状态
    protected $description;         // 操作说明
    protected $runAfter = false;    // 是否执行after方法


    // 获取订单
    public function getObject()
    {
        $this->order = Order::where('no', $this->orderNo)->lockForUpdate()->first();
        if (empty($this->order)) {
            throw new Exception('订单不存在');
        }

        if (!in_array($this->order->status, $this->acceptableStatus)) {
            \Log::alert('订单：' .$this->order->no . '订单状态不允许,原状态：' . $this->order->status . ' 更改为：' . $this->handledStatus);
            throw new Exception('订单状态不允许更改');
        }
    }

    // 获取操作前订单
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

    // 保存订单
    public function save()
    {
        $this->order->status = $this->handledStatus;
        if (!$this->order->save()) {
            throw new Exception('订单操作失败');
        }

        return $this->order;
    }

    // 更新资产
    public function updateAsset() {}

    // 设置描述
    public function setDescription()
    {
        $statusName = config('order.status')[$this->handledStatus];
        $this->description = "用户[{$this->userId}]设置订单为[$statusName]状态 ";
    }

    // 写操作记录
    public function saveLog()
    {
        $this->orderHistory->order_no    = $this->order->no;
        $this->orderHistory->after       = serialize($this->order->toArray());
        $this->orderHistory->description = $this->description;

        if (!$this->orderHistory->save()) {
            throw new Exception('操作记录失败');
        }
    }

    // 保存权重依据
    public function saveWeight() {}

    /**
     * 后续操作
     */
    public function after() {}

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
    }
}

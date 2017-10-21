<?php
namespace App\Extensions\Order;

use App\Exceptions\AssetException as Exception;
use App\Models\Order;
use App\Models\OrderHistory;

// 创建订单
class Create
{
    protected $handledStatus = 1;
    protected $type          = 1;

    public function __construct($goodsId, $count, $source, $foreignOrderNO, $originPrice, $remark)
    {

    }

    public function getObject()
    {
        $this->order = new Order;
    }

    public function setAttributes($goodsId, $count, $source, $foreignOrderNO, $originPrice, $remark)
    {
        # 开始事务
        $this->goods = getGame($goodsId);

        $this->order->no                = generateTradeNo();
        $this->order->foreign_order_no        = $foreignOrderNO;
        $this->order->source                  = $source;
        $this->order->status                  = self::BEFORE_STATUS;
        // $this->order->category_id             = $this->goods->category_id;
        // $this->order->category_id_parent      = $this->goods->category_id_parent;
        // $this->order->category_name           = $this->goods->category_name;
        // $this->order->category_name_parent    = $this->goods->category_name_parent;
        $this->order->goods_id                = $goodsId;
        $this->order->goods_name              = $this->goods->name;
        $this->order->origin_price            = $originPrice;
        $this->order->price                   = $this->goods->price;
        $this->order->quantity                = $count;
        $this->order->original_amount         = bcmul($originPrice, $count);
        $this->order->amount                  = bcmul($this->goods->price, $count);
        $this->order->remark                  = $remark;
        $this->order->creator_user_id         = $this->goods->user_id;
        $this->order->creator_primary_user_id = getPrimary($this->goods->user_id);

        if (!$this->order->save()) {
            throw new Exception('订单创建失败');
            #回滚
        }

        # 写订单详情
        # 步骤：
        # 根据商品id获取商品详情
        # 根据商品详情，匹配传入的参数
        # 写订单详情

        #事务提交
    }

    // 设置描述
    public function setDescription()
    {
        $statusName = config('order.status')[$this->handledStatus];
        $this->description = "";
    }
}

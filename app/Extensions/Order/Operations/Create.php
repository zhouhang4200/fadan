<?php
namespace App\Extensions\Order\Operations;

use App\Exceptions\AssetException as Exception;
use App\Models\Order;
use App\Models\User;
use App\Repositories\Api\GoodsRepository;

// 创建订单
class Create extends \App\Extensions\Order\Operations\Base\Operation
{
    protected $handledStatus = 1;
    protected $type          = 1;

    /**
     * @param int $userId 用户id
     * @param string $foreignOrderNO 外部单号
     * @param int $source 来源
     * @param int $goodsId 商品id
     * @param decimal $originalPrice 原价
     * @param int $quantity 数量
     * @param string $remark 备注
     */
    public function __construct($userId, $foreignOrderNO, $source, $goodsId, $originalPrice, $quantity, $remark)
    {
        $this->userId         = $userId;
        $this->foreignOrderNO = $foreignOrderNO;
        $this->source         = $source;
        $this->goodsId        = $goodsId;
        $this->originalPrice  = $originalPrice;
        $this->quantity       = $quantity;
        $this->remark         = $remark;
    }

    // 获取订单
    public function getObject()
    {
        $this->order = new Order;
    }

    public function setAttributes()
    {
        $goods = GoodsRepository::find($this->goodsId);
        if (empty($goods)) {
            throw new Exception('不存在的商品');
        }

        $user = User::find($this->userId);

        $this->order->no                      = generateOrderNo();
        $this->order->foreign_order_no        = $this->foreignOrderNO;
        $this->order->source                  = $this->source;
        $this->order->goods_id                = $this->goodsId;
        $this->order->goods_name              = $goods->name;
        $this->order->service_id              = $goods->service->id;
        $this->order->service_name            = $goods->service->name;
        $this->order->game_id                 = $goods->game->id;
        $this->order->game_name               = $goods->game->name;
        $this->order->original_price          = $this->originalPrice;
        $this->order->price                   = $goods->price;
        $this->order->quantity                = $this->quantity;
        $this->order->original_amount         = bcmul($this->originalPrice, $this->quantity);
        $this->order->amount                  = bcmul($goods->price, $this->quantity);
        $this->order->remark                  = $this->remark;
        $this->order->creator_user_id         = $this->userId;
        $this->order->creator_primary_user_id = $user->getPrimaryUserId();
    }

    // 设置描述
    public function setDescription()
    {
        $sourceName = config('order.source')[$this->source];
        $this->description = "用户[{$this->userId}]从[{$sourceName}]渠道创建了订单";
    }
}

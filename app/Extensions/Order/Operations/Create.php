<?php
namespace App\Extensions\Order\Operations;

use DB;
use App\Exceptions\AssetException as Exception;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Repositories\Api\GoodsRepository;
use Asset;
use App\Extensions\Asset\Expend;

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
     * @param float $originalPrice 原价
     * @param int $quantity 数量
     * @param array $details 订单详细参数 例：['version' => '版本','account' => '账号','region'  => '区服']
     */
    public function __construct($userId, $foreignOrderNO, $source, $goodsId, $originalPrice, $quantity, $details)
    {
        $this->userId         = $userId;
        $this->foreignOrderNO = $foreignOrderNO;
        $this->source         = $source;
        $this->goodsId        = $goodsId;
        $this->originalPrice  = $originalPrice;
        $this->quantity       = $quantity;
        $this->details        = $details;
    }

    // 获取订单
    public function getObject()
    {
        $this->order = new Order;
    }

    public function setAttributes()
    {
        $this->goods = GoodsRepository::find($this->goodsId);
        if (empty($this->goods)) {
            throw new Exception('不存在的商品');
        }

        $user = User::find($this->userId);

        $this->order->no                      = generateOrderNo();
        $this->order->foreign_order_no        = $this->foreignOrderNO;
        $this->order->source                  = $this->source;
        $this->order->goods_id                = $this->goodsId;
        $this->order->goods_name              = $this->goods->name;
        $this->order->service_id              = $this->goods->service->id;
        $this->order->service_name            = $this->goods->service->name;
        $this->order->game_id                 = $this->goods->game->id;
        $this->order->game_name               = $this->goods->game->name;
        $this->order->original_price          = $this->originalPrice ?: $this->goods->price;
        $this->order->price                   = $this->goods->price;
        $this->order->quantity                = $this->quantity;
        $this->order->original_amount         = bcmul($this->originalPrice, $this->quantity);
        $this->order->amount                  = bcmul($this->goods->price, $this->quantity);
        $this->order->creator_user_id         = $this->userId;
        $this->order->creator_primary_user_id = $user->getPrimaryUserId();
        $this->order->remark                  = '';

        // 记录订单详情
        if (!empty($this->details)) {
            $widget = $this->goods->goodsTemplate->widget->pluck('field_display_name', 'field_name');

            foreach ($this->details as $fieldName => $fieldValue) {
                if (!isset($widget[$fieldName])) continue;

                $orderDetail = new OrderDetail;
                $orderDetail->order_no                = $this->order->no;
                $orderDetail->field_name              = $fieldName;
                $orderDetail->field_display_name      = $widget[$fieldName];
                $orderDetail->filed_value             = $fieldValue;
                $orderDetail->creator_primary_user_id = $this->order->creator_primary_user_id;

                if (!$orderDetail->save()) {
                    DB::rollback();
                    throw new Exception('详情记录失败');
                }

                $this->order->remark .= "{$widget[$fieldName]}: {$fieldValue}; ";
            }
        }
    }

    public function updateAsset()
    {
        Asset::handle(new Expend($this->order->amount, Expend::TRADE_SUBTYPE_ORDER_MARKET, $this->order->no, '下订单', $this->order->creator_primary_user_id));

        // 写多态关联
        if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
            DB::rollback();
            throw new Exception('申请失败');
        }

        if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
            DB::rollback();
            throw new Exception('申请失败');
        }
    }

    // 设置描述
    public function setDescription()
    {
        $sourceName = config('order.source')[$this->source];
        $this->description = "用户[{$this->userId}]从[{$sourceName}]渠道创建了订单";
    }
}

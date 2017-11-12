<?php
namespace App\Extensions\Order\Operations;

use App\Exceptions\CustomException;
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
    protected $type = 1;

    /**
     * 商品单价
     * @var float
     */
    protected $price = 0;

    /**
     * 原单价
     * @var float|int
     */
    protected $originalPrice = 0;
    /**
     * 商品
     * @var
     */
    protected $goods;

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
        $this->userId = $userId;
        $this->foreignOrderNO = $foreignOrderNO;
        $this->source = $source;
        $this->goodsId = $goodsId;
        $this->originalPrice = $originalPrice;
        $this->quantity = $quantity;
        $this->details = $details;
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

        $price = $this->setPrice($user->getUserSetting());

        $this->order->no = generateOrderNo();
        $this->order->foreign_order_no = $this->foreignOrderNO;
        $this->order->source = $this->source;
        $this->order->goods_id = $this->goodsId;
        $this->order->goods_name = $this->goods->name;
        $this->order->service_id = $this->goods->service->id;
        $this->order->service_name = $this->goods->service->name;
        $this->order->game_id = $this->goods->game->id;
        $this->order->game_name = $this->goods->game->name;
        $this->order->original_price = $this->originalPrice;
        $this->order->price = $price;
        $this->order->quantity = $this->quantity;
        $this->order->original_amount = bcmul($this->originalPrice, $this->quantity);
        $this->order->amount = bcmul($price, $this->quantity);
        $this->order->creator_user_id = $this->userId;
        $this->order->creator_primary_user_id = $user->getPrimaryUserId();
        $this->order->remark = '';

        // 记录订单详情
        if (!empty($this->details)) {
            $widget = $this->goods->goodsTemplate->widget->pluck('field_display_name', 'field_name');

            foreach ($this->details as $fieldName => $fieldValue) {
                if (!isset($widget[$fieldName])) continue;

                $orderDetail = new OrderDetail;
                $orderDetail->order_no = $this->order->no;
                $orderDetail->field_name = $fieldName;
                $orderDetail->field_display_name = $widget[$fieldName];
                $orderDetail->field_value = $fieldValue;
                $orderDetail->creator_primary_user_id = $this->order->creator_primary_user_id;

                if (!$orderDetail->save()) {
                    throw new Exception('详情记录失败');
                }

                $this->order->remark .= "{$widget[$fieldName]}: {$fieldValue}; ";
            }
        }
    }

    public function updateAsset()
    {
        try {
            Asset::handle(new Expend($this->order->amount, Expend::TRADE_SUBTYPE_ORDER_MARKET, $this->order->no, '下订单', $this->order->creator_primary_user_id));
        }
        catch (CustomException $customException) {
            $this->order->status = 11;
            $this->order->save();
            return false;
        }

        // 写多态关联
        if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
            throw new Exception('申请失败');
        }

        if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
            throw new Exception('申请失败');
        }
    }

    // 设置描述
    public function setDescription()
    {
        $sourceName = config('order.source')[$this->source];
        $this->description = "用户[{$this->userId}]从[{$sourceName}]渠道创建了订单";

        if ($this->order->status == 1) {
            $this->description .= "并付款";
        }
    }

    /**
     * 设置商品价格
     * @param $userSetting
     * @return string
     */
    protected function setPrice($userSetting)
    {
        if ($this->originalPrice <= 0) {
            $this->originalPrice = $this->goods->price;
        }
        // 如果商品转出价高与订单原价。并却商品没有设置请允许卖本转单。转用风控系统数x订单销售单价
        if ($this->goods->price > $this->originalPrice && $this->goods->loss == 0) {
            // 获取用户设置的风控值，没有侧取平台设置的统一值
            $riskRate = isset($userSetting['api_risk_rate']) ?
                $userSetting['api_risk_rate'] : config('order.apiRiskRate');
            return bcmul($riskRate, $this->originalPrice);
        }
        return $this->goods->price;
    }
}

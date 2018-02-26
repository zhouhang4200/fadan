<?php
namespace App\Repositories\Api;

use App\Exceptions\CustomException as Exception;
use App\Models\AutomaticallyGrabGoods;
use App\Models\TaobaoTrade;
use App\Models\TaobaoOrder;

/**
* 淘宝订单仓库
*/
class TaobaoTradeRepository
{
    // 创建
    public static function create($trade, $order)
    {
        if (!empty(TaobaoTrade::where('tid', $trade['tid'])->first())) {
            throw new Exception('tid已存在');
        }

        // 查找商品ID是否存在配置表中，如果有就存储订单否则就不管
        $goods = AutomaticallyGrabGoods::where('foreign_goods_id', $trade['num_iid'])->first();

        if ($goods) {
            // 写入订单所属用户与服务类型
            $trade['user_id'] = $goods->user_id;
            $trade['service_id'] = $goods->service_id;

            // 创建交易数据
            $taobaoTrade = TaobaoTrade::create($trade);
            if (!$taobaoTrade) {
                throw new Exception('创建失败');
            }

            // 创建订单数据
            $order['taobao_trade_id'] = $taobaoTrade->id;
            $order['alipay_id'] = $taobaoTrade->alipay_id;
            TaobaoOrder::create($order);
        }

        return true;
    }

    // 更新
    public static function update($trade, $order)
    {
        // 创建交易数据
        $TaobaoTrade = TaobaoTrade::where('tid', $trade['tid'])->first();
        if (empty($TaobaoTrade)) {
            throw new Exception('数据不存在');
        }

        // 更新交易
        $result = TaobaoTrade::where('id', $TaobaoTrade['id'])->update($trade);
        if (!$result) {
            myLog('taobao-order-update', ['更新交易失败', $trade]);
        }

        // 更新订单
        $result = TaobaoOrder::where('taobao_trade_id', $TaobaoTrade['id'])->update($order);
        if (!$result) {
            myLog('taobao-order-update', ['更新订单失败', $order]);
        }

        return true;
    }

    // 处理接口传的数据
    public static function getParams($data)
    {
        $trade = [
            'alipay_id'              => $data['AlipayId'],
            'alipay_no'              => $data['AlipayNo'],
            'alipay_point'           => $data['AlipayPoint'],
            'arrive_interval'        => $data['ArriveInterval'],
            'buyer_alipay_no'        => $data['BuyerAlipayNo'],
            'buyer_area'             => $data['BuyerArea'],
            'buyer_email'            => $data['BuyerEmail'],
            'buyer_flag'             => $data['BuyerFlag'],
            'buyer_ip'               => ip2long($data['BuyerIp']),
            'buyer_nick'             => $data['BuyerNick'],
            'buyer_obtain_point_fee' => $data['BuyerObtainPointFee'],
            'buyer_rate'             => $data['BuyerRate'],
            'can_rate'               => $data['CanRate'],
            'consign_interval'       => $data['ConsignInterval'],
            'coupon_fee'             => $data['CouponFee'],
            'created'                => $data['Created'],
            'cross_bonded_declare'   => $data['CrossBondedDeclare'],
            'delay_create_delivery'  => $data['DelayCreateDelivery'],
            'et_shop_id'             => $data['EtShopId'],
            'forbid_consign'         => $data['ForbidConsign'],
            'has_buyer_message'      => $data['HasBuyerMessage'],
            'has_post_fee'           => $data['HasPostFee'],
            'has_yfx'                => $data['HasYfx'],
            'is_3d'                  => $data['Is3D'],
            'is_brand_sale'          => $data['IsBrandSale'],
            'is_daixiao'             => $data['IsDaixiao'],
            'is_force_wlb'           => $data['IsForceWlb'],
            'is_lgtype'              => $data['IsLgtype'],
            'is_part_consign'        => $data['IsPartConsign'],
            'is_sh_ship'             => $data['IsShShip'],
            'is_wt'                  => $data['IsWt'],
            'num'                    => $data['Num'],
            'num_iid'                => $data['NumIid'],
            'ofp_hold'               => $data['OfpHold'],
            'pay_time'               => $data['PayTime'],
            'payment'                => $data['Payment'],
            'pcc_af'                 => $data['PccAf'],
            'point_fee'              => $data['PointFee'],
            'post_gate_declare'      => $data['PostGateDeclare'],
            'price'                  => $data['Price'],
            'real_point_fee'         => $data['RealPointFee'],
            'receiver_address'       => $data['ReceiverAddress'],
            'seller_can_rate'        => $data['SellerCanRate'],
            'seller_flag'            => $data['SellerFlag'],
            'seller_nick'            => $data['SellerNick'],
            'seller_rate'            => $data['SellerRate'],
            'share_group_hold'       => $data['ShareGroupHold'],
            'status'                 => $data['Status'],
            'team_buy_hold'          => $data['TeamBuyHold'],
            'tid'                    => $data['Tid'],
            'tid_str'                => $data['TidStr'],
            'top_hold'               => $data['TopHold'],
            'toptype'                => $data['Toptype'],
            'trade_from'             => $data['TradeFrom'],
            'type'                   => $data['Type'],
            'zero_purchase'          => $data['ZeroPurchase'],
            'handle_status'            => 0,
        ];

        $dataOrder = $data['Orders'][0];
        $order = [
            'bind_oid'         => $dataOrder['BindOid'],
            'buyer_rate'       => $dataOrder['BuyerRate'],
            'cid'              => $dataOrder['Cid'],
            'is_daixiao'       => $dataOrder['IsDaixiao'],
            'is_oversold'      => $dataOrder['IsOversold'],
            'is_service_order' => $dataOrder['IsServiceOrder'],
            'is_sh_ship'       => $dataOrder['IsShShip'],
            'is_www'           => $dataOrder['IsWww'],
            'item_meal_id'     => $dataOrder['ItemMealId'],
            'num'              => $dataOrder['Num'],
            'num_iid'          => $dataOrder['NumIid'],
            'oid'              => $dataOrder['Oid'],
            'outer_iid'        => $dataOrder['OuterIid'],
            'refund_id'        => $dataOrder['RefundId'],
            'refund_status'    => $dataOrder['RefundStatus'],
            'seller_rate'      => $dataOrder['SellerRate'],
            'title'            => $dataOrder['Title'],
        ];

        foreach ($trade as $key => $value) {
            $trade[$key] = ($value === null ? '' : $value);
        }

        foreach ($order as $key => $value) {
            $order[$key] = ($value === null ? '' : $value);
        }

        return ['trade' => $trade, 'order' => $order];
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Extensions\Order\Operations\Create;
use App\Models\SiteInfo;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Order, Exception;
use App\Events\NotificationEvent;
use App\Exceptions\CustomException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Extensions\Order\ForeignOrder\ForeignOrderFactory;

class OrderController extends Controller
{

    public function create()
    {
        ForeignOrderFactory::choose('kamen')->outputOrder([]);
    }

    public function KamenOrder(Request $request)
    {
        if (in_array(getClientIp(), ['120.26.205.22', '116.205.13.50'])) {

            $orderData = ForeignOrderFactory::choose('kamen')->outputOrder($request->data);
            if (isset($orderData['price'])) {
                $userId = 0;
                //  用站点ID找到主账号与子账号随机分配一个用户
                $masterUserId = SiteInfo::where('kamen_site_id', $orderData['kamen_site_id'])->first();

                $subUserId = User::where('parent_id', $masterUserId->user_id)->pluck('id')->toArray();

                if ($subUserId) {
                    $userId = $subUserId[rand(0, count($subUserId) - 1)];
                } else {
                    $userId = $masterUserId->user_id;
                }

                // 原始订单数据
                $goodsId = $orderData['goods_id']; // 商品Id
                $originalPrice = $orderData['price']; // 原单价
                $quantity = $orderData['quantity']; // 数量
                $foreignOrderNO = isset($orderData['foreign_order_no']) ? $orderData['foreign_order_no'] : ''; // 外部ID
                $wangWang = !empty($orderData['wang_wang']) ? $orderData['wang_wang'] : ''; // 天猫订单旺旺号

                Order::handle(new Create($userId, $foreignOrderNO, $masterUserId->channel, $goodsId, $originalPrice, $quantity, $orderData));

                if (Order::get()->status != 11) {
                    // 给所有用户推送新订单消息
                    event(new NotificationEvent('NewOrderNotification', Order::get()->toArray()));
                    // 待接单数量加1
                    waitReceivingQuantityAdd();
                    // 待接单数量
                    event(new NotificationEvent('MarketOrderQuantity', ['quantity' => marketOrderQuantity()]));
                    // 写入待分配订单hash 如果订单有旺旺号则一同写入待分配hash中，以达到同一旺旺下的订单在指定时间内分配给同一商户
                    waitReceivingAdd(Order::get()->no,
                        Carbon::now('Asia/Shanghai')->addMinute(1)->toDateTimeString(),
                        Order::get()->created_at->toDateTimeString(),
                        $wangWang
                    );
                    // 更新订单状态
                    return 'success';
                } else {
                    return 'success';
                }
            } else {
                return 'fail1';
            }
        }
        dd(getClientIp());
    }

    public function TmallOrder(Request $request)
    {
        return ForeignOrderFactory::choose('tmall')->outputOrder($request->data);
    }

    public function JdOrder(Request $request)
    {
        return ForeignOrderFactory::choose('jd')->outputOrder($request->data);
    }

    public function test(Request $request)
    {
        \Log::alert(json_encode($request->all()));
    }
}

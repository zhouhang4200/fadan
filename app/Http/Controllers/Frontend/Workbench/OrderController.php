<?php

namespace App\Http\Controllers\Frontend\Workbench;

use App\Events\NotificationEvent;
use App\Repositories\Frontend\OrderRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

use App\Models\GoodsTemplate;

use Order, Exception;
use App\Repositories\Frontend\GameRepository;
use App\Repositories\Frontend\ServiceRepository;
use App\Repositories\Frontend\UserGoodsRepository;
use App\Repositories\Backend\GoodsTemplateWidgetRepository;

use App\Exceptions\CustomException;
use App\Extensions\Order\Operations\Create;
use Union\UnionPaginator;

/**
 * 工作台
 * Class OrderController
 * @package App\Http\Controllers\Frontend\Workbench
 */
class OrderController extends Controller
{
    /**
     * @param ServiceRepository $serviceRepository
     * @param GameRepository $gameRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     * @internal param Request $request
     * @internal param OrderRepository $orderRepository
     */
    public function index(ServiceRepository $serviceRepository, GameRepository $gameRepository)
    {
        $services = $serviceRepository->available();
        $games = $gameRepository->available();

        return view('frontend.workbench.index', compact('orders', 'services', 'games', 'type', 'no'));
    }

    /**
     * @param Request $request
     * @param OrderRepository $orderRepository
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderList(Request $request,  OrderRepository $orderRepository)
    {
        $type = $request->input('type', 'need');
        $no = $request->order_no;
        $searchType = $request->input('search_type', 0);
        $searchContent = $request->input('search_content');

        $orders = $orderRepository->dataList($type, $no);

        if ($request->ajax()) {
            if (!in_array($type, ['need' , 'ing', 'finish', 'cancel', 'after-sales', 'market', 'cancel', 'search'])) {
                return response()->ajax(0, '不存在的类型');
            }
            return response()->json(\View::make('frontend.workbench.order-list', [
                'type' => $type,
                'no' => $no,
                'orders' => $orders,
                'searchType' => $searchType,
                'searchContent' => $searchContent,
            ])->render());
        }
    }

    /**
     * 下单
     * @param Request $request
     */
    public function order(Request $request)
    {
        try {
            // 原始订单数据
            $orderData = $request->data;
            $userId = Auth::user()->id; // 下单用户
            $goodsId = $orderData['goods']; // 商品Id
            $originalPrice = 0; // 原价
            $quantity = $orderData['quantity']; // 数量
            $foreignOrderNO = isset($orderData['foreign_order_no']) ? $orderData['foreign_order_no'] : ''; // 外部ID

            unset($orderData['amount']);
            unset($orderData['goods']);
            unset($orderData['amount']);
            unset($orderData['foreign_order_no']);

            try {
                Order::handle(new Create($userId, $foreignOrderNO, 1, $goodsId, $originalPrice, $quantity, $orderData));
                if (Order::get()->status != 11) {
                    // 给所有用户推送新订单消息
                    event(new NotificationEvent('NewOrderNotification', Order::get()->toArray()));
                    // 待接单数量加1
                    waitReceivingQuantityAdd();
                    // 写入待分配订单hash
                    waitReceivingAdd(Order::get()->no, json_encode(['receiving_date' => Carbon::now('Asia/Shanghai')->addMinute(1)->toDateTimeString(), 'created_date' => Order::get()->created_at->toDateTimeString()]));
                    // 待接单数量
                    event(new NotificationEvent('MarketOrderQuantity', ['quantity' => marketOrderQuantity()]));
                    return response()->ajax(1, '下单成功');
                } else {
                    return response()->ajax(0, '下单失败您的余额不足');
                }
            } catch (CustomException $exception) {
                return response()->ajax(0, $exception->getMessage());
            }
        } catch (CustomException $customException) {
            return response()->ajax(0, '下单失败请联系平台工作人员');
        }
    }

    /**
     * 获取当前用户可下单的商品
     * @param Request $request
     * @param UserGoodsRepository $userGoodsRepository
     * @return mixed
     */
    public function goods(Request $request, UserGoodsRepository $userGoodsRepository)
    {
        $displayGoods = $userGoodsRepository->allGoods($request->service_id, $request->game_id);

        if (count($displayGoods)) {
            return response()->ajax(1, '获取成功', ['goods' => $displayGoods]);
        }
        return response()->ajax(0, '您没有在此类型与游戏的组合下发布商品');
    }

    /**
     * 获取关联的模版
     * @param Request $request
     * @param GoodsTemplateWidgetRepository $goodsTemplateWidgetRepository
     * @return mixed
     */
    public function template(Request $request, GoodsTemplateWidgetRepository $goodsTemplateWidgetRepository)
    {
        // 获取模版ID
        $templateId = GoodsTemplate::getTemplateId($request->service_id, $request->game_id);
        if ($templateId) {
            $widgets = $goodsTemplateWidgetRepository->getTemplateAllWidgetByTemplateId($templateId);

            return response()->ajax(1, '获取成功', ['widgets' => $widgets]);
        }
        return response()->ajax(0, '没有商品模版');
    }

    /**
     * 根据父级ID获取子级下拉项
     * @param Request $request
     * @param GoodsTemplateWidgetRepository $goodsTemplateWidgetRepository
     * @return \Illuminate\Http\JsonResponse
     */
    public function widgetChild(Request $request, GoodsTemplateWidgetRepository $goodsTemplateWidgetRepository)
    {
        $widgetValue = $goodsTemplateWidgetRepository->getSelectValueByParentId($request->parent_id);

        $valueArr = explode(',', $widgetValue->field_value);

        return response()->ajax(1, '获取成功', ['child' => explode('|', $valueArr[$request->id - 1])]);
    }
}


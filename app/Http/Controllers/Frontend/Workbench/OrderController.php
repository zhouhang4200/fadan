<?php

namespace App\Http\Controllers\Frontend\Workbench;

use App\Repositories\Frontend\OrderRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

use App\Models\GoodsTemplate;

use Order, \Exception;
use App\Repositories\Frontend\GameRepository;
use App\Repositories\Frontend\ServiceRepository;
use App\Repositories\Frontend\UserGoodsRepository;
use App\Repositories\Backend\GoodsTemplateWidgetRepository;

use App\Exceptions\CustomException;
use App\Extensions\Order\Operations\Create;

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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(ServiceRepository $serviceRepository, GameRepository $gameRepository)
    {
        $services = $serviceRepository->available();
        $games = $gameRepository->available();
        return view('frontend.workbench.index', compact('services', 'games'));
    }

    /**
     * 下订单
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

            Order::handle(new Create($userId, $foreignOrderNO, 1, $goodsId, $originalPrice, $quantity, $orderData));
            return response()->ajax(1, '下单成功');
        } catch (CustomException $customException) {
            return response()->ajax(0, '下单失败请联系平台工作人员');
        } catch (Exception $exception) {
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

        return response()->ajax(1, '获取成功', ['child' => explode('|', $valueArr[$request->id])]);
    }

    /**
     * 订单列表获取
     * @param Request $request
     * @param OrderRepository $orderRepository
     */
    public function orderList(Request $request, OrderRepository $orderRepository)
    {
        $status = $request->status;
        $orderNO = $request->order_no;

        $orderRepository->dataList($status, $orderNO);
    }
}

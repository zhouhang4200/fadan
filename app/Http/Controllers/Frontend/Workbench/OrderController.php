<?php

namespace App\Http\Controllers\Frontend\Workbench;

use App\Exceptions\CustomException;
use App\Models\GoodsTemplate;
use App\Repositories\Frontend\GoodsRepository;
use App\Repositories\Backend\GoodsTemplateWidgetRepository;
use App\Repositories\Frontend\UserGoodsRepository;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Order;
use App\Repositories\Frontend\GameRepository;
use App\Repositories\Frontend\ServiceRepository;
use App\Extensions\Order\Operations\Create;

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
            response()->ajax(1, '下单成功');
        } catch (CustomException $customException) {
            response()->ajax(0, '下单失败请联系平台工作人员');
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
}

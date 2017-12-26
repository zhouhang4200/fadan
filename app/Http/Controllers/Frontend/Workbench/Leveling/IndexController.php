<?php

namespace App\Http\Controllers\Frontend\Workbench\Leveling;

use App\Extensions\Order\Operations\CreateLeveling;
use App\Models\GoodsTemplateWidget;
use App\Repositories\Frontend\OrderRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

use App\Models\GoodsTemplate;

use Order, Exception;
use App\Repositories\Frontend\GameRepository;
use App\Exceptions\CustomException;


/**
 * 代练订单
 * Class OrderController
 * @package App\Http\Controllers\Frontend\Workbench
 */
class IndexController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('frontend.workbench.leveling.index');
    }

    /**
     * @param Request $request
     * @param OrderRepository $orderRepository
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderList(Request $request, OrderRepository $orderRepository)
    {
        $no = $request->order_no;
        $pageSize = $request->limit;
        $type = $request->input('type', 0);

        $orders = $orderRepository->levelingDataList($type, $pageSize);


        if ($request->ajax()) {
            if (!in_array($type, ['need', 'ing', 'finish', 'cancel', 'after-sales', 'market', 'cancel', 'search'])) {
                return response()->ajax(0, '不存在的类型');
            }

            $orderArr = [];
            foreach($orders as $item) {
                $orderInfo = $item->toArray();
                $orderInfo['status'] = config('order.status')[$orderInfo['status']] ?? '';
                $orderArr[] = array_merge($item->detail->pluck('field_value', 'field_name')->toArray(), $orderInfo);
            }

            return response()->json([
                'code' => 0,
                'msg' => '',
                'count' => $orders->total(),
                'data' => $orderArr
            ]);
        }
    }

    /**
     * @param GameRepository $gameRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(GameRepository $gameRepository)
    {
        $game = $gameRepository->available();
        return view('frontend.workbench.leveling.create', compact('game'));
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
            $gameId = $orderData['game_id']; // 模版ID
            $templateId = $orderData['id']; // 模版ID
            $originalPrice = $orderData['source_price']; // 原价
            $price  = $orderData['game_leveling_amount']; // 代练价格
            $source  = $orderData['order_source']; // 代练价格
            $foreignOrderNO = isset($orderData['foreign_order_no']) ? $orderData['foreign_order_no'] : ''; // 来源订单号

            try {
                Order::handle(new CreateLeveling($gameId, $templateId, $userId, $foreignOrderNO, $price, $originalPrice, $orderData));
                if (Order::get()->status != 11) {

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
     * 获取游戏模版
     * @param Request $request
     */
    public function getTemplate(Request $request)
    {
        $templateId = GoodsTemplate::where('service_id', 2)
            ->where('game_id', $request->game_id)
            ->value('id');

        $template = GoodsTemplateWidget::select('id', 'field_type', 'field_display_name', 'field_parent_id', 'field_name', 'display_form', 'field_required')
            ->where('goods_template_id', $templateId)
            ->orderBy('field_sortord')
            ->with([
                'values' => function($query){
                    $query->select('goods_template_widget_id', 'field_value')
                        ->where('user_id', 0);
                },
                'userValues' => function($query) {
                    $query->select('goods_template_widget_id', 'field_value')
                        ->where('user_id', Auth::user()->getPrimaryUserId());
                }
            ])
            ->get();
        return response()->ajax(1, 'success', ['template' => $template->toArray(), 'id' => $templateId]);
    }
}


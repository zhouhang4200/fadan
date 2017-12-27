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
use App\Extensions\Dailian\Controllers\DailianFactory;


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
    public function index(Request $request, \App\Repositories\Backend\GameRepository $gameRepository)
    {
        $game = $gameRepository->available();
        return view('frontend.workbench.leveling.index', compact('game'));
    }

    /**
     * @param Request $request
     * @param OrderRepository $orderRepository
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderList(Request $request, OrderRepository $orderRepository)
    {
        $no = $request->input('no', 0);
        $foreignOrderNo = $request->input('foreign_order_no', 0);
        $gameId = $request->input('game_id', 0);
        $status = $request->input('status', 0);
        $wangWang = $request->input('wang_wang', 0);
        $urgentOrder = $request->input('urgent_order', 0);
        $startDate  = $request->input('start_date', 0);
        $endDate = $request->input('end_date', 0);
        $pageSize = $request->input('limit', 10);

        $orders = $orderRepository->levelingDataList($status, $no, $foreignOrderNo, $gameId, $wangWang, $urgentOrder,$startDate, $endDate, $pageSize);

        if ($request->ajax()) {
            if (!in_array($status, array_flip(config('order.status_leveling')))) {
                return response()->ajax(0, '不存在的类型');
            }

            $orderArr = [];
            foreach($orders as $item) {
                $orderInfo = $item->toArray();
                $orderInfo['status_text'] = config('order.status_leveling')[$orderInfo['status']] ?? '';
                $orderInfo['master'] = $orderInfo['creator_primary_user_id'] == Auth::user()->getPrimaryUserId() ? 1 : 0;
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

    public function changeStatus(Request $request)
    {
        $keyWord = $request->keyWord; // 关键字,关联对应的类
        $orderNo = $request->orderNo; // 订单号
        $userId = $request->userId; // 操作人id
        $apiAmount = $request->apiAmount ?? null; // 回传代练费 或 订单安全保证金
        $apiDeposit = $request->apiDeposit ?? null; // 回传双金 或 订单效率保证金
        $apiService = $request->apiService ?? null; // 回传手续费 
        $writeAmount = $request->writeAmount ?? null; // 协商填写的代练费

        try {
            $bool = DailianFactory::choose($keyWord)->run($orderNo, $userId, $apiAmount, $apiDeposit, $apiService, $writeAmount);

            if ($bool) {
                return response()->json(['status' => 1, 'message' => '操作成功!']);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 0, 'message' => '操作失败!']);
        }
    }
}


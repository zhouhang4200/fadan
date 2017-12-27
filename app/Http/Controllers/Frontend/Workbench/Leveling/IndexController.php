<?php

namespace App\Http\Controllers\Frontend\Workbench\Leveling;

use App\Extensions\Order\Operations\CreateLeveling;
use App\Models\GoodsTemplateWidget;
use App\Models\OrderDetail;
use App\Repositories\Backend\GoodsTemplateWidgetRepository;
use App\Repositories\Frontend\OrderRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

use App\Models\GoodsTemplate;

use Order, Exception;
use App\Repositories\Frontend\GameRepository;
use App\Exceptions\CustomException;
use App\Extensions\Dailian\Controllers\DailianFactory;
use App\Models\LevelingConsult;
use App\Models\Order as OrderModel;


/**
 * 代练订单
 * Class OrderController
 * @package App\Http\Controllers\Frontend\Workbench
 */
class IndexController extends Controller
{
    protected  $game;

    /**
     * IndexController constructor.
     * @param GameRepository $gameRepository
     */
    public function __construct(GameRepository $gameRepository)
    {
        $this->game = $gameRepository->available();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $game = $this->game;

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
                $orderInfo['consult'] = $item->levelingConsult ? $item->levelingConsult()->first()->consult : '';
                $orderInfo['complain'] = $item->levelingConsult ? $item->levelingConsult()->first()->complain : '';
                $orderArr[] = array_merge($item->detail->pluck('field_value', 'field_name')->toArray(), $orderInfo);
            }

            return response()->json([
                'code' => 0,
                'msg' => '',
                'count' => $orders->total(),
                'data' => $orderArr,
            ]);
        }
    }

    /**
     * @param GameRepository $gameRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(GameRepository $gameRepository)
    {
        $game = $this->game;
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
     * @param GoodsTemplateWidgetRepository $goodsTemplateWidgetRepository
     * @return mixed
     */
    public function getTemplate(Request $request, GoodsTemplateWidgetRepository $goodsTemplateWidgetRepository)
    {
        // 获取对应的模版ID
        $templateId = GoodsTemplate::getTemplateId(2, $request->game_id);
        // 获取对应的模版组件
        $template = $goodsTemplateWidgetRepository->getWidgetBy($templateId);

        return response()->ajax(1, 'success', ['template' => $template->toArray(), 'id' => $templateId]);
    }

    /**
     * 订单详情
     * @param Request $request
     * @param OrderRepository $orderRepository
     * @param GoodsTemplateWidgetRepository $goodsTemplateWidgetRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail(Request $request,
                           OrderRepository $orderRepository,
                           GoodsTemplateWidgetRepository $goodsTemplateWidgetRepository)
    {
        // 获取可用游戏
        $game = $this->game;
        // 获取订单数据
        $detail = $orderRepository->levelingDetail($request->no);
        // 获取订单对应模版ID
        $templateId = GoodsTemplate::getTemplateId(2, $detail['game_id']);
        // 获取对应的模版组件
        $template = $goodsTemplateWidgetRepository->getWidgetBy($templateId);

        return view('frontend.workbench.leveling.detail', compact('detail', 'template', 'game'));
    }

    /**
     * 更新订单
     * @param Request $request
     */
    public function update(Request $request, OrderRepository $orderRepository)
    {
        $orderNo = $request->no;
        $orderRepository->levelingDetail($orderNo);

        $order = Order::where('no', $orderNo)->first();
        $orderDetail = OrderDetail::where('order_no', $orderNo)->get();

        // 下架 没有接单 更新所有信息
        if(in_array($order->status, [1, 23])) {
            // 加价 修改主单信息


            // 其它信息只需改订单详情表
        }

        // 已接单  异常 更新部分信息
        if (in_array($order->status, [13, 17])) {
            // 加价 修改主单信息

            // 其它信息只需改订单详情表

        }
        // 待验收 可加价格
        if ($order->status == 14) {

        }
        // 状态锁定 可改密码
        if ($order->status == 18) {

        }

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

    /**
     * 撤销
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function consult(Request $request)
    {
        $data['order_no'] = $request->orderNo;
        $data['amount'] = $request->data['amount'];
        $data['deposit'] = $request->data['deposit'];
        $data['user_id'] = Auth::id();
        $data['revoke_message'] = $request->data['revoke_message'];

        $order = OrderModel::where('no', $data['order_no'])->first();

        if (Auth::user()->getPrimaryUserId() == $order->creator_primary_user_id) {
            $data['consult'] = 1; // 发单方提出撤销
        } else if (Auth::user()->getPrimaryUserId() == $order->gainer_primary_user_id) {
            $data['consult'] = 2; // 接单方
        } else {
            return response()->ajax(0, '操作失败!');
        }


        $bool = LevelingConsult::UpdateOrcreate(['order_no' => $data['order_no']], $data);

        if ($bool) {
            return response()->ajax(1, '操作成功!');
        }
        return response()->ajax(0, '操作失败!');
    }

    /**
     * 申诉
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function complete(Request $request)
    {
        $data['order_no'] = $request->orderNo;
        $data['complain_message'] = $request->data['complain_message'];

        $order = OrderModel::where('no', $data['order_no'])->first();

        if (Auth::user()->getPrimaryUserId() == $order->creator_primary_user_id) {
            $data['complain'] = 1; // 发单方提出申诉
        } else if (Auth::user()->getPrimaryUserId() == $order->gainer_primary_user_id) {
            $data['complain'] = 2; // 接单方
        } else {
            return response()->ajax(0, '操作失败!');
        }

        $bool = LevelingConsult::where('order_no', $data['order_no'])->update($data);

        if ($bool) {
            return response()->ajax(1, '操作成功!');
        }
        return response()->ajax(0, '操作失败!');
    }
}


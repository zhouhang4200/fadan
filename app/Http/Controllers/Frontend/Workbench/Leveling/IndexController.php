<?php

namespace App\Http\Controllers\Frontend\Workbench\Leveling;

use App\Extensions\Asset\Expend;
use App\Extensions\Asset\Income;
use App\Extensions\Order\ForeignOrder\ForeignOrder;
use App\Extensions\Order\Operations\CreateLeveling;
use App\Http\Controllers\Backend\Data\DayDataController;
use App\Models\Game;
use App\Models\GoodsTemplateWidget;
use App\Models\GoodsTemplateWidgetValue;
use App\Models\OrderDetail;
use App\Models\Order as OrderModel;
use App\Models\SmsSendRecord;
use App\Models\User;
use App\Repositories\Frontend\GoodsTemplateWidgetRepository;
use App\Repositories\Frontend\OrderDetailRepository;
use App\Repositories\Frontend\OrderRepository;
use App\Repositories\Frontend\GoodsTemplateWidgetValueRepository;
use App\Repositories\Frontend\OrderHistoryRepository;
use App\Services\SmSApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

use App\Models\GoodsTemplate;

use DB, Order, Exception, Asset;
use App\Repositories\Frontend\GameRepository;
use App\Exceptions\CustomException;
use App\Extensions\Dailian\Controllers\DailianFactory;
use App\Models\LevelingConsult;
use App\Services\Show91;
use Excel;
use App\Exceptions\DailianException;
use App\Repositories\Frontend\OrderAttachmentRepository;
use App\Listeners\Leveling\ChangeStatus;
use App\Events\AutoRequestInterface;

/**
 * 代练订单
 * Class OrderController
 * @package App\Http\Controllers\Frontend\Workbench
 */
class IndexController extends Controller
{
    protected $game;

    /**
     * IndexController constructor.
     * @param GameRepository $gameRepository
     */
    public function __construct(GameRepository $gameRepository)
    {
        $this->game = $gameRepository->available();
    }

    /**
     * @param Request $request
     * @param OrderRepository $orderRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, OrderRepository $orderRepository)
    {
        $game = $this->game;
        $employee = User::where('parent_id', Auth::user()->getPrimaryUserId())->get();
        $tags = GoodsTemplateWidgetValueRepository::getTags(Auth::user()->getPrimaryUserId());

        return view('frontend.workbench.leveling.index', compact('game', 'employee', 'tags'));
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
        $startDate = $request->input('start_date', 0);
        $endDate = $request->input('end_date', 0);
        $label = $request->input('label');
        $pageSize = $request->input('limit', 10);

        if ($request->export) {

            $options = compact('no', 'foreignOrderNo', 'gameId', 'status', 'wangWang', 'urgentOrder', 'startDate', 'endDate');

            return redirect(route('frontend.workbench.leveling.excel'))->with(['options' => $options]);
        }

        $orders = $orderRepository->levelingDataList($status, $no, $foreignOrderNo, $gameId, $wangWang, $urgentOrder, $startDate, $endDate, $label, $pageSize);

        if ($request->ajax()) {
            if (!in_array($status, array_flip(config('order.status_leveling')))) {
                return response()->ajax(0, '不存在的类型');
            }

            $orderArr = [];
            foreach ($orders as $item) {
                $orderInfo = $item->toArray();

                // 删掉无用的数据
                unset($orderInfo['detail']);

                $orderInfo['status_text'] = config('order.status_leveling')[$orderInfo['status']] ?? '';
                $orderInfo['master'] = $orderInfo['creator_primary_user_id'] == Auth::user()->getPrimaryUserId() ? 1 : 0;
                $orderInfo['consult'] = $orderInfo['leveling_consult']['consult'] ?? '';
                $orderInfo['complain'] = $orderInfo['leveling_consult']['complain'] ?? '';

                // 当前订单数据
                $orderCurrent = array_merge($item->detail->pluck('field_value', 'field_name')->toArray(), $orderInfo);

                if (!in_array($orderInfo['status'], [19, 20, 21])){
                    $orderCurrent['payment_amount'] = '';
                    $orderCurrent['get_amount'] = '';
                    $orderCurrent['poundage'] = '';
                    $orderCurrent['profit'] = '';
                } else {
                    // 支付金额
                    if ($orderInfo['status'] == 21) {
                        $amount = $orderInfo['leveling_consult']['api_amount'];
                    } else {
                        $amount = $orderInfo['leveling_consult']['amount'];
                    }
                    // 支付金额
                    $orderCurrent['payment_amount'] = $amount !=0 ?  $amount:  $orderInfo['amount'];
                    // 利润
                    if (isset($orderCurrent['source_price'])) {
                        $orderCurrent['profit'] = ($orderCurrent['source_price'] ?? 0) - ($orderCurrent['payment_amount']?? 0) +  ($orderCurrent['get_amount']?? 0) - ($orderCurrent['poundage']?? 0);
                    }

                    $orderCurrent['payment_amount'] = (float)$orderCurrent['payment_amount'];
                    $orderCurrent['get_amount'] = (float)$orderCurrent['get_amount'];
                    $orderCurrent['poundage'] = (float)$orderCurrent['poundage'];
                    $orderCurrent['profit'] = (float)$orderCurrent['profit'];
                }

                $days = $orderCurrent['game_leveling_day'] ?? 0;
                $hours = $orderCurrent['game_leveling_hour'] ?? 0;
                $orderCurrent['leveling_time'] = $days . '天' . $hours . '小时'; // 代练时间

                // 如果存在接单时间
                if (isset($orderCurrent['receiving_time']) && !empty($orderCurrent['receiving_time'])) {
                    // 计算到期的时间戳
                    $expirationTimestamp = strtotime($orderCurrent['receiving_time']) + $days * 86400 + $hours * 3600;
                    // 计算剩余时间
                    $leftSecond = $expirationTimestamp - time();
                    $orderCurrent['left_time'] = Sec2Time($leftSecond); // 剩余时间
                } else {
                    $orderCurrent['left_time'] = '';
                }

                $orderArr[] = $orderCurrent;
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
            $templateId = GoodsTemplate::where('game_id', $gameId)->where('service_id', 4)->value('id'); // 模版ID
            $originalPrice = $orderData['source_price']; // 原价
            $price = $orderData['game_leveling_amount']; // 代练价格
            $source = $orderData['order_source']; // 代练价格
            $foreignOrderNO = isset($orderData['foreign_order_no']) ? $orderData['foreign_order_no'] : ''; // 来源订单号

            // 获取当前下单人名字
            $orderData['cstomer_service_name'] = Auth::user()->name;

            try {
                Order::handle(new CreateLeveling($gameId, $templateId, $userId, $foreignOrderNO, $price, $originalPrice, $orderData));

                return response()->ajax(1, '下单成功');

            } catch (CustomException $exception) {
                return response()->ajax(0, $exception->getMessage());
            } catch (DailianException $dailianException) {
                return response()->ajax(0, $dailianException->getMessage());
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
        $templateId = GoodsTemplate::getTemplateId(4, $request->game_id);
        // 获取对应的模版组件
        $template = $goodsTemplateWidgetRepository->getWidgetBy($templateId);
        // 如果有订单号则获取订单原来设置的值
        $orderTemplateValue = [];
        if ($request->no) {
            $orderTemplateValue = OrderDetailRepository::getByOrderNo($request->no);
        }
        return response()->ajax(1, 'success', ['template' => $template->toArray(), 'id' => $templateId, 'value' => $orderTemplateValue]);
    }

    /**
     * 获取下拉项的子项
     * @param Request $request
     */
    public function getSelectChild(Request $request)
    {
        return GoodsTemplateWidgetValue::where('parent_id', $request->parent_id)->get();
    }

    /**
     * 订单详情
     * @param Request $request
     * @param OrderRepository $orderRepository
     * @param GoodsTemplateWidgetRepository $goodsTemplateWidgetRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail(Request $request, OrderRepository $orderRepository, GoodsTemplateWidgetRepository $goodsTemplateWidgetRepository)
    {
        // 获取可用游戏
        $game = $this->game;
        // 获取订单数据
        $detail = $orderRepository->levelingDetail($request->no);
        // 获取订单对应模版ID
        $templateId = GoodsTemplate::getTemplateId(4, $detail['game_id']);
        // 获取对应的模版组件
        $template = $goodsTemplateWidgetRepository->getWidgetBy($templateId);

        $detail['master'] = $detail['creator_primary_user_id'] == Auth::user()->getPrimaryUserId() ? 1 : 0;
        $detail['consult'] = $detail['leveling_consult']['consult'] ?? '';
        $detail['complain'] = $detail['leveling_consult']['complain'] ?? '';


        $days = $detail['game_leveling_day'] ?? 0;
        $hours = $detail['game_leveling_hour'] ?? 0;
        $detail['leveling_time'] = $days . '天' . $hours . '小时'; // 代练时间

        // 如果存在接单时间
        if (isset($detail['receiving_time']) && !empty($detail['receiving_time'])) {
            // 计算到期的时间戳
            $expirationTimestamp = strtotime($detail['receiving_time']) + $days * 86400 + $hours * 3600;
            // 计算剩余时间
            $leftSecond = $expirationTimestamp - time();
            $detail['left_time'] = Sec2Time($leftSecond); // 剩余时间
        } else {
            $detail['left_time'] = '';
        }

        // 撤销说明
        if(isset($detail['leveling_consult']['consult']) && $detail['leveling_consult']['consult'] != 0) {

            //发起人的主ID 与 当前主ID一样则撤销发起人
            $user = User::where('id', $detail['leveling_consult']['user_id'])->first();

            if ($user->getPrimaryUserId() == Auth::user()->getPrimaryUserId()) {
                $text = '你进行撤销操作';
            } else {
                $text = '对方进行撤销操作';
            }

            if ($detail['creator_primary_user_id'] == Auth::user()->getPrimaryUserId()) {
                $text .= '你支付代练费' . $detail['leveling_consult']['api_service'] . '元';
                $text .= '对方支付保证金' . $detail['leveling_consult']['api_deposit'] . '元' . '原因：' . $detail['leveling_consult']['revoke_message'];
            } else {
                $text .= '对方支付代练费' . $detail['leveling_consult']['api_service'] . '元';
                $text .= '你支付保证金' . $detail['leveling_consult']['api_deposit'] . '元' . '原因：' . $detail['leveling_consult']['revoke_message'];
            }
            $detail['consult_desc'] = $text;
        }
        // 仲裁说明
        if(isset($detail['leveling_consult']['complete']) && $detail['leveling_consult']['complain'] != 0) {
            //发起人的主ID 与 当前主ID一样则仲裁发起人
            $user = User::where('id', $detail['leveling_consult']['user_id'])->first();
            if ($user->getPrimaryUserId() == Auth::user()->getPrimaryUserId()) {
                $text = '你进行仲裁操作 原因：' . $detail['leveling_consult']['complain_message'];
            } else {
                $text = '对方进行仲裁操作 原因：' . $detail['leveling_consult']['complain_message'];
            }
            $detail['complain_desc'] = $text;
        }

        // 仲裁结果
        if(isset($detail['leveling_consult']['complete']) && $detail['leveling_consult']['complete'] == 2) {

            $text = '客服进行了仲裁';
            if ($detail['creator_primary_user_id'] == Auth::user()->getPrimaryUserId()) {
                $text .= '你支付代练费' .  $detail['leveling_consult']['api_service'] . '元';
                $text .= '对方支付保证金' . $detail['leveling_consult']['api_deposit'] . '元';
            } else {
                $text .= '对方支付代练费' . $detail['leveling_consult']['api_service'] . '元';
                $text .= '你支付保证金' . $detail['leveling_consult']['api_deposit'] . '元';
            }

            $detail['complain_result'] = $text;
        }

        return view('frontend.workbench.leveling.detail', compact('detail', 'template', 'game'));
    }

    /**
     * 重下单
     * @param OrderRepository $orderRepository
     * @param GoodsTemplateWidgetRepository $goodsTemplateWidgetRepository
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @internal param Request $request
     */
    public function repeat(OrderRepository $orderRepository, GoodsTemplateWidgetRepository $goodsTemplateWidgetRepository, $id)
    {
        // 获取可用游戏
        $game = $this->game;
        // 获取订单数据
        $detail = $orderRepository->levelingDetail($id);
        // 获取订单对应模版ID
        $templateId = GoodsTemplate::getTemplateId(4, $detail['game_id']);
        // 获取对应的模版组件
        $template = $goodsTemplateWidgetRepository->getWidgetBy($templateId);
        // 写入订单关联数据
        $detail['master'] = $detail['creator_primary_user_id'] == Auth::user()->getPrimaryUserId() ? 1 : 0;
        $detail['consult'] = $detail['leveling_consult']['consult'] ?? '';
        $detail['complain'] = $detail['leveling_consult']['complain'] ?? '';

        return view('frontend.workbench.leveling.repeat', compact('detail', 'template', 'game'));
    }

    /**
     * 操作记录
     * @param $order_no
     * @return mixed
     */
    public function history($order_no)
    {
        $dataList = OrderHistoryRepository::dataList($order_no);
        $html = view('frontend.workbench.leveling.history', compact('dataList'))->render();
        return response()->ajax(1, 'success', $html);
    }

    /**
     * 从show91接口拿留言数据
     * @param $order_no
     * @return mixed
     */
    public function leaveMessage($order_no)
    {
        // 取订单信息
        $order = (new OrderRepository)->detail($order_no);
        if (empty($order)) {
            return response()->ajax(0, '订单不存在');
        }

        // 取订单详情
        $orderDetail = $order->detail->pluck('field_value', 'field_name');
        // 第三方单号
        $thirdOrderNo = $orderDetail['third_order_no'] ?? '';

        try {
            $dataList = Show91::messageList(['oid' => $thirdOrderNo]);
        } catch (CustomException $e) {
            return response()->ajax(0, $e->getMessage());
        }

        $show91Uid = config('show91.uid');

        $html = view('frontend.workbench.leveling.leave-message', compact('dataList', 'show91Uid'))->render();
        return response()->ajax(1, 'success', $html);
    }

    /**
     * 从show91接口拿截图数据
     * @param $order_no
     * @return mixed
     */
    public function leaveImage($order_no)
    {
        try {
            $dataList = OrderAttachmentRepository::dataList($order_no);
        } catch (CustomException $e) {
            return response()->ajax($e->getCode(), $e->getMessage());
        }

        $html = view('frontend.workbench.leveling.leave-image', compact('dataList'))->render();
        return response()->ajax(1, 'success', $html);
    }

    /**
     * 上传截图后，推送到show91
     * @param Request $request
     * @return mixed
     * @internal param $order_no
     */
    public function uploadImage(Request $request)
    {
        $orderNo = $request->order_no;
        $description = $request->description ?: '无';
        if (empty($orderNo)) {
            return response()->ajax(0, '单号缺失');
        }

        if (!$request->file('image')->isValid()) {
            return response()->ajax(0, '上传失败');
        }

        $diskName = 'order';
        $fileName = $request->file('image')->store('', $diskName);

        try {
            OrderAttachmentRepository::saveImageAndUploadToShow91($orderNo, $diskName, $fileName, $description);
        } catch (CustomException $e) {
            return response()->ajax(0, $e->getMessage());
        }

        return response()->ajax(1);
    }

    /**
     * 向show91接口发送留言
     * @param Request $request
     * @return mixed
     * @internal param $oid 91的订单id
     */
    public function sendMessage(Request $request)
    {
        $oid = $request->oid ?: 'ORD180115104933226951';
        $mess = $request->mess ?: '12341234';
        if (empty($oid)) {
            return response()->ajax(0, '单号不正确');
        }

        try {
            $res = Show91::addMess(['oid' => $oid, 'mess' => $mess]);
        } catch (CustomException $e) {
            return response()->ajax($e->getCode(), $e->getMessage());
        }

        return response()->ajax(1);
    }

    /**
     * 更新订单
     * @param Request $request
     * @param OrderRepository $orderRepository
     * @param GoodsTemplateWidgetRepository $goodsTemplateWidgetRepository
     */
    public function update(Request $request, OrderRepository $orderRepository, GoodsTemplateWidgetRepository $goodsTemplateWidgetRepository)
    {
        $requestData = $request->data;
        $orderNo = $requestData['no'];

        DB::beginTransaction();
        try {

            $order = OrderModel::where('no', $orderNo)->lockForUpdate()->first();
            $orderDetail = OrderDetail::where('order_no', $orderNo)->pluck('field_value', 'field_name');
            $orderDetailDisplayName = OrderDetail::where('order_no', $orderNo)->pluck('field_display_name', 'field_name');

            // 如果本次修改提交，游戏ID与原订单不同则删除原有订单详情，写入新的值
            if ($requestData['game_id'] != $order->game_id) {
                // 删除原订单详情
                OrderDetail::where('order_no', $orderNo)->delete();
                // 找到对应的游戏ID模版ID
                $templateId = GoodsTemplate::where('game_id', $requestData['game_id'])->where('service_id', 4)->value('id');
                // 按模填入订单详情数据
                OrderDetailRepository::create($templateId, $orderNo, $requestData);
                // 本次修改与原单价不同则对进对应的资金操作
                if ($order->price != $requestData['game_leveling_amount']) {
                    // 加价
                    if ($order->price < $requestData['game_leveling_amount']) {
                        $amount = $requestData['game_leveling_amount'] - $order->price;
                        Asset::handle(new Expend($amount, 77, $orderNo, '代练改价支出', $order->creator_primary_user_id));

                        $order->price = $requestData['game_leveling_amount'];
                        $order->amount = $requestData['game_leveling_amount'];
                        $order->save();

                        OrderDetail::where('order_no', $orderNo)->where('field_name', 'game_leveling_amount')->update([
                            'field_value' => $requestData['game_leveling_amount']
                        ]);
                    } else { // 减价
                        $amount = $order->price - $requestData['game_leveling_amount'];
                        Asset::handle(new Income($amount, 814, $orderNo, '代练改价退款', $order->creator_primary_user_id));

                        $order->price = $requestData['game_leveling_amount'];
                        $order->amount = $requestData['game_leveling_amount'];
                        $order->save();

                        OrderDetail::where('order_no', $orderNo)->where('field_name', 'game_leveling_amount')->update([
                            'field_value' => $requestData['game_leveling_amount']
                        ]);
                    }
                }
                // 更改订单数据
                $order->game_id = $requestData['game_id'];
                $order->game_name = Game::where('id', $requestData['game_id'])->value('name');
                $order->price = $requestData['game_leveling_amount'];
                $order->amount = $requestData['game_leveling_amount'];
                $order->save();

            } else {
                // 下架 没有接单 更新所有信息
                if (in_array($order->status, [1, 23])) {
                    $changeValue = '';
                    // 加价 修改主单信息
                    if ($order->price != $requestData['game_leveling_amount']) {
                        // 加价
                        if ($order->price < $requestData['game_leveling_amount']) {
                            $amount = $requestData['game_leveling_amount'] - $order->price;
                            Asset::handle(new Expend($amount, 77, $orderNo, '代练改价支出', $order->creator_primary_user_id));

                            $order->price = $requestData['game_leveling_amount'];
                            $order->amount = $requestData['game_leveling_amount'];
                            $order->save();

                            OrderDetail::where('order_no', $orderNo)->where('field_name', 'game_leveling_amount')->update([
                                'field_value' => $requestData['game_leveling_amount']
                            ]);
                        } else { // 减价
                            $amount = $order->price - $requestData['game_leveling_amount'];
                            Asset::handle(new Income($amount, 814, $orderNo, '代练改价退款', $order->creator_primary_user_id));

                            $order->price = $requestData['game_leveling_amount'];
                            $order->amount = $requestData['game_leveling_amount'];
                            $order->save();

                            OrderDetail::where('order_no', $orderNo)->where('field_name', 'game_leveling_amount')->update([
                                'field_value' => $requestData['game_leveling_amount']
                            ]);
                        }
                    }

                    // 其它信息只需改订单详情表
                    foreach ($requestData as $key => $value) {
                        if (isset($orderDetail[$key])) {
                            if ($orderDetail[$key] != $value) {
                                // 更新值
                                OrderDetail::where('order_no', $orderNo)->where('field_name', $key)->update([
                                    'field_value' => $value
                                ]);
                                $changeValue .= $orderDetailDisplayName[$key] . '更改前：' . $value . ' 更改后：' . $requestData[$key] . '<br/>';
                            }
                        }
                    }
                    // 手动触发调用外部接口时间
                    $order = OrderModel::where('no', $order->no)->first();

                    event(new AutoRequestInterface($order, 'addOrder', true));
                }

                // 已接单  异常 更新部分信息 （加价 加时间天 加时间小时 修改密码 ）
                if (in_array($order->status, [13, 17])) {
                    // 加价 修改主单信息
                    if ($order->price < $requestData['game_leveling_amount']) {
                        $addAmount = bcsub($request->data['game_leveling_amount'], $order->amount, 2);
                        $amount = $requestData['game_leveling_amount'] - $order->price;
                        Asset::handle(new Expend($amount, 77, $orderNo, '代练改价支出', $order->creator_primary_user_id));

                        $order->price = $requestData['game_leveling_amount'];
                        $order->amount = $requestData['game_leveling_amount'];
                        $order->save();

                        OrderDetail::where('order_no', $orderNo)->where('field_name', 'game_leveling_amount')->update([
                            'field_value' => $requestData['game_leveling_amount']
                        ]);
                        // 接口加价
                        $order->addAmount = $addAmount;
                        event(new AutoRequestInterface($order, 'addPrice'));
                    } else if ($order->price > $requestData['game_leveling_amount']) {
                        return response()->ajax(0, '代练价格只可增加');
                    }
                    // 修改密码
                    if ($requestData['password'] != $orderDetail['password']) {
                        // 更新值
                        OrderDetail::where('order_no', $orderNo)->where('field_name', 'password')->update([
                            'field_value' => $requestData['password']
                        ]);
                    }

                    // 修改 游戏代练天
                    if ($requestData['game_leveling_day'] != $orderDetail['game_leveling_day'] && $requestData['game_leveling_day'] > $orderDetail['game_leveling_day']) {
                        // 接口增加天数
                        $addDays = bcsub($request->data['game_leveling_day'], $order->detail()->where('field_name', 'game_leveling_day')->value('field_value'), 0);
                        // 更新值
                        OrderDetail::where('order_no', $orderNo)->where('field_name', 'game_leveling_day')->update([
                            'field_value' => $requestData['game_leveling_day']
                        ]);
                    } else if ($requestData['game_leveling_day'] != $orderDetail['game_leveling_day']) {
                        return response()->ajax(0, '代练时间只可增加');
                    }

                    if ($requestData['game_leveling_hour'] != $orderDetail['game_leveling_hour'] && ($requestData['game_leveling_hour'] > $orderDetail['game_leveling_hour']
                            || ($requestData['game_leveling_hour'] < $orderDetail['game_leveling_hour'] && $requestData['game_leveling_day'] > $orderDetail['game_leveling_day']))
                    ) {
                        $addHours = bcsub($request->data['game_leveling_hour'], $order->detail()->where('field_name', 'game_leveling_hour')->value('field_value'), 0);
                        // 更新值
                        OrderDetail::where('order_no', $orderNo)->where('field_name', 'game_leveling_hour')->update([
                            'field_value' => $requestData['game_leveling_hour']
                        ]);
                    }

                    if (isset($addDays) && !isset($addHours)) {
                        $order->addDays = $addDays;
                        $order->addHours = 0;
                        event(new AutoRequestInterface($order, 'addLimitTime'));
                    } elseif (!isset($addDays) && isset($addHours)) {
                        $order->addDays = 0;
                        $order->addHours = $addHours;
                        event(new AutoRequestInterface($order, 'addLimitTime'));
                    } elseif (isset($addDays) && isset($addHours)) {
                        $order->addDays = $addDays;
                        $order->addHours = $addHours;
                        event(new AutoRequestInterface($order, 'addLimitTime'));
                    }
                }
                // 待验收 可加价格
                if ($order->status == 14) {
                    if ($order->price < $requestData['game_leveling_amount']) {
                        $addAmount = bcsub($request->data['game_leveling_amount'], $order->amount, 2);
                        $amount = $requestData['game_leveling_amount'] - $order->price;
                        Asset::handle(new Expend($amount, 77, $orderNo, '代练改价支出', $order->creator_primary_user_id));

                        $order->price = $requestData['game_leveling_amount'];
                        $order->amount = $requestData['game_leveling_amount'];
                        $order->save();

                        OrderDetail::where('order_no', $orderNo)->where('field_name', 'game_leveling_amount')->update([
                            'field_value' => $requestData['game_leveling_amount']
                        ]);
                        // 接口加价
                        $order->addAmount = $addAmount;
                        event(new AutoRequestInterface($order, 'addPrice'));
                    } else {
                        return response()->ajax(1, '代练价格只可增加');
                    }
                }
                // 状态锁定 可改密码
                if ($order->status == 18) {
                    // 修改密码
                    if ($requestData['password'] != $orderDetail['password']) {
                        // 更新值
                        OrderDetail::where('order_no', $orderNo)->where('field_name', 'password')->update([
                            'field_value' => $requestData['password']
                        ]);
                    }
                }

                // 其它信息只需改订单详情表
                foreach ($requestData as $key => $value) {
                    if (isset($orderDetail[$key])) {
                        if ($orderDetail[$key] != $value && in_array($key, ['urgent_order', 'label', 'order_source', 'foreign_order_no', 'source_price', 'client_name', 'client_phone', 'client_qq', 'client_wang_wang', 'game_leveling_require_day', 'game_leveling_require_hour', 'cstomer_service_remark'])) {
                            // 更新值
                            OrderDetail::where('order_no', $orderNo)->where('field_name', $key)->update([
                                'field_value' => $value
                            ]);
                            if ($key == 'source_price') {
                                $order->original_amount = $requestData[$key];
                                $order->save();
                            }
//                            $changeValue .= $orderDetailDisplayName[$key] . '更改前：' . $value . ' 更改后：' . $requestData[$key] . '<br/>';
                        }
                    }
                }
            }

        } catch (CustomException $customException) {
            DB::rollBack();
            return response()->ajax(0, '修改失败');
        } catch (DailianException $e) {
            DB::rollBack();
            return response()->ajax(0, '修改失败');
        }
        DB::commit();

        return response()->ajax(1, '修改成功');
    }

    /**
     * 订单操作 改变订单状态
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeStatus(Request $request)
    {
        $keyWord = $request->keyWord; // 关键字,关联对应的类
        $orderNo = $request->orderNo; // 订单号
        $userId = Auth::id(); // 操作人id

        DB::beginTransaction();
        try {
            // 加急 取消加急
            $bool = false;
            if (in_array($keyWord, ['urgent', 'unUrgent'])) {
                OrderDetail::where('creator_primary_user_id', Auth::user()->getPrimaryUserId())
                    ->where('order_no', $orderNo)
                    ->where('field_name', 'urgent_order')
                    ->update(['field_value' => $keyWord == 'urgent' ? 1 : 0]);
                // 写操作记录
                $bool = true;
            } else {
                $bool = DailianFactory::choose($keyWord)->run($orderNo, $userId);
            }
        } catch (DailianException $e) {
            DB::rollback();
            return response()->json(['status' => 0, 'message' => $e->getMessage()]);
        }
        DB::commit();
        return response()->json(['status' => 1, 'message' => '操作成功!']);
    }

    /**
     * 撤销
     * @param  Request $request [description]
     * @return \Illuminate\Http\JsonResponse [type]           [description]
     */
    public function consult(Request $request)
    {
        DB::beginTransaction();
        try {
            $data['order_no'] = $request->orderNo;
            $data['amount'] = $request->data['amount'];
            $data['deposit'] = $request->data['deposit'];
            $data['user_id'] = Auth::id();
            $data['revoke_message'] = $request->data['revoke_message'];
            // 订单数据
            $order = OrderModel::where('no', $data['order_no'])->first();
            // 订单双金
            $safeDeposit = $order->detail()->where('field_name', 'security_deposit')->value('field_value');
            $effectDeposit = $order->detail()->where('field_name', 'efficiency_deposit')->value('field_value');
            $orderDeposit = bcadd($safeDeposit, $effectDeposit);
            $isOverDeposit = bcsub($orderDeposit, $data['deposit']);
            $isOverAmount = bcsub($order->amount, $data['amount']);
            // 写入双金与订单双击比较
            if ($isOverDeposit < 0) {
                return response()->ajax(0, '操作失败！要求退回双金金额大于订单双金!');
            }
            // 写入代练费与订单代练费比较
            if ($isOverAmount < 0) {
                return response()->ajax(0, '操作失败！要求退回代练费大于订单代练费!');
            }
            // 判断是接单还是发单方操作
            if (Auth::user()->getPrimaryUserId() == $order->creator_primary_user_id) {
                $data['consult'] = 1; // 发单方提出撤销
            } else if (Auth::user()->getPrimaryUserId() == $order->gainer_primary_user_id) {
                $data['consult'] = 2; // 接单方
            } else {
                return response()->ajax(0, '找不到订单对应的接单或发单人!');
            }
            // 存信息
            LevelingConsult::updateOrCreate(['order_no' => $data['order_no']], $data);
            // 改状态
            DailianFactory::choose('revoke')->run($data['order_no'], Auth::id());
        } catch (DailianException $e) {
            DB::rollBack();
            return response()->ajax(0, $e->getMessage());
        }
        DB::commit();
        return response()->json(['status' => 1, 'message' => '操作成功!']);
    }

    /**
     * 申诉
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function complain(Request $request)
    {
        DB::beginTransaction();
        try {
            $userId = Auth::id();
            $data['user_id'] = Auth::id();
            $data['order_no'] = $request->orderNo;
            $data['complain_message'] = $request->data['complain_message'];

            $order = OrderModel::where('no', $data['order_no'])->first();
            // 操作人是发单还是接单
            if (Auth::user()->getPrimaryUserId() == $order->creator_primary_user_id) {
                $data['complain'] = 1; // 发单方提出申诉
            } else if (Auth::user()->getPrimaryUserId() == $order->gainer_primary_user_id) {
                $data['complain'] = 2; // 接单方
            } else {
                return response()->ajax(0, '操作失败!');
            }

            LevelingConsult::where('order_no', $data['order_no'])->updateOrCreate(['order_no' => $data['order_no']], $data);
            // 改状态
            DailianFactory::choose('applyArbitration')->run($data['order_no'], $userId);
        } catch (DailianException $e) {
            DB::rollBack();
            return response()->ajax(0, $e->getMessage());
        }
        DB::commit();
        return response()->ajax(1, '操作成功!');
    }

    /**
     * 发送短信
     * @param Request $request
     * @return mixed
     */
    public function sendSms(Request $request)
    {
        $orderInfo = OrderModel::where('no', $request->no)
            ->where('creator_primary_user_id', Auth::user()->getPrimaryUserId())
            ->with('detail')
            ->first();

        if ($orderInfo) {
            $orderArr = array_merge($orderInfo->detail->pluck('field_value', 'field_name')->toArray(), $orderInfo->toArray());

            if (isset($orderArr['client_phone']) && $orderArr['client_phone']) {
                $sendResult = (new SmSApi())->send(2, $orderArr['client_phone'], $request->contents, $orderInfo->creator_primary_user_id);

                if ((bool)strpos($sendResult, "mterrcode=000")) {
                    // 发送成功写发送记录 , Auth::user()->getPrimaryUserId()
                    SmsSendRecord::create([
                        'primary_user_id' => Auth::user()->getPrimaryUserId(),
                        'user_id' => Auth::user()->id,
                        'order_no' => $orderInfo->no,
                        'client_phone' => $orderArr['client_phone'],
                        'content' => $request->contents,
                    ]);
                    return response()->ajax(1, '发送成功!');
                }
            } else {
                return response()->ajax(0, '您没有填写客户手机号');
            }
        }
        return response()->ajax(0, '发送失败');
    }

    /**
     * @param Request $request
     * @param OrderRepository $orderRepository
     * @return \Illuminate\Http\RedirectResponse
     */
    public function excel(Request $request, OrderRepository $orderRepository)
    {
        $no = $request->no ?? 0;
        $foreignOrderNo = $request->foreignOrderNo ?? 0;
        $gameId = $request->gameId ?? 0;
        $wangWang = $request->wangWang ?? 0;
        $startDate = $request->startDate ?? 0;
        $endDate = $request->endDate ?? 0;
        $status = $request->status ?? 0;
        $urgentOrder = $request->urgentOrder ?? 0;
        $serviceId = 2;

        $orderRepository->levelingExport(compact('serviceId', 'status', 'no', 'foreignOrderNo', 'gameId', 'wangWang', 'urgentOrder', 'startDate', 'endDate'));
    }

    /**
     * 待发单
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function wait()
    {
        return view('frontend.workbench.leveling.wait');
    }

    /**
     * 待发单数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function waitList(Request $request)
    {
        $no = $request->no;
        $wangWang = $request->wang_wang;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $orders = \App\Models\ForeignOrder::filter(compact('no', 'wangWang', 'startDate', 'endDate'))
            ->where('gainer_primary_user_id', Auth::user()->getPrimaryUserId())
            ->where('status', 1)
            ->paginate(30);

        return response()->json(\View::make('frontend.workbench.leveling.wait-order-list', [
            'no' => $no,
            'orders' => $orders,
            'wangWang' => $wangWang,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ])->render());
    }
}


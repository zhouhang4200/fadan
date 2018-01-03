<?php

namespace App\Http\Controllers\Frontend\Workbench\Leveling;

use App\Extensions\Asset\Expend;
use App\Extensions\Asset\Income;
use App\Extensions\Order\Operations\CreateLeveling;
use App\Models\GoodsTemplateWidget;
use App\Models\OrderDetail;
use App\Models\Order as OrderModel;
use App\Models\User;
use App\Repositories\Backend\GoodsTemplateWidgetRepository;
use App\Repositories\Frontend\OrderRepository;
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
     * @param Request $request
     * @param OrderRepository $orderRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, OrderRepository $orderRepository)
    {
        $game = $this->game;
        $employee = User::where('parent_id', Auth::user()->getPrimaryUserId())->get();

        return view('frontend.workbench.leveling.index', compact('game', 'employee'));
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

        if ($request->export) {

            $options = compact('no', 'foreignOrderNo', 'gameId', 'status', 'wangWang', 'urgentOrder', 'startDate', 'endDate');

            return redirect(route('frontend.workbench.leveling.excel'))->with(['options' => $options]);
        }

        $orders = $orderRepository->levelingDataList($status, $no, $foreignOrderNo, $gameId, $wangWang, $urgentOrder, $startDate, $endDate, $pageSize);

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

                return response()->ajax(1, '下单成功');
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
     * @param OrderRepository $orderRepository
     * @param GoodsTemplateWidgetRepository $goodsTemplateWidgetRepository
     */
    public function update(Request $request, OrderRepository $orderRepository, GoodsTemplateWidgetRepository $goodsTemplateWidgetRepository)
    {
        $requestData = $request->data;
        $orderNo = $requestData['no'];
        $orderRepository->levelingDetail($orderNo);

        $order = OrderModel::where('no', $orderNo)->first();
        $orderDetail = OrderDetail::where('order_no', $orderNo)->pluck('field_value', 'field_name');
        $orderDetailDisplayName = OrderDetail::where('order_no', $orderNo)->pluck('field_display_name', 'field_name');

        // 下架 没有接单 更新所有信息
        if(in_array($order->status, [1, 23])) {
            $changeValue = '';
            // 加价 修改主单信息
            if ($order->price != $requestData['game_leveling_amount']) {
                // 加价
                if ($order->price < $requestData['game_leveling_amount']) {
                    $amount =  $requestData['game_leveling_amount'] - $order->price;
                    Asset::handle(new Expend($amount, Expend::TRADE_SUBTYPE_ORDER_GAME_LEVELING_ADD, $orderNo, '代练改价支出', $order->creator_primary_user_id));

                    $order->price = $requestData['game_leveling_amount'];
                    $order->amount = $requestData['game_leveling_amount'];
                    $order->save();

                    OrderDetail::where('order_no', $orderNo)->where('field_name', 'game_leveling_amount')->update([
                        'field_value' => $requestData['game_leveling_amount']
                    ]);
                } else { // 减价
                    $amount =  $order->price  -  $requestData['game_leveling_amount'];
                    Asset::handle(new Income($amount, Income::TRADE_SUBTYPE_GAME_LEVELING_CHANGE_PRICE, $orderNo, '代练改价退款', $order->creator_primary_user_id));

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
                            'field_value' =>$value
                        ]);
                        $changeValue .= $orderDetailDisplayName[$key] . '更改前：' . $value . ' 更改后：' . $requestData[$key] . '<br/>';
                    }
                }
            }
        }

        // 已接单  异常 更新部分信息 （加价 加时间天 加时间小时 修改密码 ）
        if (in_array($order->status, [13, 17])) {
            // 加价 修改主单信息
            if ($order->price < $requestData['game_leveling_amount']) {
                $amount =  $requestData['game_leveling_amount'] - $order->price;
                Asset::handle(new Expend($amount, Expend::TRADE_SUBTYPE_ORDER_GAME_LEVELING_ADD, $orderNo, '代练改价支出', $order->creator_primary_user_id));

                $order->price = $requestData['game_leveling_amount'];
                $order->amount = $requestData['game_leveling_amount'];
                $order->save();

                OrderDetail::where('order_no', $orderNo)->where('field_name', 'game_leveling_amount')->update([
                    'field_value' => $requestData['game_leveling_amount']
                ]);
            }
            // 修改密码
            if ($requestData['password'] != $orderDetail['password']) {
                // 更新值
                OrderDetail::where('order_no', $orderNo)->where('field_name', 'password')->update([
                    'field_value' =>$requestData['password']
                ]);
            }
            // 修改 游戏代练小时
            if ($requestData['game_leveling_hour'] != $orderDetail['game_leveling_hour'] && $requestData['game_leveling_hour'] > $orderDetail['game_leveling_hour']) {
                // 更新值
                OrderDetail::where('order_no', $orderNo)->where('field_name', 'game_leveling_hour')->update([
                    'field_value' =>$requestData['game_leveling_hour']
                ]);
            }
            // 修改 游戏代练天
            if ($requestData['game_leveling_day'] != $orderDetail['game_leveling_day'] && $requestData['game_leveling_day'] > $orderDetail['game_leveling_day']) {
                // 更新值
                OrderDetail::where('order_no', $orderNo)->where('field_name', 'game_leveling_day')->update([
                    'field_value' => $requestData['game_leveling_day']
                ]);
            }

        }
        // 待验收 可加价格
        if ($order->status == 14) {
            if ($order->price < $requestData['game_leveling_amount']) {
                $amount =  $requestData['game_leveling_amount'] - $order->price;
                Asset::handle(new Expend($amount, Expend::TRADE_SUBTYPE_ORDER_GAME_LEVELING_ADD, $orderNo, '代练改价支出', $order->creator_primary_user_id));

                $order->price = $requestData['game_leveling_amount'];
                $order->amount = $requestData['game_leveling_amount'];
                $order->save();

                OrderDetail::where('order_no', $orderNo)->where('field_name', 'game_leveling_amount')->update([
                    'field_value' => $requestData['game_leveling_amount']
                ]);
            }
        }
        // 状态锁定 可改密码
        if ($order->status == 18) {
            // 修改密码
            if ($requestData['password'] != $orderDetail['password']) {
                // 更新值
                OrderDetail::where('order_no', $orderNo)->where('field_name', 'password')->update([
                    'field_value' =>$requestData['password']
                ]);
            }
        }
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
            LevelingConsult::UpdateOrcreate(['order_no' => $data['order_no']], $data);
            // 改状态
            DailianFactory::choose('revoke')->run($data['order_no'], $data['user_id']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->ajax(0, '操作失败!');
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

            LevelingConsult::where('order_no', $data['order_no'])->update($data);
            // 改状态
            DailianFactory::choose('applyArbitration')->run($data['order_no'], $userId);
        } catch (Exception $e) {  
            DB::rollBack();      
            return response()->ajax(0, '操作失败!');
        }
        DB::commit();
        return response()->ajax(1, '操作成功!');
    }

    public function export($orders)
    {
        try {
            // 标题
            $title = [
                '序号',
                '订单号',
                '订单来源',
                '标签',
                '客服备注',
                '代练标题',
                '游戏区服',
                '代练类型',
                '账号密码',
                '角色名称',
                '订单状态',
                '来源价格',
                '发单价',
                '创建时间',
                '更新时间',
            ];
            // 数组分割,反转
            $chunkOrders = array_chunk(array_reverse($orders->toArray()), 1000);
            Excel::create(iconv('UTF-8', 'gbk', '代练订单'), function ($excel) use ($chunkOrders, $title) {

                foreach ($chunkOrders as $chunkOrder) {
                    // 内容
                    $datas = [];
                    foreach ($chunkOrder as $key => $order) {
                        $datas[] = [
                            $order['id'],
                            $order['no'],
                            $order['source'] ? config('order.source')[$order['source']] : '--',
                            OrderDetail::where('order_no', $order['no'])->where('field_name', 'label')->value('field_value') ?? '--',
                            OrderDetail::where('order_no', $order['no'])->where('field_name', 'cstomer_service_remark')->value('field_value'),
                            OrderDetail::where('order_no', $order['no'])->where('field_name', 'game_leveling_title')->value('field_value'),
                            OrderDetail::where('order_no', $order['no'])->where('field_name', 'version')->value('field_value') . '/' . OrderDetail::where('order_no', $order['no'])->where('field_name', 'serve')->value('field_value'),
                            OrderDetail::where('order_no', $order['no'])->where('field_name', 'game_leveling_type')->value('field_value'),
                            OrderDetail::where('order_no', $order['no'])->where('field_name', 'account')->value('field_value') . '/' . OrderDetail::where('order_no', $order['no'])->where('field_name', 'password')->value('field_value'),
                            OrderDetail::where('order_no', $order['no'])->where('field_name', 'role')->value('field_value'),
                            config('order.status_leveling')[$order['status']] ?? '--',
                            OrderDetail::where('order_no', $order['no'])->where('field_name', 'source_price')->value('field_value'),
                            $order['amount'],
                            $order['created_at'] ?? '--',
                            $order['updated_at'] ?? '--',
                        ];
                    }
                    // 将标题加入到数组
                    array_unshift($datas, $title);
                    // 每页多少数据
                    $excel->sheet("页数", function ($sheet) use ($datas) {
                        $sheet->rows($datas);
                    });
                }
            })->export('xls');

        } catch (Exception $e) {

        }
    }

    public function excel(Request $request, OrderRepository $orderRepository)
    {
        try {
            $no = $request->no ?? 0;
            $foreignOrderNo = $request->foreignOrderNo ?? 0;
            $gameId = $request->gameId ?? 0;
            $wangWang = $request->wangWang ?? 0;
            $startDate = $request->startDate ?? 0;
            $endDate = $request->endDate ?? 0;
            $status = $request->status ?? 0;
            $urgentOrder = $request->urgentOrder ?? 0;

            $dailianOrders = $orderRepository->filterOrders($status, $no, $foreignOrderNo, $gameId, $wangWang, $urgentOrder, $startDate, $endDate);

            if ($dailianOrders) {
                $this->export($dailianOrders);
            }
            return redirect(route('frontend.workbench.leveling.index'))->with(['message' => '无导出数据']);
        } catch (Exception $e) {
            
        }
    }

    /**
     * 待发单
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function wait()
    {
        return view('frontend.workbench.leveling');
    }
}


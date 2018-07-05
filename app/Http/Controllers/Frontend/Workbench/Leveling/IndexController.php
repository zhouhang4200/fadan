<?php

namespace App\Http\Controllers\Frontend\Workbench\Leveling;

use App\Extensions\Dailian\Controllers\Arbitrationing;
use App\Extensions\Dailian\Controllers\Complete;
use App\Extensions\Dailian\Controllers\Revoking;
use App\Models\AutomaticallyGrabGoods;
use App\Models\BusinessmanContactTemplate;
use App\Models\GameLevelingRequirementsTemplate;
use App\Models\GoodsTemplateWidget;
use App\Models\OrderAttachment;
use App\Models\OrderSendResult;
use App\Models\UserSetting;
use App\Exceptions\AssetException;
use App\Extensions\Asset\Expend;
use App\Extensions\Asset\Income;
use App\Extensions\Order\Operations\CreateLeveling;
use App\Models\Game;
use App\Models\GoodsTemplateWidgetValue;
use App\Models\OrderDetail;
use App\Models\Order as OrderModel;
use App\Models\OrderHistory;
use App\Models\SmsTemplate;
use App\Models\TaobaoTrade;
use App\Models\User;
use App\Repositories\Api\TaobaoTradeRepository;
use App\Repositories\Frontend\GoodsTemplateWidgetRepository;
use App\Repositories\Frontend\OrderDetailRepository;
use App\Repositories\Frontend\OrderRepository;
use App\Repositories\Frontend\GoodsTemplateWidgetValueRepository;
use App\Repositories\Frontend\OrderHistoryRepository;
use App\Services\DailianMama;
use App\Services\Leveling\DD373Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Events\OrderBasicData;
use App\Models\GoodsTemplate;

use DB, Order, Exception, Asset, Redis;
use App\Repositories\Frontend\GameRepository;
use App\Exceptions\CustomException;
use App\Extensions\Dailian\Controllers\DailianFactory;
use App\Models\LevelingConsult;
use App\Services\Show91;
use Excel;
use App\Exceptions\DailianException;
use App\Repositories\Frontend\OrderAttachmentRepository;
use App\Events\AutoRequestInterface;
use TopClient;
use TradeFullinfoGetRequest;
use App\Models\OrderAutoMarkup;

/**
 * 代练订单
 * Class OrderController
 * @package App\Http\Controllers\Frontend\Workbench
 */
class IndexController extends Controller
{
    /**
     * @var mixed
     */
    protected $game;

    /**
     * IndexController constructor.
     * @param GameRepository $gameRepository
     */
    public function __construct(GameRepository $gameRepository)
    {
        parent::__construct();
        $this->game = $gameRepository->availableByServiceId(4);
    }

    /**
     * @param Request $request
     * @param OrderRepository $orderRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function oldIndex(Request $request, OrderRepository $orderRepository)
    {
        $game = $this->game;
        $employee = User::where('parent_id', Auth::user()->getPrimaryUserId())->get();
        $tags = GoodsTemplateWidgetValueRepository::getTags(Auth::user()->getPrimaryUserId());
        $smsTemplate = SmsTemplate::where('user_id', Auth::user()->getPrimaryUserId())->where('type', 2)->get();

        return view('frontend.workbench.leveling.index', compact('game', 'employee', 'tags', 'smsTemplate'));
    }

    /**
     * 新订单列表
     * @param Request $request
     * @param OrderRepository $orderRepository
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request, OrderRepository $orderRepository)
    {
        $status = $request->input('status', 0);
        $no = $request->input('no', '');
        $taobaoStatus = $request->input('taobao_status', 0);
        $gameId = $request->input('game_id', 0);
        $wangWang = $request->input('wang_wang');
        $customerServiceName = $request->input('customer_service_name');
        $platform = $request->input('platform', 0);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $game = $this->game;
        $employee = User::where('parent_id', Auth::user()->getPrimaryUserId())->get();
        $tags = GoodsTemplateWidgetValueRepository::getTags(Auth::user()->getPrimaryUserId());

        // 导出订单
        if ($request->export) {
            $orderRepository->levelingExport(compact('serviceId', 'status', 'no', 'foreignOrderNo', 'gameId', 'wangWang', 'urgentOrder', 'startDate', 'endDate'));
        }
        // 获取订单
        $orders = $orderRepository->levelingDataList($status, $no,  $taobaoStatus,  $gameId, $wangWang, $customerServiceName, $platform, $startDate, $endDate);

        // 查询各状态订单数
        $statusCount = $orderRepository->levelingOrderCount($status, $no,  $taobaoStatus,  $gameId, $wangWang, $customerServiceName, $platform, $startDate, $endDate);

        $allStatusCount = OrderModel::where('creator_primary_user_id', auth()->user()->getPrimaryUserId())
            ->where('service_id', 4)->where('status', '!=', 24)->count();

        return view('frontend.workbench.leveling.index-new')->with([
            'orders' => $orders,
            'game' => $game,
            'employee' => $employee,
            'tags' => $tags,
            'no' => $no,
            'customerServiceName' => $customerServiceName,
            'gameId' => $gameId,
            'status' => $status,
            'taobaoStatus' => $taobaoStatus,
            'wangWang' => $wangWang,
            'platform' => $platform,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'statusCount' => $statusCount,
            'allStatusCount' => $allStatusCount,
            'fullUrl' => $request->fullUrl(),
        ]);
    }

    /**
     * ajax 获取订单数据
     * @param Request $request
     * @param OrderRepository $orderRepository
     * @return array|\Illuminate\Http\RedirectResponse
     */
    public function orderList(Request $request, OrderRepository $orderRepository)
    {
        $no = $request->input('no', 0);
        $customerServiceName = $request->input('customer_service_name', 0);
        $gameId = $request->input('game_id', 0);
        $status = $request->input('status', 0);
        $wangWang = $request->input('wang_wang');
        $urgentOrder = $request->input('urgent_order', 0);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $pageSize = $request->input('limit', 50);
        $taobaoStatus = $request->input('taobao_status', 0);
        $platform = $request->input('platform', 0);
        $levelingType = $request->input('game_leveling_type', 0);

        if ($request->export) {
            $orderRepository->levelingDataListExport($status, $no,  $taobaoStatus,  $gameId, $wangWang, $customerServiceName, $platform, $startDate, $endDate, $levelingType, $pageSize);
        }

        $orders = $orderRepository->levelingDataList($status, $no,  $taobaoStatus,  $gameId, $wangWang, $customerServiceName, $platform, $startDate, $endDate, $levelingType, $pageSize);

        if ($request->ajax()) {

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

                $orderCurrent['payment_amount'] = '';
                $orderCurrent['get_amount'] = '';
                $orderCurrent['poundage'] = '';
                $orderCurrent['profit'] = '';
                if (in_array($orderInfo['status'], [19, 20, 21])){
                    // 支付金额
                    $amount = 0;
                    if (in_array($orderInfo['status'], [21, 19])) {
                        $amount = $orderInfo['leveling_consult']['api_amount'];
                        $orderCurrent['get_amount'] = $orderInfo['leveling_consult']['api_deposit'];
                    } else {
                        $amount = $orderInfo['amount'];
                    }
                    // 支付金额
                    $orderCurrent['payment_amount'] = $amount !=0 ?  $amount + 0:  $amount;

                    $orderCurrent['payment_amount'] = (float)$orderCurrent['payment_amount'] + 0;
                    $orderCurrent['get_amount'] = (float)$orderCurrent['get_amount'] + 0;
                    $orderCurrent['poundage'] = (float)$orderCurrent['poundage'] + 0;
                    // 利润
                    $orderCurrent['profit'] = ($orderCurrent['get_amount']  - $orderCurrent['payment_amount']  - $orderCurrent['poundage']) + 0;
                }

                $days = $orderCurrent['game_leveling_day'] ?? 0;
                $hours = $orderCurrent['game_leveling_hour'] ?? 0;
                $orderCurrent['leveling_time'] = $days . '天' . $hours . '小时'; // 代练时间

                // 如果存在接单时间
                $orderCurrent['time_out'] = 0;
                if (isset($orderCurrent['receiving_time']) && !empty($orderCurrent['receiving_time'])) {
                    // 计算到期的时间戳
                    $expirationTimestamp = strtotime($orderCurrent['receiving_time']) + $days * 86400 + $hours * 3600;
                    // 计算剩余时间
                    $leftSecond = $expirationTimestamp - time();
                    $orderCurrent['left_time'] = sec2Time($leftSecond); // 剩余时间
                    if ($leftSecond < 0) {
                        $orderCurrent['timeout'] = 1;
                        $orderCurrent['timeout_time'] = sec2Time(abs($leftSecond)); // 超时时间
                    }
                } else {
                    $orderCurrent['left_time'] = '';
                }
                // 按状态显示不同时间文案
                $orderCurrent['status_time'] = '结束';
                if ($orderInfo['status'] == 1) {
                    $orderCurrent['status_time'] =  sec2Time(time() - strtotime($orderInfo['created_at']));
                }  elseif ($orderInfo['status'] == 13 && isset($orderCurrent['receiving_time'])) {
                    $orderCurrent['status_time'] =  sec2Time(time() - strtotime($orderCurrent['receiving_time']));
                }

                // 接单平台名字
                $orderCurrent['third_name'] = '';
                if (isset($orderCurrent['third']) && isset(config('partner.platform')[(int)$orderCurrent['third']])) {
                    $orderCurrent['third_name'] = config('partner.platform')[$orderCurrent['third']]['name'];
                }

                // 订单超过12小时
                $currentTime = new Carbon();
                $orderTime = $currentTime->parse($orderCurrent['created_at']);
                $orderCurrent['day'] = $orderTime->diffInDays($currentTime, false);
                if (isset($orderCurrent['password'])) {
                    $orderCurrent['password'] = str_replace(substr($orderCurrent['password'], -4, 4), '****', $orderCurrent['password']);
                } else {
                    $orderCurrent['password'] = '';
                }

                $orderCurrent['amount'] = $orderCurrent['amount'] + 0;


                // 如果是接单账号则隐藏:玩家旺旺、客服备注、来源价格、利润 等字段数据
                if (auth()->user()->getPrimaryUserId() == $orderCurrent['gainer_primary_user_id']) {
                    $orderCurrent['profit'] = '';
                    $orderCurrent['client_wang_wang'] = '';
                    $orderCurrent['seller_nick'] = '';
                    $orderCurrent['source_price'] = '';
                    $orderCurrent['customer_service_remark'] = '';
                }

                $temp = [];
                foreach ($orderCurrent as $key => $value) {
                    if (is_string($value)) {
                        $temp[$key] = htmlspecialchars($value);
                    } else {
                        $temp[$key] = $value;
                    }
                }
                $orderArr[] = $temp;

            }
      
            return [
                'code' => 0,
                'msg' => '',
                'count' => $orders->total(),
                'data' =>  $orderArr,
            ];
        }
    }

    /**
     * @param Request $request
     * @param GameRepository $gameRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request, GameRepository $gameRepository)
    {
        $game = $this->game;
        $tid = $request->tid;
        $gameId = $request->game_id ? $request->game_id : 1;
        $businessmanInfo = auth()->user()->getPrimaryInfo();

        // 拆分淘宝订单号
        $tidArr = explode(',', $tid);
        $filterTidArr = array_filter($tidArr);
        // 获取所有淘宝订单
        $allTaobaoTrade = TaobaoTrade::whereIn('tid', $filterTidArr)->orderBy('id')->get()->toArray();

        // 有淘宝订单则更新淘宝订单卖家备注
        $fixedInfo = [];
        $taobaoTrade = null;
        if ($allTaobaoTrade) {
            $taobaoTrade = TaobaoTrade::where('tid', $allTaobaoTrade[0]['tid'])->first();
        }

        if ($taobaoTrade) {
            if (empty($taobaoTrade->seller_memo)) {
                // 获取备注并更新
                $client = new TopClient;
                $client->format = 'json';
                $client->appkey = '12141884';
                $client->secretKey = 'fd6d9b9f6ff6f4050a2d4457d578fa09';

                $req = new TradeFullinfoGetRequest;
                $req->setFields("tid, type, status, payment, orders, seller_memo");
                $req->setTid($tid);
                $resp = $client->execute($req, taobaoAccessToken($taobaoTrade->seller_nick));

                if (!empty($resp->trade->seller_memo)) {
                    $taobaoTrade->seller_memo = $resp->trade->seller_memo;
                    $taobaoTrade->save();
                }
            }
            // 从收货地址中拆分区服角色信息
            $receiverAddress = explode("\r\n", trim($taobaoTrade->receiver_address));
            // 获取抓取商品配置
            $goodsConfig = AutomaticallyGrabGoods::where('foreign_goods_id', $taobaoTrade->num_iid)->first();

            // 如果游戏为DNF并且是推荐号则生成固定填入的订单数据
            if ($goodsConfig && $goodsConfig->game_id == 86 && $goodsConfig->type == 1) { //  && $goodsConfig->type == 1
                $fixedInfo = $this->dnfFixedInfo($receiverAddress, $taobaoTrade);
            }
        }

        return view('frontend.v1.workbench.leveling.create', compact(
            'game', 'tid', 'gameId', 'taobaoTrade', 'businessmanInfo', 'receiverAddress', 'fixedInfo', 'allTaobaoTrade'));
    }

    /**
     * 下单
     * @param Request $request
     */
    public function order(Request $request)
    {
        try {
            $orderData = $request->data; // 表单所有数据
            $gameId = $orderData['game_id']; // 游戏ID
            $templateId = GoodsTemplate::where('game_id', $gameId)->where('service_id', 4)->value('id'); // 模版ID
            $originalPrice = $orderData['source_price']; // 原价
            $price = $orderData['game_leveling_amount']; // 代练价格
            $foreignOrderNO = isset($orderData['source_order_no']) ? $orderData['source_order_no'] : ''; // 来源订单号
            $orderData['urgent_order'] = isset($orderData['urgent_order']) ? 1 : 0; // 是否加急

            $userId = Auth::user()->id; // 下单用户ID
            $orderData['customer_service_name'] = Auth::user()->username; // 下单人名字
            $orderData['order_source'] = '天猫'; // 订单来源

            if (isset($request->value) && ! empty($request->value)) {
                // 原始发单人
                if (isset($orderData['creator_user_id'])) {
                    $originalUser = User::where('id', $orderData['creator_user_id'])->first();
                    $orderData['customer_service_name'] = $originalUser->username;
                    $userId = $originalUser->id;
                }
            }

            try {
                $order = Order::handle(new CreateLeveling($gameId, $templateId, $userId, $foreignOrderNO, $price, $originalPrice, $orderData));

                // 提示哪些平台下单成功，哪些平台下单失败
                $orderDetails = OrderDetail::where('order_no', $order->no)
                    ->pluck('field_value', 'field_name')
                    ->toArray();

                // 下单成功之后，查看此订单是否设置了每小时自动加价
                $this->checkIfAutoMarkup($order, $orderDetails);

                return response()->ajax(1, '下单成功！');
            } catch (CustomException $exception) {
                return response()->ajax(0, $exception->getMessage());
            } catch (DailianException $dailianException) {
                return response()->ajax(0, $dailianException->getMessage());
            } catch (AssetException $assetException) {
                return response()->ajax(0, $assetException->getMessage());
            }
        } catch (CustomException $customException) {
            return response()->ajax(0, '下单失败请联系平台工作人员');
        }
    }

    /**
     * 获取游戏 区\代练类型
     * @param Request $request
     */
    public function getRegionType(Request $request)
    {
        // 获取模版ID
        $templateId = GoodsTemplate::where('game_id', $request->game_id)->where('service_id', 4)->value('id');
        // 获取代练区 获取代练类型ID
        $regionTypeId = GoodsTemplateWidget::where('goods_template_id', $templateId)
            ->whereIn('field_name', ['region', 'game_leveling_type'])
            ->pluck('id');

        // 获取代练区 获取代练类型选项值
        $regionType = GoodsTemplateWidgetValue::select(['field_name', 'field_value', 'id'])->whereIn('goods_template_widget_id', $regionTypeId)
            ->get()->toArray();

        return response()->ajax(1, 'success', $regionType);
    }

    /**
     * 获取游戏模版
     * @param Request $request
     * @param GoodsTemplateWidgetRepository $goodsTemplateWidgetRepository
     * @return mixed
     */
    public function getTemplate(Request $request, GoodsTemplateWidgetRepository $goodsTemplateWidgetRepository)
    {
        $businessmanInfo = auth()->user()->getPrimaryInfo();

        // 获取对应的模版ID
        $templateId = GoodsTemplate::getTemplateId(4, $request->game_id);
        // 获取对应的模版组件
        $template = $goodsTemplateWidgetRepository->getWidgetBy($templateId);
        // 获取订单备注
        $sellerMemo = TaobaoTrade::where('tid', $request->tid)->value('seller_memo');

        // 如果有订单号则获取订单原来设置的值
        $orderTemplateValue = [];
        if ($request->no) {
            $orderTemplateValue = OrderDetailRepository::getByOrderNo($request->no);
        }
        return response()->ajax(1, 'success', [
            'id' => $templateId,
            'value' => $orderTemplateValue,
            'sellerMemo' => $sellerMemo,
            'businessmanInfoMemo' => $businessmanInfo,
            'template' => $template->toArray(),
        ]);
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
     * 获取游戏代练模版
     * @param Request $request
     */
    public function getGameLevelingTemplate(Request $request)
    {
        return GameLevelingRequirementsTemplate::where('user_id', auth()->user()->getPrimaryUserId())
            ->whereIn('game_id', [0 , $request->game_id])
            ->orderBy('game_id')
            ->get();
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
        // 获取商户的联系方式模版信息
        // 获取可用游戏
        $game = $this->game;
        // 获取订单数据
        $detail = $orderRepository->levelingDetail($request->no);
        // 查看是否有打手QQ和手机
        $orderDetails = OrderDetail::where('order_no', $detail['no'])
            ->pluck('field_value', 'field_name')
            ->toArray();
        $contact = BusinessmanContactTemplate::where('user_id', auth()->user()->getPrimaryUserId())->get();
        // 获取淘宝订单数据
        $taobaoTrade = TaobaoTrade::where('tid', $orderDetails['source_order_no'])->first();

        // 有淘宝订单则更新淘宝订单卖家备注
        $fixedInfo = [];
        if ($taobaoTrade) {
            // 从收货地址中拆分区服角色信息
            $receiverAddress = explode("\r\n", trim($taobaoTrade->receiver_address));
            // 获取抓取商品配置
            $goodsConfig = AutomaticallyGrabGoods::where('foreign_goods_id', $taobaoTrade->num_iid)->first();

            // 如果游戏为DNF并且是推荐号则生成固定填入的订单数据
            if ($goodsConfig && $goodsConfig->game_id == 86 && $goodsConfig->type == 1) { //  && $goodsConfig->type == 1
                $fixedInfo = $this->dnfFixedInfo($receiverAddress, $taobaoTrade);
            }
        }

        // 写订单日志
        OrderHistory::create([
            'order_no' => $detail['no'],
            'user_id' => auth()->user()->id,
            'creator_primary_user_id' => auth()->user()->getPrimaryUserId(),
            'admin_user_id' => 0,
            'type' => 32,
            'name' => config('order.operation_type')[32],
            'description' => auth()->user()->nickname . ' 查看订单',
            'before' => serialize([]),
            'after' => serialize([]),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // 获取订单对应模版ID
        $templateId = GoodsTemplate::getTemplateId(4, $detail['game_id']);
        // 获取对应的模版组件
        $template = $goodsTemplateWidgetRepository->getWidgetBy($templateId);
        // 短信模版
        $smsTemplate = SmsTemplate::where('user_id', Auth::user()->getPrimaryUserId())->where('type', 2)->get();

        $detail['master'] = $detail['creator_primary_user_id'] == Auth::user()->getPrimaryUserId() ? 1 : 0;
        $detail['consult'] = $detail['leveling_consult']['consult'] ?? '';
        $detail['complain'] = $detail['leveling_consult']['complain'] ?? '';

        // 撤销说明
        if(isset($detail['leveling_consult']['consult']) && $detail['leveling_consult']['consult'] != 0 && $detail['leveling_consult']['user_id'] != 0) {
            if ($detail['leveling_consult']['complete'] != 2) {
                //发起人的发单主ID 与 当前主ID一样则撤销发起人为发单人
                if ($detail['creator_primary_user_id'] == Auth::user()->getPrimaryUserId() && $detail['leveling_consult']['consult'] == 1) {
                    $text = '你发起协商撤销。';
                    $text .= '你支付代练费' . ($detail['leveling_consult']['amount'] + 0) . '元，';
                    $text .= '对方支付保证金' . ($detail['leveling_consult']['deposit'] + 0). '元。原因：' . $detail['leveling_consult']['revoke_message'];

                    $detail['payment_amount'] = $detail['leveling_consult']['amount'] + 0;
                    $detail['get_amount'] = $detail['leveling_consult']['deposit'] + 0;
                } elseif ($detail['creator_primary_user_id'] != Auth::user()->getPrimaryUserId() && $detail['leveling_consult']['consult'] == 1) {
                    $text = '对方发起协商撤销。';
                    $text .= '对方支付代练费' . ($detail['leveling_consult']['api_amount'] + 0) . '元，';
                    $text .= '你支付保证金' . ($detail['leveling_consult']['api_deposit'] + 0) . '元。原因：' . $detail['leveling_consult']['revoke_message'];

                    $detail['payment_amount'] = $detail['leveling_consult']['api_amount'] + 0;
                    $detail['get_amount'] = $detail['leveling_consult']['api_deposit'] + 0;
                }

                if ($detail['gainer_primary_user_id'] == Auth::user()->getPrimaryUserId() && $detail['leveling_consult']['consult'] == 2) {
                    $text = '你发起协商撤销。';
                    $text .= '对方支付代练费' . ($detail['leveling_consult']['amount'] + 0) . '元，';
                    $text .= '你方支付保证金' . ($detail['leveling_consult']['deposit'] + 0). '元。原因：' . $detail['leveling_consult']['revoke_message'];

                    $detail['payment_amount'] = $detail['leveling_consult']['amount'] + 0;
                    $detail['get_amount'] = $detail['leveling_consult']['deposit'] + 0;
                } elseif ($detail['gainer_primary_user_id'] != Auth::user()->getPrimaryUserId() && $detail['leveling_consult']['consult'] == 2) {
                    $text = '对方发起协商撤销。';
                    $text .= '你支付代练费' . ($detail['leveling_consult']['api_amount'] + 0) . '元，';
                    $text .= '对方支付保证金' . ($detail['leveling_consult']['api_deposit'] + 0) . '元。原因：' . $detail['leveling_consult']['revoke_message'];

                    $detail['payment_amount'] = $detail['leveling_consult']['api_amount'] + 0;
                    $detail['get_amount'] = $detail['leveling_consult']['api_deposit'] + 0;
                }

                $detail['consult_desc'] = $text;
            }
        }
        // 仲裁说明
        if(isset($detail['leveling_consult']['complain']) && $detail['leveling_consult']['complain'] != 0) {
            if ($detail['leveling_consult']['complete'] != 1) {
                //发起人的主ID 与 当前主ID一样则仲裁发起人
                if ($detail['creator_primary_user_id'] == Auth::user()->getPrimaryUserId() && $detail['leveling_consult']['complain'] == 1) {
                    $text = '你发起申请仲裁。原因：' . $detail['leveling_consult']['complain_message'];
                } elseif ($detail['creator_primary_user_id'] != Auth::user()->getPrimaryUserId() && $detail['leveling_consult']['complain'] == 1) {
                    $text = '对方发起申请仲裁。 原因：' . $detail['leveling_consult']['complain_message'];
                }

                if ($detail['gainer_primary_user_id'] == Auth::user()->getPrimaryUserId() && $detail['leveling_consult']['complain'] == 2) {
                    $text = '你发起申请仲裁。原因：' . $detail['leveling_consult']['complain_message'];
                } elseif ($detail['gainer_primary_user_id'] != Auth::user()->getPrimaryUserId() && $detail['leveling_consult']['complain'] == 2) {
                    $text = '对方发起申请仲裁。 原因：' . $detail['leveling_consult']['complain_message'];
                }

                $detail['complain_desc'] = $text;
            }
        }
        // 仲裁结果
        if(isset($detail['leveling_consult']['complete']) && $detail['leveling_consult']['complete'] == 2) {
            $text = '。客服进行了仲裁。';

            if ($detail['creator_primary_user_id'] == Auth::user()->getPrimaryUserId() && $detail['leveling_consult']['complain'] == 1) {
                $text .= '你支付代练费' .  ($detail['leveling_consult']['api_amount'] + 0) . '元，';
                $text .= '对方支付保证金' . ($detail['leveling_consult']['api_deposit'] + 0) . '元';
            } elseif ($detail['creator_primary_user_id'] != Auth::user()->getPrimaryUserId() && $detail['leveling_consult']['complain'] == 1) {
                $text .= '对方支付代练费' .  ($detail['leveling_consult']['api_amount'] + 0) . '元，';
                $text .= '你支付保证金' . ($detail['leveling_consult']['api_deposit'] + 0) . '元';
            }

            if ($detail['gainer_primary_user_id'] == Auth::user()->getPrimaryUserId() && $detail['leveling_consult']['complain'] == 2) {
                $text .= '对方支付代练费' .  ($detail['leveling_consult']['api_amount'] + 0) . '元，';
                $text .= '你支付保证金' . ($detail['leveling_consult']['api_deposit'] + 0) . '元';
            } elseif ($detail['gainer_primary_user_id'] != Auth::user()->getPrimaryUserId() && $detail['leveling_consult']['complain'] == 2) {
                $text .= '你支付代练费' .  ($detail['leveling_consult']['api_amount'] + 0) . '元，';
                $text .= '对方支付保证金' . ($detail['leveling_consult']['api_deposit'] + 0) . '元';
            }

            $detail['payment_amount'] = $detail['leveling_consult']['api_amount'] + 0;
            $detail['get_amount'] = $detail['leveling_consult']['api_deposit'] + 0;
            $detail['complain_desc'] .= $text;
        }

        if (!in_array($detail['status'], [19, 20, 21])){
            $detail['payment_amount'] = '';
            $detail['get_amount'] = '';
            $detail['poundage'] = '';
            $detail['profit'] = '';
        } else {
            // 支付金额
            $amount = 0;
            if (!in_array($detail['status'], [21, 19])) {
                $detail['payment_amount'] = $detail['amount'];
            }
            // 支付金额
            $detail['get_amount'] = (float)$detail['get_amount'] + 0;
            $detail['poundage'] = (float)$detail['poundage'] + 0;
            // 利润
            $detail['profit'] = ($detail['get_amount']  - $detail['payment_amount']  - $detail['poundage']) + 0;
        }

        $days = $detail['game_leveling_day'] ?? 0;
        $hours = $detail['game_leveling_hour'] ?? 0;
        $detail['leveling_time'] = $days . '天' . $hours . '小时'; // 代练时间

        // 如果存在接单时间
        if (isset($detail['receiving_time']) && !empty($detail['receiving_time'])) {
            // 计算到期的时间戳
            $expirationTimestamp = strtotime($detail['receiving_time']) + $days * 86400 + $hours * 3600;
            // 计算剩余时间
            $leftSecond = $expirationTimestamp - time();
            $detail['left_time'] = sec2Time($leftSecond); // 剩余时间
        } else {
            $detail['left_time'] = '';
        }

        // 如果是接单账号则隐藏:玩家旺旺、客服备注、来源价格、利润 等字段数据
        if (auth()->user()->getPrimaryUserId() == $detail['gainer_primary_user_id']) {
            $detail['profit'] = '';
            $detail['client_wang_wang'] = '';
            $detail['seller_nick'] = '';
            $detail['source_price'] = '';
            $detail['customer_service_remark'] = '';
        }
        // 如果是未接单状态则查询订单发送情况
        $sendResult = '';
        if ($detail['status'] == 1) {
            $sendResult = OrderSendResult::where('order_no', $detail['no'])->get();
        }

        return view('frontend.v1.workbench.leveling.detail', compact('detail', 'template', 'game', 'smsTemplate', 'taobaoTrade', 'contact', 'fixedInfo', 'sendResult'));
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
        // 获取商户的联系方式模版信息
        $contact = BusinessmanContactTemplate::where('user_id', auth()->user()->getPrimaryUserId())->get();

        $taobaoTrade = TaobaoTrade::where('tid', $detail['source_order_no'])->first();

        // 写入订单关联数据
        $detail['master'] = $detail['creator_primary_user_id'] == Auth::user()->getPrimaryUserId() ? 1 : 0;
        $detail['consult'] = $detail['leveling_consult']['consult'] ?? '';
        $detail['complain'] = $detail['leveling_consult']['complain'] ?? '';

        return view('frontend.v1.workbench.leveling.repeat', compact('detail', 'template', 'game', 'contact', 'taobaoTrade'));
    }

    /**
     * 给订单增加备注
     * @param Request $request
     */
    public function remark(Request $request)
    {
        OrderDetail::where('order_no', $request->no)
            ->where('creator_primary_user_id', auth()->user()->getPrimaryUserId())
            ->where('field_name', 'customer_service_remark')
            ->update([
                'field_value' => $request->value,
            ]);
    }

    /**
     * 操作记录
     * @param $order_no
     * @return mixed
     */
    public function history($order_no)
    {
        $dataList = OrderHistoryRepository::dataList($order_no);
        return  view('frontend.workbench.leveling.history', compact('dataList'));
    }

    /**
     * 从接单平台接口拿留言数据
     * @param $orderNo
     * @param Request $request
     * @return mixed
     */
    public function leaveMessage($orderNo, Request $request)
    {
        $bingId = $request->input('bing_id', 0);
        // 取订单信息
        $order = (new OrderRepository)->detail($orderNo);
        if (empty($order)) {
            return response()->ajax(0, '订单不存在');
        }

        // 取订单详情
        $orderDetail = $order->detail->pluck('field_value', 'field_name');

        $messageArr = [];
        $message = [];
        try {
            if ($orderDetail['third'] == 1) {
                $messageArr = Show91::messageList(['oid' => $orderDetail['show91_order_no']]);
            } elseif ($orderDetail['third'] == 2) {
                // 代练妈妈 获取留言传入千手订单号
                $messageArr = DailianMama::chatOldList($orderDetail['dailianmama_order_no'], $bingId);
            }
             // 其他通用平台
            if (config('leveling.third_orders')) {
                // 获取订单和订单详情以及仲裁协商信息
                $orderDatas = $this->getOrderAndOrderDetailAndLevelingConsult($orderNo);
               // 遍历代练平台
                foreach (config('leveling.third_orders') as $third => $thirdOrderNoName) {
                    // 如果订单详情里面存在某个代练平台的订单号，撤单此平台订单
                    if ($third == $orderDatas['third'] && isset($orderDatas['third_order_no']) && ! empty($orderDatas['third_order_no'])) {
                        // 控制器-》方法-》参数
                        $message = call_user_func_array([config('leveling.controller')[$third], config('leveling.action')['getMessage']], [$orderDatas]);
                    }
                }
            }
        } catch (CustomException $e) {
            return response()->ajax(0, $e->getMessage());
        }

        $show91Uid = config('show91.uid');
        $dailianUid = config('dailianmama.uid');

//        for ($i =0; $i <=20; $i++) {
//            $message[] = [
//                'sender' => $i% 2 > 0 ? '您': '打手',
//                'send_content' => str_random(20),
//                'send_time' => date('Y-m-d H:i:s'),
//            ];
//        }

        return response()->ajax(1, 'success', [
            'third' => $orderDetail['third'],
            'show91Uid' => $show91Uid,
            'dailianMamaUid' => $dailianUid,
            'messageArr' => $messageArr,
            'message' => $message
        ]);
    }

    /**
     * 从show91接口拿截图数据
     * @param $order_no
     * @return mixed
     */
    public function leaveImage(Request $request)
    {
        $dataList = [];
        try {
             // 查看是否有图片
            $existImage = OrderAttachment::where('order_no', $request->order_no)->get()->toArray();

            if (!count($existImage)) {

                // 其他通用平台
                if (config('leveling.third_orders')) {
                    // 获取订单和订单详情以及仲裁协商信息
                    $orderDatas = $this->getOrderAndOrderDetailAndLevelingConsult($request->order_no);
                    // 遍历代练平台
                    foreach (config('leveling.third_orders') as $third => $thirdOrderNoName) {
                        // 如果订单详情里面存在某个代练平台的订单号，撤单此平台订单
                        if ($third == $orderDatas['third'] && isset($orderDatas['third_order_no']) && ! empty($orderDatas['third_order_no'])) {
                            // 控制器-》方法-》参数
                            $dataList =  call_user_func_array([config('leveling.controller')[$third], config('leveling.action')['getScreenshot']], [$orderDatas]);
                        }
                    }
                }
            } else {
                foreach ($existImage as $item) {
                    $dataList[] = [
                        'url' => asset($item['url']),
                        'username' => '',
                        'created_at' => $item['created_at'],
                        'description' => $item['description'],
                    ];
                }
            }
        } catch (Exception $e) {
            return response()->ajax(1, $e->getMessage());
        }
        return response()->ajax(1, 'success', $dataList);
    }

    /**
     * 上传截图后，推送到show91
     * @param Request $request
     * @return mixed
     * @internal param $order_nor
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
            OrderAttachmentRepository::saveImageAndUploadToThirdParty($orderNo, $diskName, $fileName, $description);
        } catch (CustomException $e) {
            return response()->ajax(0, $e->getMessage());
        } catch (DailianException $e) {
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
        $orderNo = $request->input('order_no');
        $message = $request->input('message');

        if (empty($orderNo)) {
            return response()->ajax(0, '单号不正确');
        }

        // 取订单信息
        $order = (new OrderRepository)->detail($orderNo);
        if (empty($order)) {
            return response()->ajax(0, '订单不存在');
        }
        // 取订单详情
        $orderDetail = $order->detail->pluck('field_value', 'field_name');
        // 第三方单号
        $thirdOrderNo = $orderDetail['third'] == 1 ? $orderDetail['show91_order_no'] : $orderDetail['dailianmama_order_no'];

        try {
            // if ($orderDetail['third'] == 1) {
            //     $res = Show91::addMess(['oid' => $thirdOrderNo, 'mess' => $message]);
            // } else if ($orderDetail['third'] == 2) {
            //     $res = DailianMama::addChat($thirdOrderNo, $message);
            // }
            // 其他平台
            if (config('leveling.third_orders')) {
                // 获取订单和订单详情以及仲裁协商信息
                $orderDatas = $this->getOrderAndOrderDetailAndLevelingConsult($orderNo);
                $orderDatas['message'] = $message;
                // 遍历代练平台
                foreach (config('leveling.third_orders') as $third => $thirdOrderNoName) {
                    // 如果订单详情里面存在某个代练平台的订单号
                    if ($third == $orderDatas['third'] && isset($orderDatas['third_order_no']) && ! empty($orderDatas['third_order_no'])) {
                        // 控制器-》方法-》参数
                        call_user_func_array([config('leveling.controller')[$third], config('leveling.action')['replyMessage']], [$orderDatas]);
                    }
                }
            }

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

        if (isset($requestData['gainer_primary_user_id'])) {
            unset($requestData['gainer_primary_user_id']);
        }

        $history = [];
        DB::beginTransaction();
        try {
            $order = OrderModel::where('no', $orderNo)->lockForUpdate()->first();
            $orderDetail = OrderDetail::where('order_no', $orderNo)->pluck('field_value', 'field_name');
            $orderDetailDisplayName = OrderDetail::where('order_no', $orderNo)->pluck('field_display_name', 'field_name');

            // 如果本次修改提交，游戏ID与原订单不同则删除原有订单详情，写入新的值
            if ($requestData['game_id'] != $order->game_id && $order->status == 1) {
                // 删除原订单详情
                OrderDetail::where('order_no', $orderNo)->delete();
                // 找到对应的游戏ID模版ID
                $templateId = GoodsTemplate::where('game_id', $requestData['game_id'])->where('service_id', 4)->value('id');
                // 按模填入订单详情数据
                // 保留91订单号
                $requestData['show91_order_no'] = $orderDetail['show91_order_no'];
                OrderDetailRepository::create($templateId, $orderNo, $requestData);
                // 本次修改与原单价不同则对进对应的资金操作
                if ($order->price != $requestData['game_leveling_amount']) {
                    // 加价
                    if ($order->price < $requestData['game_leveling_amount']) {
                        $amount = bcsub($requestData['game_leveling_amount'], $order->price);

                        if (abs($order->price) == $amount) {
                            throw new CustomException('金额不合法');
                        }

                        if(checkPayment($order->no)) {
                            Asset::handle(new Expend($amount, 7, $orderNo, '代练改价支出', $order->creator_primary_user_id));
                        }

                        $order->price = $requestData['game_leveling_amount'];
                        $order->amount = $requestData['game_leveling_amount'];
                        $order->save();

                        OrderDetail::where('order_no', $orderNo)->where('field_name', 'game_leveling_amount')->update([
                            'field_value' => $requestData['game_leveling_amount']
                        ]);
                    } else { // 减价
                        $amount = bcsub($order->price, $requestData['game_leveling_amount']);
                        if (abs($order->price) == $amount) {
                            throw new CustomException('金额不合法');
                        }

                        if(checkPayment($order->no)) {
                            Asset::handle(new Income($amount, 14, $orderNo, '代练改价退款', $order->creator_primary_user_id));
                        }

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

                // 手动触发调用外部接口时间
                $newOrder = OrderModel::where('no', $order->no)->first();

                /** 修改订单, 其他平台通用 **/
                if (config('leveling.third_orders')) {
                    // 获取订单和订单详情以及仲裁协商信息
                    $orderDatas = $this->getOrderAndOrderDetailAndLevelingConsult($order->no);
                    // 遍历代练平台
                    foreach (config('leveling.third_orders') as $third => $thirdOrderNoName) {
                        // 如果订单详情里面存在某个代练平台的订单号，撤单此平台订单
                        if (isset($orderDatas[$thirdOrderNoName]) && ! empty($orderDatas[$thirdOrderNoName])) {
                            // 控制器-》方法-》参数
                            call_user_func_array([config('leveling.controller')[$third], config('leveling.action')['updateOrder']], [$orderDatas]);
                        }
                    }
                }
            } else {
                // 下架 没有接单 更新所有信息
                if (in_array($order->status, [1, 23])) {
                    $changeValue = '';
                    // 加价 修改主单信息
                    if ($order->price != $requestData['game_leveling_amount']) {
                        // 加价
                        if ($order->price < $requestData['game_leveling_amount']) {
                            $amount = bcsub($requestData['game_leveling_amount'], $order->price, 2);

                            if(checkPayment($order->no)) {
                                Asset::handle(new Expend($amount, 7, $orderNo, '代练改价支出', $order->creator_primary_user_id));
                            }
                            $order->price = $requestData['game_leveling_amount'];
                            $order->amount = $requestData['game_leveling_amount'];
                            $order->save();

                            OrderDetail::where('order_no', $orderNo)->where('field_name', 'game_leveling_amount')->update([
                                'field_value' => $requestData['game_leveling_amount']
                            ]);
                        } else { // 减价
                            $amount = bcsub($order->price, $requestData['game_leveling_amount'], 2);
                            if ($amount < 0 || $requestData['game_leveling_amount'] == 0) {
                                throw new CustomException('金额不合法');
                            }

                            if(checkPayment($order->no)) {
                                Asset::handle(new Income($amount, 14, $orderNo, '代练改价退款', $order->creator_primary_user_id));
                            }

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
                            }
                        }
                    }
                    // 手动触发调用外部接口时间
                    $order = OrderModel::where('no', $order->no)->first();

                    /** 修改订单, 其他平台通用 **/
                    if (config('leveling.third_orders')) {
                        // 获取订单和订单详情以及仲裁协商信息
                        $orderDatas = $this->getOrderAndOrderDetailAndLevelingConsult($order->no);
                        // 遍历代练平台
                        foreach (config('leveling.third_orders') as $third => $thirdOrderNoName) {
                            // 如果订单详情里面存在某个代练平台的订单号，撤单此平台订单
                            if (isset($orderDatas[$thirdOrderNoName]) && ! empty($orderDatas[$thirdOrderNoName])) {
                                // 控制器-》方法-》参数
                                call_user_func_array([config('leveling.controller')[$third], config('leveling.action')['updateOrder']], [$orderDatas]);
                            }
                        }
                    }
                    /**修改订单**/
                }

                // 已接单  异常 更新部分信息 （加价 加时间天 加时间小时 修改密码 ）
                if (in_array($order->status, [13, 17])) {
                    // 加价 修改主单信息
                    if ($order->price < $requestData['game_leveling_amount']) {
                        $addAmount = bcsub($request->data['game_leveling_amount'], $order->amount, 2);
                        $amount = $requestData['game_leveling_amount'] - $order->price;

                        if(checkPayment($order->no)) {
                            Asset::handle(new Expend($amount, 7, $orderNo, '代练改价支出', $order->creator_primary_user_id));
                        }

                        $order->price = $requestData['game_leveling_amount'];
                        $order->amount = $requestData['game_leveling_amount'];
                        $order->save();

                        OrderDetail::where('order_no', $orderNo)->where('field_name', 'game_leveling_amount')->update([
                            'field_value' => $requestData['game_leveling_amount']
                        ]);
                        // 加价 其他平台通用
                        if (config('leveling.third_orders')) {
                            // 获取订单和订单详情以及仲裁协商信息
                            $orderDatas = $this->getOrderAndOrderDetailAndLevelingConsult($order->no);
                           // 遍历代练平台
                            foreach (config('leveling.third_orders') as $third => $thirdOrderNoName) {
                                // 如果订单详情里面存在某个代练平台的订单号，撤单此平台订单
                                if ($third == $orderDatas['third'] && isset($orderDatas['third_order_no']) && ! empty($orderDatas['third_order_no'])) {
                                    // 控制器-》方法-》参数
                                    call_user_func_array([config('leveling.controller')[$third], config('leveling.action')['addMoney']], [$orderDatas]);
                                }
                            }
                        }
                    } else if ($order->price > $requestData['game_leveling_amount']) {
                        return response()->ajax(0, '代练价格只可增加');
                    }
                    // 修改密码
                    if ($requestData['password'] != $orderDetail['password']) {
                        // 更新值
                        OrderDetail::where('order_no', $orderNo)->where('field_name', 'password')->update([
                            'field_value' => $requestData['password']
                        ]);
                        // 其他平台通用
                        if (config('leveling.third_orders')) {
                             // 获取订单和订单详情以及仲裁协商信息
                            $orderDatas = $this->getOrderAndOrderDetailAndLevelingConsult($order->no);
                            // 遍历代练平台
                                foreach (config('leveling.third_orders') as $third => $thirdOrderNoName) {
                                    // 如果订单详情里面存在某个代练平台的订单号，撤单此平台订单
                                    if ($third == $orderDatas['third'] && isset($orderDatas['third_order_no']) && ! empty($orderDatas['third_order_no'])) {
                                    // 控制器-》方法-》参数
                                    call_user_func_array([config('leveling.controller')[$third], config('leveling.action')['updateAccountAndPassword']], [$orderDatas]);
                                }
                            }
                        }
                    }

                    if ($requestData['account'] != $orderDetail['account']) {
                        OrderDetail::where('order_no', $orderNo)->where('field_name', 'account')->update([
                            'field_value' => $requestData['password']
                        ]);
                        // 账号密码修改，91和代练妈妈通用
                        // event(new AutoRequestInterface($order, 'editOrderAccPwd', false));
                        // 其他平台通用
                        if (config('leveling.third_orders')) {
                             // 获取订单和订单详情以及仲裁协商信息
                            $orderDatas = $this->getOrderAndOrderDetailAndLevelingConsult($order->no);
                            // 遍历代练平台
                                foreach (config('leveling.third_orders') as $third => $thirdOrderNoName) {
                                    // 如果订单详情里面存在某个代练平台的订单号，撤单此平台订单
                                    if ($third == $orderDatas['third'] && isset($orderDatas['third_order_no']) && ! empty($orderDatas['third_order_no'])) {
                                    // 控制器-》方法-》参数
                                    call_user_func_array([config('leveling.controller')[$third], config('leveling.action')['updateAccountAndPassword']], [$orderDatas]);
                                }
                            }
                        }
                    }

                    if ($requestData['game_leveling_day'] > $orderDetail['game_leveling_day'] || ($requestData['game_leveling_day'] == $orderDetail['game_leveling_day'] && $requestData['game_leveling_hour'] > $orderDetail['game_leveling_hour'])) {
                         // 接口增加的天数
                        $addDays = bcsub($request->data['game_leveling_day'], $order->detail()->where('field_name', 'game_leveling_day')->value('field_value'), 0);
                        // 更新值
                        OrderDetail::where('order_no', $orderNo)->where('field_name', 'game_leveling_day')->update([
                            'field_value' => $requestData['game_leveling_day']
                        ]);
                        // 增加的小时数
                        $addHours = bcsub($request->data['game_leveling_hour'], $order->detail()->where('field_name', 'game_leveling_hour')->value('field_value'), 0);
                        // 更新值
                        OrderDetail::where('order_no', $orderNo)->where('field_name', 'game_leveling_hour')->update([
                            'field_value' => $requestData['game_leveling_hour']
                        ]);

                         // 其他平台通用加时
                        if (config('leveling.third_orders')) {
                             // 获取订单和订单详情以及仲裁协商信息
                            $orderDatas = $this->getOrderAndOrderDetailAndLevelingConsult($order->no);
                            // 遍历代练平台
                                foreach (config('leveling.third_orders') as $third => $thirdOrderNoName) {
                                    // 如果订单详情里面存在某个代练平台的订单号，撤单此平台订单
                                    if ($third == $orderDatas['third'] && isset($orderDatas['third_order_no']) && ! empty($orderDatas['third_order_no'])) {
                                    // 控制器-》方法-》参数
                                    call_user_func_array([config('leveling.controller')[$third], config('leveling.action')['addTime']], [$orderDatas]);
                                }
                            }
                        }
                    } elseif ($requestData['game_leveling_day'] < $orderDetail['game_leveling_day'] || ($requestData['game_leveling_day'] == $orderDetail['game_leveling_day'] && $requestData['game_leveling_hour'] < $orderDetail['game_leveling_hour'])) {
                        return response()->ajax(0, '请重新选择天和小时，代练时间只可增加');
                    }
                }
                // 待验收 可加价格
                if ($order->status == 14) {

                    if ($order->price < $requestData['game_leveling_amount']) {
                        $addAmount = bcsub($request->data['game_leveling_amount'], $order->amount, 2);
                        $amount = $requestData['game_leveling_amount'] - $order->price;

                        if(checkPayment($order->no)) {
                            Asset::handle(new Expend($amount, 7, $orderNo, '代练改价支出', $order->creator_primary_user_id));
                        }

                        $order->price = $requestData['game_leveling_amount'];
                        $order->amount = $requestData['game_leveling_amount'];
                        $order->save();

                        OrderDetail::where('order_no', $orderNo)->where('field_name', 'game_leveling_amount')->update([
                            'field_value' => $requestData['game_leveling_amount']
                        ]);
                        // 接口加价
                        // $order->addAmount = $addAmount;
                        // event(new AutoRequestInterface($order, 'addPrice'));
                        // 加价 其他平台通用
                        if (config('leveling.third_orders')) {
                            // 获取订单和订单详情以及仲裁协商信息
                            $orderDatas = $this->getOrderAndOrderDetailAndLevelingConsult($order->no);
                           // 遍历代练平台
                            foreach (config('leveling.third_orders') as $third => $thirdOrderNoName) {
                                // 如果订单详情里面存在某个代练平台的订单号，撤单此平台订单
                                if ($third == $orderDatas['third'] && isset($orderDatas['third_order_no']) && ! empty($orderDatas['third_order_no'])) {
                                    // 控制器-》方法-》参数
                                    call_user_func_array([config('leveling.controller')[$third], config('leveling.action')['addMoney']], [$orderDatas]);
                                }
                            }
                        }
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
                        if ($orderDetail[$key] != $value) {
                            // 更新值
                            OrderDetail::where('order_no', $orderNo)->where('field_name', $key)->update([
                                'field_value' => $value
                            ]);
                            if ($key == 'source_price') {
                                $order->original_amount = $requestData[$key];
                                $order->save();
                            }
                             $changeHistory = '编辑:' . $orderDetailDisplayName[$key] . '   编辑前：' . $orderDetail[$key] . ' 编辑后：' . $requestData[$key];
                            $history[] = [
                                'order_no' => $orderNo,
                                'user_id' => auth()->user()->id,
                                'creator_primary_user_id' => auth()->user()->getPrimaryUserId(),
                                'name' => '编辑',
                                'type' => 22,
                                'before' => serialize($orderDetail[$key]),
                                'after' => serialize($requestData[$key]),
                                'description' => $changeHistory,
                                'created_at' => date('Y-m-d H:i:s'),
                            ];
                        }
                    }
                }
                if ($history) {
                    \DB::table('order_histories')->insert($history);
                }
            }
            $orderDetails = OrderDetail::where('order_no', $order->no)
                ->pluck('field_value', 'field_name')
                ->toArray();

            // 如果有补款单号，那么更新此单关联订单的补款单号
            $otherOrders = OrderModel::where('foreign_order_no', $order->foreign_order_no)
                ->where('foreign_order_no', '!=', '')
                ->get();

            if (isset($otherOrders) && ! empty($otherOrders)) {
                foreach($otherOrders as $otherOrder) {
                    OrderDetail::where('order_no', $otherOrder->no)
                        ->where('field_name', 'source_order_no_1')
                        ->where('order_no', '!=', $order->no)
                        ->update(['field_value' => $orderDetails['source_order_no_1']]);
                }

                foreach($otherOrders as $otherOrder) {
                    OrderDetail::where('order_no', $otherOrder->no)
                        ->where('field_name', 'source_order_no_2')
                        ->where('order_no', '!=', $order->no)
                        ->update(['field_value' => $orderDetails['source_order_no_2']]);
                }

                // 将其他单的来源价格更新为此单的来源价格
                foreach ($otherOrders as $otherOrder) {
                    OrderDetail::where('order_no', $otherOrder->no)
                        ->where('order_no', '!=', $order->no)
                        ->where('field_name', 'source_price')
                        ->update(['field_value' => $orderDetails['source_price']]);
                    OrderModel::where('no', $otherOrder->no)
                        ->where('no', '!=', $order->no)
                        ->update(['original_price' =>  $orderDetails['source_price']]);
                }
            }

            // 将来源价格同步到订单表
            $order->original_amount = $orderDetails['source_price'];
            $order->original_price = $orderDetails['source_price'];
            $order->save();

            // 写入基础数据表
            event(new OrderBasicData($order));

            $this->checkIfAutoMarkup($order, $orderDetails);
        } catch (CustomException $customException) {
            DB::rollBack();
            return response()->ajax(0, $customException->getMessage());
        } catch (DailianException $e) {
            DB::rollBack();
            return response()->ajax(0, $e->getMessage());
        } catch (AssetException $e) {
            DB::rollBack();
            return response()->ajax(0, $e->getMessage());
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->ajax(0, $exception->getMessage());
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
        $delivery = $request->input('delivery', 0); // 是否将淘宝订单发货
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
            } else if ($keyWord == 'complete') { // 订单完成操作
                (new Complete())->run($orderNo, auth()->user()->id, 1, (int)$delivery);
            } else if ($keyWord == 'applyComplete') { // 申请完成
                // 如果有图片则存储图片
                $pic = [];
                if (isBase64($request->pic1)) {
                    $pic[]  = [
                        'order_no' => $orderNo,
                        'channel_name' => '',
                        'third_order_no' => '',
                        'file_name' => '',
                        'third_file_name' => '',
                        'third_file_url' => '',
                        'size' => '',
                        'mime_type' => '',
                        'md5' => '',
                        'description' => '',
                        'url' => base64ToImg($request->pic1, 'apply_complete'),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                }
                if (isBase64($request->pic2)) {
                    $pic[]  = [
                        'order_no' => $orderNo,
                        'channel_name' => '',
                        'third_order_no' => '',
                        'file_name' => '',
                        'third_file_name' => '',
                        'third_file_url' => '',
                        'size' => '',
                        'mime_type' => '',
                        'md5' => '',
                        'description' => '',
                        'url' => base64ToImg($request->pic2, 'apply_complete'),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                }
                if (isBase64($request->pic3)) {
                    $pic[]  = [
                        'order_no' => $orderNo,
                        'channel_name' => '',
                        'third_order_no' => '',
                        'file_name' => '',
                        'third_file_name' => '',
                        'third_file_url' => '',
                        'size' => '',
                        'mime_type' => '',
                        'md5' => '',
                        'description' => '',
                        'url' => base64ToImg($request->pic3, 'apply_complete'),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                }
                OrderAttachment::insert($pic);
                DailianFactory::choose($keyWord)->run($orderNo, $userId);
            } else if ($keyWord == 'cancelComplete') {
                // 查找订单相关图片删除
                $orderAttachment = OrderAttachment::where('order_no', $orderNo)->get();
                foreach ($orderAttachment as $item) {
                    // 删除图片
                    unlink(public_path($item->url));
                    $item->delete();
                }
                DailianFactory::choose($keyWord)->run($orderNo, $userId);
            } else {
                DailianFactory::choose($keyWord)->run($orderNo, $userId);
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
            // 订单数据
            $order = OrderModel::where('no', $request->orderNo)->first();

            $data['order_no'] = $request->orderNo;
            $data['amount'] = $request->data['amount'];
            $data['deposit'] = $request->data['deposit'];
            $data['user_id'] = Auth::id();
            $data['revoke_message'] = $request->data['revoke_message'];

            // 如果是接单账号则写将值写入 api_amount 与 api_deposit
            if (auth()->user()->getPrimaryUserId() == $order->gainer_primary_user_id) {
                $data['api_amount'] =  $request->data['amount'];
                $data['api_deposit'] =  $request->data['deposit'];
                $data['consult'] = 2;
            }

            // 订单双金
            $safeDeposit = $order->detail()->where('field_name', 'security_deposit')->value('field_value');
            $effectDeposit = $order->detail()->where('field_name', 'efficiency_deposit')->value('field_value');
            $orderDeposit = bcadd($safeDeposit, $effectDeposit);
            $isOverDeposit = bcsub($orderDeposit, $data['deposit']);
            $isOverAmount = bcsub($order->amount, $data['amount']);
            // 写入双金与订单双击比较
            if ($isOverDeposit < 0) {
                return response()->ajax(0, '操作失败！要求退回双金金额不能大于订单双金!');
            }
            // 写入代练费与订单代练费比较
            if ($isOverAmount < 0) {
                return response()->ajax(0, '操作失败！要求退回代练费不能大于订单代练费!');
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
//            LevelingConsult::updateOrCreate(['order_no' => $data['order_no']], $data);
            // 改状态
            (new Revoking())->run($data['order_no'], Auth::id(), $data);
//            DailianFactory::choose('revoke')->run($data['order_no'], Auth::id());
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
            $pic1 = $request->pic1;
            $pic2 = $request->pic2;
            $pic3 = $request->pic3;

            if (empty($pic1) && empty($pic2) && empty($pic3)) {
                return response()->ajax(0, '请至少传入一张图片!');
            }

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
            (new Arbitrationing())->run($data['order_no'], $userId, 1, ['pic1' => $pic1, 'pic2' => $pic2, 'pic3' => $pic3]);
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
                $result = sendSms(Auth::user()->getPrimaryUserId(),
                    $orderInfo->no,
                    $orderArr['client_phone'],
                    $request->contents,
                    '代练短信费',
                    $orderArr['source_order_no'],
                    $orderArr['third_order_no'],
                    $orderArr['third']
                );
                return response()->ajax($result['status'], $result['message']);
            } else {
                return response()->ajax(0, '您没有填写客户手机号');
            }
        }
        return response()->ajax(0, '发送失败');
    }

    /**
     * 获取订单，订单详情，协商仲裁的所有信息
     * @param $orderNo
     * @return array
     * @throws DailianException
     */
    public function getOrderAndOrderDetailAndLevelingConsult($orderNo)
    {
        $collectionArr =  DB::select("
            SELECT a.order_no, 
                MAX(CASE WHEN a.field_name='region' THEN a.field_value ELSE '' END) AS region,
                MAX(CASE WHEN a.field_name='serve' THEN a.field_value ELSE '' END) AS serve,
                MAX(CASE WHEN a.field_name='account' THEN a.field_value ELSE '' END) AS account,
                MAX(CASE WHEN a.field_name='password' THEN a.field_value ELSE '' END) AS password,
                MAX(CASE WHEN a.field_name='source_order_no' THEN a.field_value ELSE '' END) AS source_order_no,
                MAX(CASE WHEN a.field_name='role' THEN a.field_value ELSE '' END) AS role,
                MAX(CASE WHEN a.field_name='game_leveling_type' THEN a.field_value ELSE '' END) AS game_leveling_type,
                MAX(CASE WHEN a.field_name='game_leveling_title' THEN a.field_value ELSE '' END) AS game_leveling_title,
                MAX(CASE WHEN a.field_name='game_leveling_instructions' THEN a.field_value ELSE '' END) AS game_leveling_instructions,
                MAX(CASE WHEN a.field_name='game_leveling_requirements' THEN a.field_value ELSE '' END) AS game_leveling_requirements,
                MAX(CASE WHEN a.field_name='auto_unshelve_time' THEN a.field_value ELSE '' END) AS auto_unshelve_time,
                MAX(CASE WHEN a.field_name='game_leveling_amount' THEN a.field_value ELSE '' END) AS game_leveling_amount,
                MAX(CASE WHEN a.field_name='game_leveling_day' THEN a.field_value ELSE '' END) AS game_leveling_day,
                MAX(CASE WHEN a.field_name='game_leveling_hour' THEN a.field_value ELSE '' END) AS game_leveling_hour,
                MAX(CASE WHEN a.field_name='security_deposit' THEN a.field_value ELSE '' END) AS security_deposit,
                MAX(CASE WHEN a.field_name='efficiency_deposit' THEN a.field_value ELSE '' END) AS efficiency_deposit,
                MAX(CASE WHEN a.field_name='user_phone' THEN a.field_value ELSE '' END) AS user_phone,
                MAX(CASE WHEN a.field_name='user_qq' THEN a.field_value ELSE '' END) AS user_qq,
                MAX(CASE WHEN a.field_name='source_price' THEN a.field_value ELSE '' END) AS source_price,
                MAX(CASE WHEN a.field_name='client_name' THEN a.field_value ELSE '' END) AS client_name,
                MAX(CASE WHEN a.field_name='client_phone' THEN a.field_value ELSE '' END) AS client_phone,
                MAX(CASE WHEN a.field_name='client_qq' THEN a.field_value ELSE '' END) AS client_qq,
                MAX(CASE WHEN a.field_name='client_wang_wang' THEN a.field_value ELSE '' END) AS client_wang_wang,
                MAX(CASE WHEN a.field_name='game_leveling_require_day' THEN a.field_value ELSE '' END) AS game_leveling_require_day,
                MAX(CASE WHEN a.field_name='game_leveling_require_hour' THEN a.field_value ELSE '' END) AS game_leveling_require_hour,
                MAX(CASE WHEN a.field_name='customer_service_remark' THEN a.field_value ELSE '' END) AS customer_service_remark,
                MAX(CASE WHEN a.field_name='receiving_time' THEN a.field_value ELSE '' END) AS receiving_time,
                MAX(CASE WHEN a.field_name='checkout_time' THEN a.field_value ELSE '' END) AS checkout_time,
                MAX(CASE WHEN a.field_name='customer_service_name' THEN a.field_value ELSE '' END) AS customer_service_name,
                MAX(CASE WHEN a.field_name='third_order_no' THEN a.field_value ELSE '' END) AS third_order_no,
                MAX(CASE WHEN a.field_name='third' THEN a.field_value ELSE '' END) AS third,
                MAX(CASE WHEN a.field_name='poundage' THEN a.field_value ELSE '' END) AS poundage,
                MAX(CASE WHEN a.field_name='price_markup' THEN a.field_value ELSE '' END) AS price_markup,
                MAX(CASE WHEN a.field_name='show91_order_no' THEN a.field_value ELSE '' END) AS show91_order_no,
                MAX(CASE WHEN a.field_name='mayi_order_no' THEN a.field_value ELSE '' END) AS mayi_order_no,
                MAX(CASE WHEN a.field_name='dd373_order_no' THEN a.field_value ELSE '' END) AS dd373_order_no,
                MAX(CASE WHEN a.field_name='dailianmama_order_no' THEN a.field_value ELSE '' END) AS dailianmama_order_no,
                MAX(CASE WHEN a.field_name='wanzi_order_no' THEN a.field_value ELSE '' END) AS wanzi_order_no,
                MAX(CASE WHEN a.field_name='hatchet_man_qq' THEN a.field_value ELSE '' END) AS hatchet_man_qq,
                MAX(CASE WHEN a.field_name='hatchet_man_phone' THEN a.field_value ELSE '' END) AS hatchet_man_phone,
                MAX(CASE WHEN a.field_name='order_password' THEN a.field_value ELSE '' END) AS order_password,
                MAX(CASE WHEN a.field_name='game_leveling_requirements_template' THEN a.field_value ELSE '' END) AS game_leveling_requirements_template,
                b.no,
                b.status as order_status,
                b.created_at as order_created_at,
                b.amount,
                b.creator_user_id, 
                b.creator_primary_user_id, 
                b.game_id, 
                b.gainer_user_id, 
                b.gainer_primary_user_id,
                c.user_id,
                c.amount AS pay_amount,
                c.deposit,
                c.api_amount,
                c.api_deposit,
                c.api_service,
                c.status,
                c.consult,
                c.complain,
                c.complete,
                c.remark,
                c.revoke_message,
                c.complain_message
            FROM order_details a
            LEFT JOIN orders b
            ON a.order_no = b.no
            LEFT JOIN leveling_consults c
            ON a.order_no = c.order_no
            WHERE a.order_no='$orderNo'");
        
        $collection = is_array($collectionArr) ? $collectionArr[0] : '';

        if (empty($collection) || ! $collection->no) {
            throw new DailianException('订单号错误');
        }

        return (array) $collection;
    }

    /**
     * 检查是否设置了每小时自动加价
     * @param $order
     * @param $orderDetails
     * @return bool
     */
    public function checkIfAutoMarkup($order, $orderDetails)
    {
        if (! isset($orderDetails['markup_range']) || empty($orderDetails['markup_range']) || ! isset($orderDetails['markup_top_limit']) || empty($orderDetails['markup_top_limit'])) {
            Redis::hDel('order:automarkup-every-hour', $order->no);
        }
        // 如果这笔订单存在加价幅度和加价上限，
        if (isset($orderDetails['markup_range']) && ! empty($orderDetails['markup_range']) && isset($orderDetails['markup_top_limit']) && ! empty($orderDetails['markup_top_limit'])) {
            $bool = bcsub($orderDetails['game_leveling_amount'], $orderDetails['markup_top_limit']) < 0 ? true : false;

            if (! $bool) {
                Redis::hDel('order:automarkup-every-hour', $order->no);
                return false;
            }
            // 将此订单存入哈希
            $key = $order->no;
            $name = "order:automarkup-every-hour";
            $value = "0@".$orderDetails['game_leveling_amount']."@".$order->updated_at;

            Redis::hSet($name, $key, $value);
        }
    }

    /**
     * 自动计算来源价格
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function sourcePrice(Request $request)
    {
        try {
            $value = 0;
            $sourceArr = [];
            if (isset($request->source_no) && ! empty($request->source_no) && is_numeric($request->source_no)) {
                $sourceArr[] = $request->source_no;
            }
            if (isset($request->source_no1) && ! empty($request->source_no1) && is_numeric($request->source_no1)) {
                $sourceArr[] = $request->source_no1;
            }
            if (isset($request->source_no2) && ! empty($request->source_no2) && is_numeric($request->source_no2)) {
                $sourceArr[] = $request->source_no2;
            }
            $orderDetails = OrderDetail::where('order_no', $request->no)
                ->pluck('field_value', 'field_name')
                ->toArray();

            if (isset($orderDetails['source_order_no']) && ! empty($orderDetails['source_order_no'])) {
                $sourceArr[] = $orderDetails['source_order_no'];
            }
            $value = TaobaoTrade::whereIn('tid', $sourceArr)->sum('payment'); // 自动计算的价格

            if (empty($value)) {
                $value = 0;
            }
            // dd($value, $sourceArr);
            // 如果用户手写了来源价格,谁大就返回谁
            if (isset($request->source_price) && ! empty($request->source_price) && is_numeric($request->source_price)) {
                if ($request->source_price > $value) {
                    return response()->ajax(1, $request->source_price);
                } 
            }
            return response()->ajax(1, $value);
        } catch (Exception $e) {
            return response()->ajax(0, 0);
        }
        return response()->ajax(0, 0);
    }

    /**
     * v1订单列表
     * @param Request $request
     * @param OrderRepository $orderRepository
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function indexNew(Request $request, OrderRepository $orderRepository)
    {
        $status = $request->input('status', 0);
        $no = $request->input('no', '');
        $taobaoStatus = $request->input('taobao_status', 0);
        $gameId = $request->input('game_id', 0);
        $wangWang = $request->input('wang_wang');
        $customerServiceName = $request->input('customer_service_name');
        $platform = $request->input('platform', 0);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $levelingType = $request->input('end_date');

        $game = $this->game;
        $employee = User::where('parent_id', Auth::user()->getPrimaryUserId())->get();

        if ($request->export) {
            return $orderRepository->levelingExport($status, $no,  $taobaoStatus,  $gameId, $wangWang, $customerServiceName, $platform, $startDate, $endDate, $levelingType);
        }

        // 获取订单
        $orders = $orderRepository->levelingDataList($status, $no,  $taobaoStatus,  $gameId, $wangWang, $customerServiceName, $platform, $startDate, $endDate, $levelingType);

        // 查询各状态订单数
        $statusCount = $orderRepository->levelingOrderCount($status, $no,  $taobaoStatus,  $gameId, $wangWang, $customerServiceName, $platform, $startDate, $endDate, $levelingType);

        $allStatusCount = OrderModel::where('creator_primary_user_id', auth()->user()->getPrimaryUserId())
            ->where('service_id', 4)->where('status', '!=', 24)->count();

        return view('frontend.v1.workbench.leveling.index')->with([
            'orders' => $orders,
            'game' => $game,
            'employee' => $employee,
//            'tags' => $tags,
            'no' => $no,
            'customerServiceName' => $customerServiceName,
            'gameId' => $gameId,
            'status' => $status,
            'taobaoStatus' => $taobaoStatus,
            'wangWang' => $wangWang,
            'platform' => $platform,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'statusCount' => $statusCount,
            'allStatusCount' => $allStatusCount,
            'fullUrl' => $request->fullUrl(),
        ]);
    }

    /**
     * @param Request $request
     * @param GameRepository $gameRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function createNew(Request $request, GameRepository $gameRepository)
    {
        $game = $this->game;
        $tid = $request->tid;
        $gameId = $request->game_id ? $request->game_id : 1;
        $businessmanInfo = auth()->user()->getPrimaryInfo();

        // 有淘宝订单则更新淘宝订单卖家备注
        $fixedInfo = [];
        $taobaoTrade = TaobaoTrade::where('tid', $tid)->first();
        if ($taobaoTrade) {
            if (empty($taobaoTrade->seller_memo)) {
                // 获取备注并更新
                $client = new TopClient;
                $client->format = 'json';
                $client->appkey = '12141884';
                $client->secretKey = 'fd6d9b9f6ff6f4050a2d4457d578fa09';

                $req = new TradeFullinfoGetRequest;
                $req->setFields("tid, type, status, payment, orders, seller_memo");
                $req->setTid($tid);
                $resp = $client->execute($req, taobaoAccessToken($taobaoTrade->seller_nick));

                if (!empty($resp->trade->seller_memo)) {
                    $taobaoTrade->seller_memo = $resp->trade->seller_memo;
                    $taobaoTrade->save();
                }
            }
            // 从收货地址中拆分区服角色信息
            $receiverAddress = explode("\r\n", trim($taobaoTrade->receiver_address));
            // 获取抓取商品配置
            $goodsConfig = AutomaticallyGrabGoods::where('foreign_goods_id', $taobaoTrade->num_iid)->first();

            // 如果游戏为DNF并且是推荐号则生成固定填入的订单数据
            if ($goodsConfig->game_id == 86 && $goodsConfig->type == 1) { //  && $goodsConfig->type == 1
                $fixedInfo = $this->dnfFixedInfo($receiverAddress, $taobaoTrade);
            }
        }

        return view('frontend.v1.workbench.leveling.create', compact('game', 'tid', 'gameId', 'taobaoTrade', 'businessmanInfo', 'receiverAddress', 'fixedInfo'));
    }

    /**
     * 增加代练价格
     * @param Request $request
     * @return mixed
     */
    public function addAmount(Request $request)
    {
        $orderNo = $request->no;
        $amount = $request->amount;

        DB::beginTransaction();
        try {
            $order = OrderModel::where('no', $orderNo)->lockForUpdate()->first();

            if ($order) {
                // 加价
                $newAmount = $order->price + $amount;
                if ($newAmount > $order->price) {
                    $addAmount = bcsub($newAmount, $order->price);

                    if (checkPayment($order->no)) {
                        Asset::handle(new Expend($addAmount, 7, $orderNo, '代练改价支出', $order->creator_primary_user_id));
                    }

                    $order->price = $newAmount;
                    $order->amount = $newAmount;
                    $order->save();

                    OrderDetail::where('order_no', $orderNo)->where('field_name', 'game_leveling_amount')->update([
                        'field_value' => $newAmount
                    ]);

                    // 调用外部接口 加价
                    if (config('leveling.third_orders')) {
                        // 获取订单和订单详情以及仲裁协商信息
                        $updateData = $this->getOrderAndOrderDetailAndLevelingConsult($order->no);
                        // 遍历代练平台
                        foreach (config('leveling.third_orders') as $third => $thirdOrderNoName) {
                            if ($third == $updateData['third'] && isset($updateData['third_order_no']) && ! empty($updateData['third_order_no'])) {
                                call_user_func_array([config('leveling.controller')[$third], config('leveling.action')['addMoney']], [$updateData]);
                            }
                        }
                    }
                    $history[] = [
                        'order_no' => $orderNo,
                        'user_id' => auth()->user()->id,
                        'creator_primary_user_id' => auth()->user()->getPrimaryUserId(),
                        'name' => '编辑',
                        'type' => 22,
                        'before' => serialize([]),
                        'after' => serialize([]),
                        'description' =>  '编辑:代练价格 编辑前：' . bcsub($newAmount, $addAmount, 0). ' 编辑后：' . $newAmount,
                        'created_at' => date('Y-m-d H:i:s'),
                    ];

                    if ($history) {
                        \DB::table('order_histories')->insert($history);
                    }
                }
            } else {
                return response()->ajax(0, '加价失败订单不存在');
            }
        } catch (\Exception $exception) {
            return response()->ajax(0, '加价失败, ' . $exception->getMessage());
        }
        DB::commit();
        return response()->ajax(1, '加价成功');
    }

    /**
     * 增加代练时间
     * @param Request $request
     * @param OrderDetailRepository $detailRepository
     * @return mixed
     */
    public function addTime(Request $request, OrderDetailRepository $detailRepository)
    {
        DB::beginTransaction();
        try {
            $orderNo = $request->no;
            $day = $request->day;
            $hour = $request->hour;

            $detail = $detailRepository->getByOrderNo($orderNo);

            $newDay = $day + $detail['game_leveling_day'];
            $newHour = $hour + $detail['game_leveling_hour'];

            // 更新代练天数
            OrderDetail::where('order_no', $orderNo)->where('field_name', 'game_leveling_day')->update([
                'field_value' => $newDay
            ]);
            // 更新代练小时
            OrderDetail::where('order_no', $orderNo)->where('field_name', 'game_leveling_hour')->update([
                'field_value' => $newHour
            ]);

            // 调用接口更新值
            if (config('leveling.third_orders')) {
                // 获取订单和订单详情以及仲裁协商信息
                $updateData = $this->getOrderAndOrderDetailAndLevelingConsult($orderNo);
                // 遍历代练平台
                foreach (config('leveling.third_orders') as $third => $thirdOrderNoName) {
                    // 如果订单详情里面存在某个代练平台的订单号，撤单此平台订单
                    if ($third == $updateData['third'] && isset($updateData['third_order_no']) && ! empty($updateData['third_order_no'])) {
                        // 控制器-》方法-》参数
                        call_user_func_array([config('leveling.controller')[$third], config('leveling.action')['addTime']], [$updateData]);
                    }
                }
            }

            // 写操作记录
            $history[] = [
                'order_no' => $orderNo,
                'user_id' => auth()->user()->id,
                'creator_primary_user_id' => auth()->user()->getPrimaryUserId(),
                'name' => '编辑',
                'type' => 22,
                'before' => serialize([]),
                'after' => serialize([]),
                'description' =>  '编辑:代练时间  编辑前：' . $detail['game_leveling_day'] . '天' . $detail['game_leveling_hour'] . '小时' .  ' 编辑后：' . $newDay. '天' . $newHour . '小时',
                'created_at' => date('Y-m-d H:i:s'),
            ];

            if ($history) {
                \DB::table('order_histories')->insert($history);
            }

        } catch (\Exception $exception) {
            return response()->ajax(0, '增加时间失败, ' . $exception->getMessage());
        }
        DB::commit();
        return response()->ajax(1, '增加时间成功');
    }

    /***
     * 置顶
     * @param Request $request
     */
    public function setTop(Request $request)
    {
        $orderNo = $request->no;

        try {
            // 调用接口更新值
            if (config('leveling.third_orders')) {
                // 获取订单和订单详情以及仲裁协商信息
                $updateData = $this->getOrderAndOrderDetailAndLevelingConsult($orderNo);
                // 遍历代练平台
                foreach (config('leveling.third_orders') as $third => $thirdOrderNoName) {
                    call_user_func_array([config('leveling.controller')[$third], config('leveling.action')['setTop']], [$updateData]);
                }
            }

        } catch (\Exception $exception) {

        }
        OrderDetail::where('order_no', $orderNo)->where('field_name', 'is_top')->update(['field_value'=> 1]);
        return response()->ajax(1, '置顶成功');
    }

    /**
     * 解析DNF区服
     * @param $region
     * @return array
     */
    public function dnfRegionServe($region)
    {
        $regionArr = explode(':', $region);

        // 取省份名字
        if (strpos($regionArr[1], '黑龙') !== false || strpos($regionArr[1], '内蒙') !== false) {
            $province = mb_substr($regionArr[1],0 ,3);
        } else {
            $province = mb_substr($regionArr[1],0 ,2);
        }
        // 去除省份
        $noProvince = str_replace($province, '', $regionArr[1]);
        // 将汉字转为阿拉伯数字
        $num = [
            '一' => 1,
            '二' => 2,
            '三' => 3,
            '四' => 4,
            '五' => 5,
            '六' => 6,
            '七' => 7,
            '八' => 8,
            '九' => 9,
        ];
        $serveNum = str_replace('区', '', str_replace(array_keys($num), array_values($num), $noProvince));

        // 区
        if ($province == '云南' || $province == '贵州' || $province == '云贵') {
            $region = '云贵区';
        } else {
            $region = $province . '区';
        }

        // 服
        $serve = '';
        if ($province == '北京' && in_array($serveNum, [2, 4])) {
            $serve = '北京2/4区';
        } else if($province == '江苏' && in_array($serveNum, [5, 7])) {
            $serve = '江苏5/7区';
        } else if($province == '广西' && in_array($serveNum, [2, 4])) {
            $serve = '广西2/4区';
        }  else if($province == '东北' && in_array($serveNum, [4, 5, 6])) {
            $serve = '东北4/5/6区';
        } else if($province == '东北' && in_array($serveNum, [3, 7])) {
            $serve = '东北3/7区';
        } else if($province == '山东' && in_array($serveNum, [2, 7])) {
            $serve = '山东2/7区';
        } else if($province == '浙江' && in_array($serveNum, [4, 5])) {
            $serve = '浙江4/5区';
        } else if($province == '上海' && in_array($serveNum, [4, 5])) {
            $serve = '上海4/5区';
        } else if($province == '河北' && in_array($serveNum, [2, 3])) {
            $serve = '河北2/3区';
        } else if($province == '福建' && in_array($serveNum, [3, 4])) {
            $serve = '福建3/4区';
        } else if($province == '黑龙江' && in_array($serveNum, [2, 3])) {
            $serve = '黑龙江2/3区';
        } else if($province == '西北' && in_array($serveNum, [2, 3])) {
            $serve = '西北2/3区';
        } else if($province == '陕西' && in_array($serveNum, [2, 3])) {
            $serve = '陕西2/3区';
        } else if($province == '黑龙江' && in_array($serveNum, [1, 2])) {
            $serve = '吉林1/2区';
        } else {
            $serve = $province . $serveNum  . '区';
        }
        return ['region' => $region, 'serve' => $serve];
    }

    /**
     * DNF推荐号固定信息
     * @param $receiverAddress
     * @param $taobaoTrade
     * @return mixed
     */
    protected function dnfFixedInfo($receiverAddress, $taobaoTrade)
    {
        $regionServe = $this->dnfRegionServe($receiverAddress[0]);
        $role = explode(':', $receiverAddress[1]);
        // 固定的订单信息
        $fixedInfo['region'] = ['type' => 2, 'value' => $regionServe['region']];
        $fixedInfo['serve'] = ['type' => 2, 'value' => $regionServe['serve']];
        $fixedInfo['role'] = ['type' => 1, 'value' => $role[1]];
        $fixedInfo['account'] = ['type' => 1, 'value' => $role[1]];
        $fixedInfo['password'] = ['type' => 1, 'value' => '000000'];
        $fixedInfo['game_leveling_title'] = ['type' => 1, 'value' => 'DNF推荐号' . $regionServe['serve'] . $taobaoTrade->num . '次'];
        $fixedInfo['game_leveling_instructions'] = ['type' => 4, 'value' => 'DNF推荐号' . $regionServe['serve'] . $taobaoTrade->num . '次'];
        $fixedInfo['security_deposit'] = ['type' => 1, 'value' => 1];
        $fixedInfo['game_leveling_type'] = ['type' => 2, 'value' => '推荐号'];
        $fixedInfo['efficiency_deposit'] = ['type' => 1, 'value' => 1];
        $fixedInfo['game_leveling_day'] = ['type' => 2, 'value' => 0];
        $fixedInfo['game_leveling_hour'] = ['type' => 2, 'value' => 6];
        $fixedInfo['client_phone'] = ['type' => 1, 'value' => '13800138000'];

        return $fixedInfo;
    }

    /**
     * 获取仲裁信息
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getArbitrationInfo(Request $request)
    {
        $orderNo = $request->no;

        try {
            // 调用接口更新值
            if (config('leveling.third_orders')) {
                // 获取订单和订单详情以及仲裁协商信息
                $datas = $this->getOrderAndOrderDetailAndLevelingConsult($orderNo);

                if (! isset($datas['order_status']) || ! in_array($datas['order_status'], [16, 21])) {
                    return '暂无相关信息';
                }

                foreach (config('leveling.third_orders') as $third => $thirdOrderNoName) {
                    if ($third == $datas['third'] && isset($datas['third_order_no']) && ! empty($datas['third_order_no'])) {
                        $arbitrationInfos = call_user_func_array([config('leveling.controller')[$third], config('leveling.action')['getArbitrationInfo']], [$datas]);
                    }
                }

                if ($request->ajax()) {
                    return response()->json(view()->make('frontend.v1.workbench.leveling.arbitration-info', [
                        'arbitrationInfos' => $arbitrationInfos,
                        'orderNo' => $orderNo,
                        'status' => $datas['order_status'],
                    ])->render());
                }
                return  view('frontend.v1.workbench.leveling.arbitration-info')->with([
                    'status' => $datas['order_status'],
                    'orderNo' => $orderNo,
                    'arbitrationInfos' => $arbitrationInfos,
                ]);
            }
        } catch (DailianException $e) {
            myLog('get-arbitration-advence', ['单号 DailianException' => $datas->order_no ?? '', '失败' => $e->getMessage()]);
        } catch (\Exception $exception) {
            myLog('get-arbitration-advence', ['单号 Exception' => $datas->order_no ?? '', '失败' => $exception->getMessage()]);
        } 
    }

    /**
     * 发送仲裁证据
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function addArbitrationInfo(Request $request)
    {
        $orderNo = $request->no;
        $datas = $this->getOrderAndOrderDetailAndLevelingConsult($orderNo);
        $datas['arbitration_id'] = $request->arbitration_id;
        $datas['add_content'] = $request->content;
        $datas['pic'] = $request->pic;
        try {
            // 调用接口更新值
            if (config('leveling.third_orders')) {
                // 遍历代练平台
                foreach (config('leveling.third_orders') as $third => $thirdOrderNoName) {
                    if ($third == $datas['third'] && isset($datas['third_order_no']) && ! empty($datas['third_order_no'])) {
                        call_user_func_array([config('leveling.controller')[$third], config('leveling.action')['addArbitrationInfo']], [$datas]);
                        return response()->ajax(1, '发送成功');
                    }
                }
            }
            return response()->ajax(0, '发送失败');
        } catch (DailianException $e) {
            myLog('add-arbitration-advence', ['单号' => $orderNo ?? '', '失败' => $e->getMessage()]);
            return response()->ajax(0, '发送失败');
        } catch (\Exception $exception) {
            myLog('add-arbitration-advence', ['单号' => $orderNo ?? '', '失败' => $exception->getMessage()]);
            return response()->ajax(0, '发送失败');
        } 
    }

    /**
     * 获取游戏代练类型
     * @param Request $request
     */
    public function getGameLevelingType(Request $request)
    {
        // 获取模版
        $templateId = GoodsTemplate::where('service_id', 4)->where('game_id', $request->game_id)->value('id');

        $levelingType = GoodsTemplateWidgetValue::where('goods_template_widget_id', function ($query) use ($templateId){
            $query->select('id')
                ->from(with(new GoodsTemplateWidget())->getTable())
                ->where('goods_template_id', $templateId)
                ->where('field_name', 'game_leveling_type');
        })->pluck('field_value');

        return response()->ajax(1, 'success', $levelingType);

    }

    /**
     * 获取订单状态的数量
     * @param Request $request
     * @param OrderRepository $orderRepository
     * @return \Illuminate\Http\RedirectResponse
     */
    public function orderStatusCount(Request $request, OrderRepository $orderRepository)
    {
        $no = $request->input('no', 0);
        $customerServiceName = $request->input('customer_service_name', 0);
        $gameId = $request->input('game_id', 0);
        $status = $request->input('status', 0);
        $wangWang = $request->input('wang_wang');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $taobaoStatus = $request->input('taobao_status', 0);
        $platform = $request->input('platform', 0);
        $levelingType = $request->input('game_leveling_type', 0);

        $statusCount = $orderRepository->levelingOrderCount($status, $no,  $taobaoStatus,  $gameId, $wangWang, $customerServiceName, $platform, $startDate, $endDate, $levelingType);
        $statusCount[100] = $orderRepository->levelingTaobaoRefundOrderCount($status, $no,  $taobaoStatus,  $gameId, $wangWang, $customerServiceName, $platform, $startDate, $endDate, $levelingType);

        return response()->ajax('1', 'success', $statusCount);
    }
}


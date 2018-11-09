<?php

namespace App\Http\Controllers\Frontend\Channel;

use App\Models\User;
use DB;
use Exception;
use App\Models\Game;
use Yansongda\Pay\Pay;
use App\Models\GameServer;
use App\Models\GameRegion;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\GameLevelingType;
use App\Models\GameLevelingOrder;
use App\Exceptions\DailianException;
use App\Models\GameLevelingChannelGame;
use App\Models\GameLevelingChannelOrder;
use App\Http\Controllers\Controller;

class GameLevelingChannelOrderController extends Controller
{
    /**
     * 渠道下单首页
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('channel.index');
    }

    /**
     * 获取所有的代练游戏
     * @return mixed
     */
    public function game()
    {
        try {
            $games = GameLevelingChannelGame::where('user_id', request('user_id', 2))
                ->pluck('game_name')
                ->unique()
                ->toArray();
        } catch (\Exception $e) {
            return response()->ajax(0, '数据异常');
        }
        return response()->ajax(1, $games);
    }

    /**
     * 获取代练类型
     * @return mixed
     */
    public function type()
    {
        try {
            $types = GameLevelingChannelGame::where('game_name', request('game_name'))
                ->where('user_id', request('user_id', 2))
                ->pluck('game_leveling_type_name')
                ->unique()
                ->toArray();
        } catch (\Exception $e) {
            return response()->ajax(0, []);
        }
        return response()->ajax(1, $types);
    }

    /**
     * 代练目标
     * @return array
     */
    public function target()
    {
        try {
            // 渠道游戏
            $gameLevelingChannelGame = GameLevelingChannelGame::where('game_name', request('game_name'))
                ->where('user_id', request('user_id', 2))
                ->where('game_leveling_type_name', request('type'))
                ->first();

            $targets = $gameLevelingChannelGame->gameLevelingChannelPrices->pluck('level', 'sort')->toArray();
        } catch (Exception $e) {
            response()->ajax(1, []);
        }
        return response()->ajax(1, $targets);
    }

    /**
     * 自动计算下单价格和时间等信息
     * @return mixed
     */
    public function compute()
    {
        try {
            $gameName = request('game_name');
            $type = request('type');
            $level = request('level');

            if (empty($gameName) || empty($type) || empty($level)) {
                return response()->ajax(0, '游戏或类型缺失');
            }

            $arrTarget = explode(',', $level);

            if (! is_array($arrTarget) || count($arrTarget) < 2) {
                return response()->ajax(0, '请正确选择代练目标');
            }
            $startLevel = $arrTarget[0];
            $endLevel = $arrTarget[1];

            // 渠道游戏
            $gameLevelingChannelGame = GameLevelingChannelGame::where('game_name', request('game_name'))
                ->where('user_id', request('user_id', 2))
                ->where('game_leveling_type_name', request('type'))
                ->first();

            // 找到层级
            $startNumber = $gameLevelingChannelGame->gameLevelingChannelPrices()
                ->where('level', $startLevel)
                ->value('sort');

            $endNumber = $gameLevelingChannelGame->gameLevelingChannelPrices()
                ->where('level', $endLevel)
                ->value('sort');

            if (empty($startNumber) || empty($endNumber)) {
                return response()->ajax(0, '请正确选择代练目标');
            }

            if ($startNumber >= $endNumber) {
                return response()->ajax(0, '请正确选择代练目标');
            }

            $price = $gameLevelingChannelGame->gameLevelingChannelPrices()
                ->where('sort', '>=', $startNumber)
                ->where('sort', '<', $endNumber)
                ->sum('price');

            $time = $gameLevelingChannelGame->gameLevelingChannelPrices()
                ->where('sort', '>=', $startNumber)
                ->where('sort', '<', $endNumber)
                ->sum('hour');

            $count = $gameLevelingChannelGame->gameLevelingChannelPrices()
                ->where('sort', '>=', $startNumber)
                ->where('sort', '<', $endNumber)
                ->count();

            $rebate = $gameLevelingChannelGame->gameLevelingChannelDiscounts()
                    ->where('level', '<=', $count)
                    ->min('discount') ?? 100;
            // dd($rebate);
            $staticRebate =$gameLevelingChannelGame->rebate ?? 100;

            $securityDeposit = $gameLevelingChannelGame->gameLevelingChannelPrices()
                ->where('sort', '>=', $startNumber)
                ->where('sort', '<', $endNumber)
                ->sum('security_deposit');

            $efficiencyDeposit = $gameLevelingChannelGame->gameLevelingChannelPrices()
                ->where('sort', '>=', $startNumber)
                ->where('sort', '<', $endNumber)
                ->sum('efficiency_deposit');

            $day = intval(bcdiv($time, 60, 0))+0;
            $hour = intval($time%60)+0;

            // 玩家支付价格(来源价格）
            $payment = bcmul($price, $rebate*0.01, 2)+0;

            if ($price*$rebate*0.01 > 0 && $price*$rebate*0.01 < 0.01) {
                $payment = 0.01;
            }
            // 优惠之前的价格
            $showPrice = bcmul($payment, 1.5, 2)+0;

            if($showPrice > 0 && $showPrice < 0.01) {
                $showPrice = 0.01;
            }
            // 发单平台价格
            $price = bcmul($payment, $staticRebate*0.01, 2)+0;

            if($payment*$staticRebate*0.01 > 0 && $payment*$staticRebate*0.01 < 0.01) {
                $price = 0.01;
            }
            // 展示的需要代练的时间
            $showTime = sec2Time($time*3600);
            // dd($day, $hour, $securityDeposit, $efficiencyDeposit);
        } catch (\Exception $e) {
            myLog('error', [$e->getMessage()]);
            return response()->ajax(0, '服务器错误');
        }
        return response()->ajax(1, ['showPrice' => $showPrice, 'payment' => $payment, 'price' => $price, 'showTime' => $showTime, 'startNumber' =>$startNumber, 'endNumber' => $endNumber, 'startLevel' => $startLevel, 'endLevel' => $endLevel, 'time' => $time, 'securityDeposit' => $securityDeposit, 'efficiencyDeposit' => $efficiencyDeposit,
            'day' => $day, 'hour' => $hour
        ]);
    }

    /**
     * 获取游戏区
     * @return mixed
     */
    public function region()
    {
        try {
            $gameId = Game::where('name', request('game_name'))->value('id');

            $regions = GameRegion::where('game_id', $gameId)
                ->pluck('name')
                ->toArray();

            return response()->ajax(1, $regions);
        } catch (Exception $e) {
            return response()->ajax(0, '服务器错误');
        }
    }

    /**
     * 区的服
     * @param Request $request
     * @return mixed
     */
    public function server(Request $request)
    {
        try {
            $gameId = Game::where('name', request('game_name'))->value('id');
            $region = GameRegion::where('game_id', $gameId)
                ->where('name', request('region'))
                ->first();

            $servers = $region->gameServers->pluck('name')->toArray();
            return response()->ajax(1, $servers);
        } catch (Exception $e) {
            return response()->ajax(0, '服务器错误');
        }
    }

    /**
     * 跳转到下单页面
     * @return \Illuminate\Http\RedirectResponse
     */
    public function go()
    {
        $gameName = request('gameName');
        $type = request('type');
        $price = request('price');
        $payment = request('payment');
        $time = request('time');
        $startNumber = request('startNumber');
        $endNumber = request('endNumber');
        $startLevel = request('startLevel');
        $endLevel = request('endLevel');
        $securityDeposit = request('securityDeposit');
        $efficiencyDeposit = request('efficiencyDeposit');
        $day = request('day');
        $hour = request('hour');

        if (empty($gameName) || empty($type) || empty($price) || empty($payment) || empty($time) || empty($startNumber) || empty($endNumber) || empty($startLevel) || empty($endLevel)) {
            return response()->ajax(0, '请完善页面信息');
        }
        return redirect(route('channel.place-order'))->with(compact('gameName', 'type', 'price', 'payment', 'time', 'startLevel', 'endLevel', 'startNumber', 'endNumber', 'securityDeposit', 'efficiencyDeposit', 'day', 'hour'));
    }

    /**
     * 下单的页面点击支付按钮支付
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function placeOrder(Request $request)
    {
        $data = $request->all();
        $gameName = $data['game_name'] ?? '';
        $type = $data['game_leveling_type'] ?? '';
        $price = $data['price'] ?? '';
        $payment = $data['payment'] ?? '';
        $time = $data['time'] ?? '';
        $showPrice = $data['showPrice'] ?? '';
        $showTime = $data['showTime'] ?? '';
        $startLevel = $data['startLevel'] ?? '';
        $endLevel = $data['endLevel'] ?? '';
        $securityDeposit = $data['security_deposit'];
        $efficiencyDeposit = $data['efficiency_deposit'];
        $day = $data['game_leveling_day'];
        $hour = $data['game_leveling_hour'];

        if (empty($gameName) || empty($type) || ! is_numeric($data['price']) || ! is_numeric($data['payment'])) {
            return response()->ajax(0, '请重新选择游戏');
        }

        if ($data['payment'] == 0) {
            return response()->ajax(0, '代练订单异常，请联系卖家');
        }

        return view('channel.place-order', compact('gameName', 'type', 'price', 'payment', 'time', 'showPrice', 'showTime', 'data', 'startLevel', 'endLevel', 'securityDeposit', 'efficiencyDeposit', 'day', 'hour'));
    }

    /**
     * 支付同步跳转
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Yansongda\Pay\Gateways\Wechat\WapGateway
     */
    public static function pay(Request $request)
    {
        try {
            // 游戏，区，服
            $gameId = Game::where('name', request('game_name'))->value('id');
            $region = GameRegion::where('game_id', $gameId)
                ->where('name', request('region'))
                ->first();
            $server = GameServer::where('game_region_id', $region->id)->first();
            $gameLevelingType = GameLevelingType::where('game_id', $gameId)
                ->where('name', request('game_leveling_type'))
                ->first();

            // 渠道游戏
            $gameLevelingChannelGame = GameLevelingChannelGame::where('game_name', request('game_name'))
                ->where('user_id', request('user_id', 2))
                ->where('game_leveling_type_name', request('game_leveling_type'))
                ->first();

            // 创建订单
            $data = [];
            $data['trade_no'] = generateOrderNo();
            $data['game_id'] = $gameId;
            $data['game_name'] = request('game_name', '');
            $data['game_leveling_type'] = request('game_leveling_type_name', '');
            $data['status'] = 0; // 未支付
            $data['day'] = request('game_leveling_day', 0);
            $data['hour'] = request('game_leveling_hour', 0);
            $data['amount'] = request('price', 0)+0;
            $data['discount_amount'] = 0;
            $data['refund_amount'] = 0;
            $data['payment_type'] = request('pay_type', 0);
            $data['demand'] = request('demand', '');
            $data['game_region_name'] = request('region', '');
            $data['game_region_id'] = $region->id;
            $data['game_server_name'] = request('server', '');
            $data['game_server_id'] = $server->id;
            $data['game_leveling_type_name'] = request('game_leveling_type');
            $data['game_leveling_type_id'] = $gameLevelingType->id;
            $data['demand'] = request('demand', '');
            $data['payment_amount'] = request('payment', 0)+0; // 实际支付金额
            $data['user_id'] = request('user_id', 2);
            $data['user_qq'] = '';
            $data['game_leveling_channel_user_id'] = 2;
            $data['player_phone'] = request('client_phone', 2);
            $data['player_qq'] = '';
            $data['game_role'] = request('role', '');
            $data['game_account'] = request('account', '');
            $data['game_password'] = request('password', '');
            $data['user_qq'] = $gameLevelingChannelGame->user_qq;
            $data['title'] = request('game_name').'-'.request('game_leveling_type').'-'.request('startLevel').'-'.request('endLevel');
            $data['security_deposit'] = request('security_deposit',0)+0;
            $data['efficiency_deposit'] = request('efficiency_deposit',0)+0;
            $data['requirement'] = $gameLevelingChannelGame->requirements;
            $data['explain'] = $gameLevelingChannelGame->instructions;
            $data['remark'] = '';

            GameLevelingChannelOrder::create($data);

            if (request('pay_type') == 1) { // 支付宝
                $orderConfig = [
                    'out_trade_no' => $data['trade_no'],
                    'total_amount' => $data['payment_amount'],
                    'subject'      => '代练订单支付',
                ];

                $basicConfig = config('alipay.base_config');

                return Pay::alipay($basicConfig)->wap($orderConfig);
            } elseif (request('pay_type') == 2) { // 微信
                $orderConfig = [
                    'out_trade_no'     => $data['trade_no'],
                    'total_fee'        => $data['payment_amount']*100, // 单位分
                    'body'             => '代练订单支付',
                    'spbill_create_ip' => static::getIp(),
                ];

                $basicConfig = config('wechat.base_config');
                $basicConfig['notify_url'] = config('wechat.base_config.notify_url').'/'.$data['trade_no'] ?? '';
                $basicConfig['return_url'] = config('wechat.return_url').'/'.$data['trade_no'] ?? '';
                // dd($basicConfig);
                return Pay::wechat($basicConfig)->wap($orderConfig);
            }
        } catch (Exception $e) {
            myLog('pay-error', [
                'message' => $e->getMessage(),
                'trade_no' => $data['trade_no'] ?? '',
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return back()->with(['miss' => '服务器异常！']);
        }
    }

    /**
     * 获取IP地址
     * @return string
     */
    public static function getIp()
    {
        if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $ip = getenv('REMOTE_ADDR');
        } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
    }

    /**
     * 阿里异步回调
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function alipayReturn(Request $request)
    {
        try {
            $basicConfig = config('alipay.base_config');

            $data = Pay::alipay($basicConfig)->verify();

            $gameLevelingChannelOrder = GameLevelingChannelOrder::where('trade_no', $data->out_trade_no)->first();

            if ($data) {
                return view('channel.success', compact('gameLevelingChannelOrder'));
            } else {
                return view('channel.demand');
            }
        } catch (Exception $e) {
            myLog('alipay-return-error', ['message' => $e->getMessage()]);
            return view('channel.index');
        }
    }

    /**
     * 阿里异步通知
     * @return mixed
     */
    public function alipayNotify()
    {
        myLog('test', [12231]);
        DB::beginTransaction();
        try {
            $basicConfig = config('alipay.base_config');
            $alipay = Pay::alipay($basicConfig);
            $data = $alipay->verify(); // 是的，验签就这么简单！

            // 成功更新订单状态
            if (isset($data) && ($data->trade_status == 'TRADE_SUCCESS' || $data->trade_status == 'TRADE_FINISHED')) {
                $gameLevelingChannelOrder = GameLevelingChannelOrder::where('trade_no', $data->out_trade_no)->first();
                // 验证订单号
                if (! isset($gameLevelingChannelOrder) || empty($gameLevelingChannelOrder)) {
                    throw new DailianException('订单号错误');
                }
                // 验证appid
                if ($data->app_id != $basicConfig['app_id']) {
                    throw new DailianException('APPID错误');
                }
                // 验证价格
                if ($data->total_amount != $gameLevelingChannelOrder->payment_amount) {
                    throw new DailianException('支付价格不一致');
                }

                $createOrderData = [];
                $createOrderData['game_id'] = $gameLevelingChannelOrder->game_id;
                $createOrderData['game_region_id'] = $gameLevelingChannelOrder->game_region_id;
                $createOrderData['game_server_id'] = $gameLevelingChannelOrder->game_server_id;
                $createOrderData['game_leveling_type_id'] = $gameLevelingChannelOrder->game_leveling_type_id;
                $createOrderData['channel_order_trade_no'] = '';
                $createOrderData['amount'] = $gameLevelingChannelOrder->amount;
                $createOrderData['security_deposit'] = $gameLevelingChannelOrder->security_deposit;
                $createOrderData['efficiency_deposit'] = $gameLevelingChannelOrder->efficiency_deposit;
                $createOrderData['title'] = $gameLevelingChannelOrder->title;
                $createOrderData['day'] = $gameLevelingChannelOrder->day;
                $createOrderData['hour'] = $gameLevelingChannelOrder->hour;
                $createOrderData['game_account'] = $gameLevelingChannelOrder->game_account;
                $createOrderData['game_password'] = $gameLevelingChannelOrder->game_password;
                $createOrderData['game_role'] = $gameLevelingChannelOrder->game_role;
                $createOrderData['seller_nick'] = '';
                $createOrderData['buyer_nick'] = '';
                $createOrderData['price_increase_step'] = '';
                $createOrderData['price_ceiling'] = '';
                $createOrderData['user_phone'] = '';
                $createOrderData['user_qq'] = $gameLevelingChannelOrder->user_qq;
                $createOrderData['player_phone'] = $gameLevelingChannelOrder->player_phone;
                $createOrderData['explain'] = $gameLevelingChannelOrder->explain;
                $createOrderData['requirement'] = $gameLevelingChannelOrder->requirement;

                $user = User::find(request('user_id', 2));
                // 下单
                GameLevelingOrder::placeOrder($user, $createOrderData);

                $gameLevelingChannelOrder->status = 1; // （未接单）已支付
                $gameLevelingChannelOrder->payment_type = 1; // 支付渠道
                $gameLevelingChannelOrder->channel_order_trade_no = $gameLevelingChannelOrder->trade_no; // 渠道订单
                $gameLevelingChannelOrder->save();
            } else {
                throw new Exception('订单支付失败');
            }
        } catch (DailianException $e) {
            // 退款逻辑
            DB::rollback();
            myLog('alipay-notify-error', ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            return Response::create('fail');
        } catch (Exception $e) {
            DB::rollback();
            myLog('alipay-notify-error', ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            return Response::create('fail');
        }
        DB::commit();
        return Response::create('success');
    }

    /**
     * 详情页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function show(Request $request)
    {
        try {
            $gameLevelingChannelOrder = GameLevelingChannelOrder::find(request('id'));

            $gameLevelingOrder = GameLevelingOrder::where('channel_order_trade_no', $gameLevelingChannelOrder->trade_no)
                ->with('gameLevelingOrderDetail')
                ->first();
        } catch (Exception $e) {
            return '暂无相关信息!';
        }
        return view('channel.show', compact('gameLevelingChannelOrder', 'gameLevelingOrder'));
    }

    /**
     * 微信同步通知
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function wechatReturn(Request $request)
    {
        try {
//            sleep(5);
            $basicConfig = config('wechat.find_config');
            $data = Pay::wechat($basicConfig)->find(['out_trade_no' => $request->no]);
            $gameLevelingChannelOrder = GameLevelingChannelOrder::where('trade_no', $request->no)->first();

            if (isset($gameLevelingChannelOrder) && ! empty($gameLevelingChannelOrder) && isset($data) && ! empty($data) && $data->trade_state == 'SUCCESS') {
//                return view('mobile.leveling.success', compact('mobileOrder'));
                myLog('wechat-return-success', ['data' => $data ?? '']);
            } else {
                myLog('wechat-return-fail', ['data' => $data ?? '']);
//                return view('mobile.leveling.demand');
            }
        } catch (Exception $e) {
            myLog('wechat-return-error', ['message' => $e->getMessage()]);
            return view('channel.index');
        }
    }

    /**
     * 微信异步通知
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function wechatNotify(Request $request)
    {
        DB::beginTransaction();
        try {
            $basicConfig = config('wechat.base_config');
            $basicConfig['notify_url'] = config('wechat.base_config.notify_url').'/'.$request->no ?? '';
            $weChat = Pay::wechat($basicConfig);
            $data = $weChat->verify();

            // 成功更新订单状态
            if (isset($data) && $data->return_code == 'SUCCESS') {
                $gameLevelingChannelOrder = GameLevelingChannelOrder::where('trade_no', $data->out_trade_no)->first();
                // 验证订单号
                if (! isset($gameLevelingChannelOrder) || empty($gameLevelingChannelOrder)) {
                    throw new DailianException('订单不存在');
                }
                // 验证app_id
                if ($data->appid != $basicConfig['app_id']) {
                    throw new DailianException('APPID错误');
                }

                $createOrderData = [];
                $createOrderData['game_id'] = $gameLevelingChannelOrder->game_id;
                $createOrderData['game_region_id'] = $gameLevelingChannelOrder->game_region_id;
                $createOrderData['game_server_id'] = $gameLevelingChannelOrder->game_server_id;
                $createOrderData['game_leveling_type_id'] = $gameLevelingChannelOrder->game_leveling_type_id;
                $createOrderData['channel_order_trade_no'] = $gameLevelingChannelOrder->trade_no;
                $createOrderData['amount'] = $gameLevelingChannelOrder->amount;
                $createOrderData['security_deposit'] = $gameLevelingChannelOrder->security_deposit;
                $createOrderData['efficiency_deposit'] = $gameLevelingChannelOrder->efficiency_deposit;
                $createOrderData['title'] = $gameLevelingChannelOrder->title;
                $createOrderData['day'] = $gameLevelingChannelOrder->day;
                $createOrderData['hour'] = $gameLevelingChannelOrder->hour;
                $createOrderData['game_account'] = $gameLevelingChannelOrder->game_account;
                $createOrderData['game_password'] = $gameLevelingChannelOrder->game_password;
                $createOrderData['game_role'] = $gameLevelingChannelOrder->game_role;
                $createOrderData['seller_nick'] = '';
                $createOrderData['buyer_nick'] = '';
                $createOrderData['price_increase_step'] = '';
                $createOrderData['price_ceiling'] = '';
                $createOrderData['user_phone'] = '';
                $createOrderData['user_qq'] = $gameLevelingChannelOrder->user_qq;
                $createOrderData['player_phone'] = $gameLevelingChannelOrder->player_phone;
                $createOrderData['explain'] = $gameLevelingChannelOrder->explain;
                $createOrderData['requirement'] = $gameLevelingChannelOrder->requirement;

                $user = User::find(request('user_id', 2));
                // 下单
                GameLevelingOrder::placeOrder($user, $createOrderData);

                $gameLevelingChannelOrder->status = 1; // （未接单）已支付
                $gameLevelingChannelOrder->payment_type = 2; // 支付渠道
                $gameLevelingChannelOrder->channel_order_trade_no = $gameLevelingChannelOrder->trade_no; // 渠道订单
                $gameLevelingChannelOrder->save();
            } else {
                throw new Exception('订单支付失败');
            }
        } catch (DailianException $e) {
            // 退款逻辑
            DB::rollback();
            myLog('wechat-notify-error', ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            return Response::create('fail');
        } catch (Exception $e) {
            DB::rollback();
            myLog('wechat-notify-error', ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            return Response::create('fail');
        }
        DB::commit();
        return $weChat->success();
    }
}

<?php

namespace App\Http\Controllers\Channel;

use DB;
use Exception;
use EasyWeChat;
use App\Models\User;
use App\Models\Game;
use Yansongda\Pay\Pay;
use EasyWeChat\Factory;
use App\Models\GameServer;
use App\Models\GameRegion;
use Illuminate\Http\Request;
use App\Models\GameLevelingType;
use App\Models\GameLevelingOrder;
use App\Events\NotificationEvent;
use App\Http\Controllers\Controller;
use App\Models\GameLevelingPlatform;
use App\Services\OrderOperateController;
use App\Models\GameLevelingChannelGame;
use App\Models\GameLevelingChannelUser;
use App\Models\GameLevelingChannelOrder;
use App\Models\GameLevelingChannelRefund;
use App\Exceptions\GameLevelingOrderOperateException;

/**
 * 游戏代练渠道订单
 * Class GameLevelingChannelOrderController
 * @package App\Http\Controllers\Frontend\Channel
 */
class GameLevelingChannelOrderController extends Controller
{
    /**
     * 获取所有的代练游戏
     *
     * @return mixed
     */
    public function games()
    {
        try {
            $games = GameLevelingChannelGame::selectRaw('game_name as text, game_id as id, user_id, game_id')
                ->where('user_id', session('user_id'))
                ->with(['game' => function ($query) {
                    $query->select('id', 'icon');
                }])
                ->groupBy('game_id')
                ->get()
                ->map(function ($item, $key) {
                    return collect($item)->except(['user_id', 'game_id']);
                });
        } catch (\Exception $e) {
            return response()->ajax(0, '数据异常' . $e->getMessage());
        }
        return response()->ajax(1, '', $games);
    }

    /**
     * 获取游戏的区
     *
     * @return mixed
     */
    public function regions()
    {
        try {
            $regions = GameRegion::selectRaw('name as text, id')
                ->where('game_id', request('game_id'))
                ->get()
                ->toArray();

            return response()->ajax(1, '', $regions);
        } catch (Exception $e) {
            return response()->ajax(0, '服务器错误');
        }
    }

    /**
     * 获取区的服
     *
     * @param Request $request
     * @return mixed
     */
    public function servers(Request $request)
    {
        try {
            $servers = GameServer::selectRaw('name as text, id')
                ->where('game_region_id', request('region_id'))->get();
            return response()->ajax(1, '', $servers);
        } catch (Exception $e) {
            return response()->ajax(0, '', $e->getMessage());
        }
    }

    /**
     * 获取游戏区与服
     * @return \Illuminate\Http\JsonResponse
     */
    public function regionServer()
    {
        $gameRegionServer = GameRegion::select('id', 'id as value', 'name as label', 'game_id')
            ->with([
                'gameServers' => function ($query) {
                    $query->select('id', 'id as value', 'name as label', 'game_region_id');
                }
            ])
            ->where('game_id', request('game_id'))
            ->get()
            ->toJson();

        // 替换游戏区关联关系名称
        $lastData = str_replace("game_servers", "children", $gameRegionServer);

        return response()->ajax(1, '', json_decode($lastData));
    }

    /**
     * 游戏代练类型
     *
     * @return mixed
     */
    public function gameLevelingTypes()
    {
        try {
            $types = GameLevelingChannelGame::selectRaw('game_leveling_type_name as text, game_leveling_type_id as id, user_id')
                ->where('game_id', request('game_id'))
                ->where('user_id', session('user_id'))
                ->get()
                ->map(function ($item, $key) {
                    return collect($item)->except(['user_id']);
                });
        } catch (\Exception $e) {
            return response()->ajax(0, []);
        }
        return response()->ajax(1, '', $types);
    }

    /**
     * 游戏代练等级
     *
     * @return mixed
     */
    public function gameLevelingLevels()
    {
        try {
            // 渠道游戏
            $gameLevelingChannelGame = GameLevelingChannelGame::where('game_id', request('game_id'))
                ->where('user_id', session('user_id'))
                ->where('game_leveling_type_id', request('game_leveling_type_id'))
                ->first();

            $targets = $gameLevelingChannelGame->gameLevelingChannelPrices->toArray();

            $levels = [];
            foreach ($targets as $key => $item) {
                $nextLevelArr = $gameLevelingChannelGame->gameLevelingChannelPrices->where('id', '>', $item['id'])->toArray();
                $nextLevels = [];
                foreach ($nextLevelArr as $i) {
                    $nextLevels[] = [
                        'text' => $i['level'],
                        'index' => $i['id'],
                    ];
                }
                $levels[] = [
                    'text' => $item['level'],
                    'index' => $item['id'],
                    'level' => $nextLevels
                ];
            }
        } catch (Exception $e) {
            return response()->ajax(0, $e->getMessage());
        }
        return response()->ajax(1, '', $levels);
    }

    /**
     * 自动计算下单价格和时间等信息
     *
     * @return mixed
     */
    public function gameLevelingAmountTime()
    {
        # 获取代练价格时间保证金
        $amountTimeDeposit = GameLevelingChannelOrder::amountTimeDepositCompute(
            session('user_id'),
            request('game_id'),
            request('game_leveling_type_id'),
            request('game_leveling_current_level_id'),
            request('game_leveling_target_level_id'));

        return response()->ajax(1, 'success', [
            'amount' => $amountTimeDeposit->fake_amount,
            'discount_amount' => $amountTimeDeposit->amount,
            'time' => $amountTimeDeposit->show_time,
            'game' => $amountTimeDeposit->game_name . '-' . $amountTimeDeposit->game_leveling_type_name,
            'level' => $amountTimeDeposit->current_level->level . '-' . $amountTimeDeposit->target_level->level,
        ]);
    }

    /**
     * 创建订单并返回支付信息
     *
     * @return \Yansongda\Pay\Gateways\Alipay\WapGateway|\Yansongda\Pay\Gateways\Wechat\WapGateway
     * @throws EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function store()
    {
        #　获取游戏
        $game = Game::find(request('game_id'));

        #　获取区
        $region = GameRegion::find(request('game_region_id'));

        #　获取服
        $server = GameServer::find(request('game_server_id'));

        # 游戏代练类型
        $gameLevelingType = GameLevelingType::find(request('game_leveling_type_id'));

        # 获取代练价格时间保证金
        $amountTimeDeposit = GameLevelingChannelOrder::amountTimeDepositCompute(
            session()->get('user_id'),
            $game->id,
            $gameLevelingType->id,
            request('current_level_id'),
            request('target_level_id'));

        # 创建订单
        $order = GameLevelingChannelOrder::create([
            'trade_no' => generateOrderNo(),
            'user_id' => session('user_id'),
            'user_qq' => $amountTimeDeposit->user_qq,
            'game_id' => $game->id,
            'game_name' => $game->name,
            'game_region_id' => $region->id,
            'game_region_name' => $region->id,
            'game_server_id' => $server->id,
            'game_server_name' => $server->name,
            'game_leveling_type_id' => $gameLevelingType->id,
            'game_leveling_type_name' => $gameLevelingType->name,
            'game_leveling_channel_user_id' => session('channel_user_id'),
            'game_account' => request('game_account'),
            'game_password' => request('game_password'),
            'game_role' => request('game_role'),
            'player_phone' => request('player_phone'),
            'player_qq' => request('player_qq'),
            'payment_type' => request('payment_type'),
            'title' => $game->name . '-' . $gameLevelingType->name . '-' . $amountTimeDeposit->current_level->level . '-' . $amountTimeDeposit->target_level->level,
            'day' => $amountTimeDeposit->day,
            'hour' => $amountTimeDeposit->hour,
            'amount' => $amountTimeDeposit->amount,
            'supply_amount' => $amountTimeDeposit->supply_amount,
            'security_deposit' => $amountTimeDeposit->security_deposit,
            'efficiency_deposit' => $amountTimeDeposit->efficiency_deposit,
            'explain' => $amountTimeDeposit->explain,
            'requirement' => $amountTimeDeposit->requirement,
            'demand' => $amountTimeDeposit->current_level->name . '-' . $amountTimeDeposit->target_level->name,
        ]);

        # 获取支付信息 1 支付宝 2 微信
        if ($order->payment_type == 1) {
            # H5支付
            $payPar = Pay::alipay(config('alipay.base_config'))->wap([
                'out_trade_no' => $order->trade_no,
                'total_amount' => $order->amount,
                'subject' => '代练订单支付',
            ]);
            return response()->ajax(1, 'success', ['type' => 1, 'trade_no' => $order->trade_no, 'par' => $payPar->getContent()]);
        } elseif ($order->payment_type == 2) {
            // 获取授权信息
            $wxAuthInfo = session('wechat.oauth_user.default');

            // 下单
            $app = Factory::payment(config('wechat.payment.default'));
            $result = $app->order->unify([
                'body' => $order->title,
                'detail' => '丸子代练',
                'out_trade_no' => $order->trade_no,
                'total_fee' => bcmul($order->amount, 100, 0),
                'trade_type' => 'JSAPI',
                'openid' => $wxAuthInfo->getId(),
                'notify_url' => route('channel.game-leveling.wx.pay.notify'),
                'return_url' => url('/channel/order/pay/success', ['trade_no' => $order->trade_no]),
            ]);

            $payPar = $app->jssdk->bridgeConfig($result['prepay_id'], false);

            return response()->ajax(1, 'success', ['type' => 2, 'trade_no' => $order->trade_no, 'par' => $payPar]);
        }
    }

    /**
     * @return mixed
     * @throws EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function pcStore()
    {
        #　获取游戏
        $game = Game::find(request('game_id'));

        #　获取区
        $region = GameRegion::find(request('game_region_id'));

        #　获取服
        $server = GameServer::find(request('game_server_id'));

        # 游戏代练类型
        $gameLevelingType = GameLevelingType::find(request('game_leveling_type_id'));

        # 获取代练价格时间保证金
        $amountTimeDeposit = GameLevelingChannelOrder::amountTimeDepositCompute(
            session()->get('user_id'),
            $game->id,
            $gameLevelingType->id,
            request('current_level_id'),
            request('target_level_id'));

        # 创建订单
        $order = GameLevelingChannelOrder::create([
            'trade_no' => generateOrderNo(),
            'user_id' => session('user_id'),
            'user_qq' => $amountTimeDeposit->user_qq,
            'game_id' => $game->id,
            'game_name' => $game->name,
            'game_region_id' => $region->id,
            'game_region_name' => $region->id,
            'game_server_id' => $server->id,
            'game_server_name' => $server->name,
            'game_leveling_type_id' => $gameLevelingType->id,
            'game_leveling_type_name' => $gameLevelingType->name,
            'game_leveling_channel_user_id' => session('channel_user_id'),
            'game_account' => request('game_account'),
            'game_password' => request('game_password'),
            'game_role' => request('game_role'),
            'player_phone' => request('player_phone'),
            'player_qq' => request('player_qq'),
            'payment_type' => request('payment_type'),
            'title' => $game->name . '-' . $gameLevelingType->name . '-' . $amountTimeDeposit->current_level->level . '-' . $amountTimeDeposit->target_level->level,
            'day' => $amountTimeDeposit->day,
            'hour' => $amountTimeDeposit->hour,
            'amount' => $amountTimeDeposit->amount,
            'supply_amount' => $amountTimeDeposit->supply_amount,
            'security_deposit' => $amountTimeDeposit->security_deposit,
            'efficiency_deposit' => $amountTimeDeposit->efficiency_deposit,
            'explain' => $amountTimeDeposit->explain,
            'requirement' => $amountTimeDeposit->requirement,
            'demand' => $amountTimeDeposit->current_level->level . '-' . $amountTimeDeposit->target_level->level,
        ]);

        # 获取支付信息 1 支付宝 2 微信
        if ($order->payment_type == 1) {
            # 支付宝扫码支付
            $pay = Pay::alipay(array_merge(config('alipay.base_config'), [
                'notify_url' => route('game-leveling.alipay.pay.notify'),
            ]))->scan([
                'out_trade_no' => $order->trade_no,
                'total_amount' => $order->amount,
                'subject' => '代练订单支付',
            ]);

            # 生成二维码
            $qr = base64_encode(\QrCode::format('png')
                ->merge('/public/channel-pc/images/alipay_qr.png')
                ->size(200)->errorCorrection('H')
                ->generate($pay['qr_code']));

            return response()->ajax(1, 'success', ['type' => 1, 'trade_no' => $order->trade_no, 'qr' => 'data:image/png;base64,' . $qr]);
        } elseif ($order->payment_type == 2) {
            # 微信扫码支付
            $app = Factory::payment(config('wechat.payment.default'));
            $pay = $app->order->unify([
                'body' => $order->title,
                'detail' => '丸子代练',
                'out_trade_no' => $order->trade_no,
                'total_fee' => bcmul($order->amount, 100, 0),
                'trade_type' => 'NATIVE',
                'product_id' => $order->trade_no, // $message['product_id'] 则为生成二维码时的产品 ID
                'notify_url' => route('channel.game-leveling.wx.pay.notify'),
                'return_url' => url('/channel/order/pay/success', ['trade_no' => $order->trade_no]),
            ]);

            # 生成二维码
            $qr = base64_encode(\QrCode::format('png')
                ->merge('/public/channel-pc/images/wechat_qr.png')
                ->size(200)->errorCorrection('H')
                ->generate($pay['code_url']));

            return response()->ajax(1, 'success', ['type' => 2, 'trade_no' => $order->trade_no, 'qr' => 'data:image/png;base64,' . $qr]);
        }
    }

    /**
     * 微信回调
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws EasyWeChat\Kernel\Exceptions\Exception
     */
    public function weChatNotify()
    {
        $app = Factory::payment(config('wechat.payment.default'));
        $response = $app->handlePaidNotify(function ($message, $fail) {
            // 业务逻辑
            if (is_array($message) && isset($message['result_code']) == 'SUCCESS') {
                $order = GameLevelingChannelOrder::where('trade_no', $message['out_trade_no'])
                    ->where('status', 1)
                    ->first();

                if ($order) {
                    DB::beginTransaction();

                    try {
                        $order->payment_at = date('Y-m-d H:i:s');
                        $order->payment_amount = bcdiv($message['total_fee'], 100, 2);
                        $order->status = 2;
                        $order->save();

                        $user = User::find($order->user_id);
                        $gameLevelingOrder = GameLevelingOrder::placeOrder($user, $order->toArray()); // 下单
                        # 更新渠道订单状态
                        $gameLevelingOrder->channel_order_trade_no = $order->trade_no; // 渠道订单
                        $gameLevelingOrder->channel_order_status = 2; // 渠道订单支付状态
                        $gameLevelingOrder->save();
                    } catch (\Exception $exception) {
                        myLog('weChatNotify', [$exception->getLine(), $exception->getMessage()]);
                        DB::rollback();
                    }

                    DB::commit();
                }
                return true;
            }
            // 错误消息
            $fail('Order not exists');
        });

        return $response;
    }

    /**
     * 获取IP地址
     *
     * @return string
     */
    public static function getIp()
    {
        if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $ip = getenv('REMOTE_ADDR');
        } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return preg_match('/[\d\.]{7,15}/', $ip, $matches) ? $matches [0] : '';
    }

    /**
     * 渠道订单筛选
     *
     * @return mixed
     */
    public function orderList()
    {
        try {
            $gameLevelingChannelOrders = GameLevelingChannelOrder::filter(request()->all())
                ->where('game_leveling_channel_user_id', session('channel_user_id'))
                ->where('user_id', session('user_id'))
                ->oldest('id')
                ->where('status', '!=', 1)
                ->get();
        } catch (Exception $e) {
            myLog('channel-order-list', [$e->getMessage(), $e->getLine()]);
            return response()->ajax(0, '服务器异常!');
        }
        return $gameLevelingChannelOrders;
    }

    /**
     * 完成验收
     *
     * @return mixed
     */
    public function complete()
    {
        DB::beginTransaction();
        try {
            // 当前操作人是否是订单持有者
            $gameLevelingChannelUser = GameLevelingChannelUser::find(session('channel_user_id'));

            if (!$gameLevelingChannelUser) {
                throw new GameLevelingOrderOperateException('当前操作人不是该订单持有人!');
            }

            // 渠道表状态更新
            $gameLevelingChannelOrder = GameLevelingChannelOrder::where('trade_no', request('trade_no'))
                ->where('status', 3)
                ->where('user_id', session('user_id'))
                ->first();

            $gameLevelingChannelOrder->status = 4;
            $gameLevelingChannelOrder->save();
        } catch (Exception $e) {
            DB::rollback();
            myLog('channel-complete', [$e->getMessage(), $e->getLine()]);;
            return response()->ajax(0, '订单异常！');
        }
        DB::commit();
        return response()->ajax(1, '操作成功!');
    }

    /**
     * 取消退款
     *
     * @return mixed
     */
    public function cancelRefund()
    {
        DB::beginTransaction();
        try {
            // 当前操作人是否是订单持有者
            $gameLevelingChannelUser = GameLevelingChannelUser::where('id', session('channel_user_id'))
                ->where('user_id', session('user_id'))
                ->first();

            if (!$gameLevelingChannelUser) {
                throw new GameLevelingOrderOperateException('当前操作人不是该订单持有人!');
            }

            // 渠道表状态更新
            $gameLevelingChannelOrder = GameLevelingChannelOrder::where('trade_no', request('trade_no'))
                ->where('status', 5)
                ->where('user_id', session('user_id'))
                ->first();

            $gameLevelingChannelOrder->status = 2;
            $gameLevelingChannelOrder->save();

            // 申请退款表状态更新
            GameLevelingChannelRefund::where('game_leveling_channel_order_trade_no', request('trade_no'))
                ->where('status', 1)
                ->update(['status' => 4]);

            // 发单平台表状态更新
            GameLevelingOrder::where('channel_order_trade_no', request('trade_no'))
                ->where('channel_order_status', 5)
                ->where('user_id', session('user_id'))
                ->update(['channel_order_status' => 2]);

            // 取消成功之后，上架第三方平台的订单
            if ($order = GameLevelingOrder::where('channel_order_trade_no', request('trade_no'))->where('status', 22)->latest('id')->first()) {
                OrderOperateController::init(User::find($order->user_id), $order)->onSale();

                // 该订单下单成功的接单平台
                $gameLevelingPlatforms = GameLevelingPlatform::where('game_leveling_order_trade_no', $order->trade_no)
                    ->get();

                // 下单成功的接单平台
                if ($gameLevelingPlatforms->count() > 0) {
                    foreach ($gameLevelingPlatforms as $gameLevelingPlatform) {
                        call_user_func_array([config('gameleveling.controller')[$gameLevelingPlatform->platform_id], config('gameleveling.action')['onSale']], [$order]);
                    }
                }
            }
        } catch (GameLevelingOrderOperateException $e) {
            myLog('channel-cancel-refund-error', ['trade_no' => $gameLevelingChannelOrder->trade_no ?? '', 'message' => $e->getMessage(), 'line' => $e->getLine()]);
            DB::rollback();
            return response()->ajax(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            myLog('channel-cancel-refund-error', [$e->getMessage(), $e->getLine()]);
            return response()->ajax(0, '订单异常！');
        }
        DB::commit();
        return response()->ajax(1, '操作成功!');
    }

    /**
     * 申请退款
     *
     * @return mixed
     */
    public function applyRefund()
    {
        DB::beginTransaction();
        try {
            // 获取渠道订单
            $gameLevelingChannelOrder = GameLevelingChannelOrder::where('trade_no', request('trade_no'))
                ->where('status', 2)
                ->where('user_id', session('user_id'))
                ->first();

            if (!$gameLevelingChannelOrder) {
                return response()->ajax(0, '订单不存在!');
            }

            if (!request('refund_reason')) {
                return response()->ajax(0, '请输入退款原因!');
            }

            if (!request('type')) {
                return response()->ajax(0, '请选择退款类型!');
            }

            if (request('type') == 2 && !request('refund_amount')) {
                return response()->ajax(0, '请填写退款金额!');
            }

            if (request('type') == 2 && !is_numeric(request('refund_amount'))) {
                return response()->ajax(0, '退款金额必须填写数字值!');
            }

            if (request('type') == 2 && request('refund_amount') < 0.01) {
                return response()->ajax(0, '退款金额必须大于一分钱!');
            }

            if (request('type') == 2 && request('refund_amount') && $gameLevelingChannelOrder->payment_amount < request('refund_amount')) {
                return response()->ajax(0, '申请退款金额不得大于订单实际支付金额!');
            }
            $gameLevelingChannelOrder->status = 5;
            $gameLevelingChannelOrder->save();

            // 图片
            $pic1 = '';
            $pic2 = '';
            $pic3 = '';
            if (request('images') && is_array(request('images'))) {
                foreach (request('images') as $key => $image) {
                    if ($key === 0) {
                        $pic1 = base64ToImg($image, 'apply-refund');
                    } elseif ($key === 1) {
                        $pic2 = base64ToImg($image, 'apply-refund');
                    } elseif ($key === 2) {
                        $pic3 = base64ToImg($image, 'apply-refund');
                    }
                }
            }
            // 申请退款
            $data['game_leveling_channel_order_trade_no'] = $gameLevelingChannelOrder->trade_no;
            $data['game_leveling_type_id'] = $gameLevelingChannelOrder->game_leveling_type_id;
            $data['game_leveling_type_name'] = $gameLevelingChannelOrder->game_leveling_type_name;
            $data['day'] = $gameLevelingChannelOrder->day;
            $data['hour'] = $gameLevelingChannelOrder->hour;
            $data['type'] = request('type');
            $data['payment_type'] = $gameLevelingChannelOrder->payment_type;
            $data['status'] = 1;
            $data['amount'] = $gameLevelingChannelOrder->amount;
            $data['payment_amount'] = $gameLevelingChannelOrder->payment_amount;
            $data['refund_amount'] = request('type') == 1 ? $gameLevelingChannelOrder->payment_amount : request('refund_amount');
            $data['pic1'] = $pic1;
            $data['pic2'] = $pic2;
            $data['pic3'] = $pic3;
            $data['refund_reason'] = request('refund_reason');
            $data['refuse_refund_reason'] = '';

            GameLevelingChannelRefund::create($data);

            // 发单平台表渠道订单状态更新
            GameLevelingOrder::where('channel_order_trade_no', request('trade_no'))
                ->where('channel_order_status', 2)
                ->where('user_id', session('user_id'))
                ->update(['channel_order_status' => 5]);

            // 申请成功之后，下架第三方平台的订单
            if ($order = GameLevelingOrder::where('channel_order_trade_no', request('trade_no'))->where('status', 1)->latest('id')->first()) {
                OrderOperateController::init(User::find($order->user_id), $order)->offSale();

                // 该订单下单成功的接单平台
                $gameLevelingPlatforms = GameLevelingPlatform::where('game_leveling_order_trade_no', $order->trade_no)
                    ->get();

                // 下单成功的接单平台
                if ($gameLevelingPlatforms->count() > 0) {
                    foreach ($gameLevelingPlatforms as $gameLevelingPlatform) {
                        call_user_func_array([config('gameleveling.controller')[$gameLevelingPlatform->platform_id], config('gameleveling.action')['offSale']], [$order]);
                    }
                }
            }
        } catch (GameLevelingOrderOperateException $e) {
            myLog('channel-apply-refund-error', ['trade_no' => $gameLevelingChannelOrder->trade_no ?? '', 'message' => $e->getMessage(), 'line' => $e->getLine()]);
            DB::rollback();
            return response()->ajax(0, $e->getMessage());
        } catch (Exception $e) {
            myLog('channel-apply-refund-error', ['trade_no' => $gameLevelingChannelOrder->trade_no ?? '', 'message' => $e->getMessage(), 'line' => $e->getLine()]);
            DB::rollback();
            return response()->ajax(0, '操作失败：服务器异常!');
        }
        DB::commit();
        return response()->ajax(1, '操作成功!');
    }

    /**
     * 订单详情
     *
     * @return mixed
     */
    public function show()
    {
        try {
            return GameLevelingChannelOrder::where('trade_no', request('trade_no'))
                ->where('user_id', session('user_id'))
                ->where('game_leveling_channel_user_id', session('channel_user_id'))
                ->with(['gameLevelingChannelRefund' => function($query) {
                    $query->select('game_leveling_channel_order_trade_no',
                        'created_at',
                        'updated_at',
                        'status',
                        'refund_reason',
                        'refuse_refund_reason',
                        'status',
                        'pic1',
                        'pic2',
                        'pic3'
                    )
                        ->orderBy('id', 'desc');
                }])
                ->first();
        } catch (Exception $e) {
            myLog('channel-order-show-error', [$e->getMessage(), $e->getLine()]);
        }
    }

    public function pcWeChatNotify()
    {
        myLog('pc-wecaht', [request()->all()]);
    }

    /**
     * pc 扫码支付回调
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function pcAliPayNotify()
    {
        $alipay = Pay::alipay(config('alipay.base_config'));

        try{
            $data = $alipay->verify();

            myLog('alipay', [$data]);

            # 支付宝确认交易成功
            if (in_array($data->trade_status,  ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
                // 查找 订单
                $order = GameLevelingChannelOrder::where('trade_no', $data->out_trade_no)
                    ->where('amount', $data->total_amount)
                    ->where('status', 1)
                    ->first();

                # 查到充值订单
                if ($order) {
                    DB::beginTransaction();

                    try {
                        $order->payment_at = date('Y-m-d H:i:s');
                        $order->payment_amount = $data->total_amount;
                        $order->status = 2;
                        $order->save();

                        $user = User::find($order->user_id);
                        // 下单
                        $gameLevelingOrder = GameLevelingOrder::placeOrder($user, array_merge($order->toArray(), [
                            'source_amount' => $order->amount,
                            'amount' => $order->supply_amount]));
                        # 更新渠道订单状态
                        $gameLevelingOrder->channel_order_trade_no = $order->trade_no; // 渠道订单
                        $gameLevelingOrder->channel_order_status = 2; // 渠道订单支付状态
                        $gameLevelingOrder->save();
                    } catch (\Exception $exception) {
                        myLog('alipayNotify', [$exception->getLine(), $exception->getMessage()]);
                        DB::rollback();
                    }

                    DB::commit();

                    # 发送通知
                    event((new NotificationEvent('channelPcPayResult', [
                        'message' => '充值成功',
                    ])));
                }
            }
        } catch (Exception $e) {
            \Log::debug('Alipay notify Error', [$e->getMessage()]);
        }

        return $alipay->success();
    }
}

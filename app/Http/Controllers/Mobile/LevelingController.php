<?php

namespace App\Http\Controllers\Mobile;

use DB;
use Agent;
use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Game;
use App\Models\Order;
use Order as OrderFacade;
use App\Extensions\Order\Operations\CreateLeveling;
use App\Models\OrderDetail;
use Yansongda\Pay\Pay;
use App\Models\MobileOrder;
use App\Models\GoodsTemplate;
use App\Services\RedisConnect;
use App\Models\LevelingConfigure;
use App\Models\GoodsTemplateWidget;
use App\Models\GoodsTemplateWidgetValue;
use App\Models\LevelingPriceConfigure;
use App\Models\LevelingRebateConfigure;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LevelingController extends Controller
{
	/**
	 * 获取需求
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
    public function demand(Request $request)
    {
        // if (Agent::isMobile()) {
        	

            return view('mobile.leveling.demand', compact('games'));
        // } else {
        //     abort(404);
        // }
    }

    /**
     * 获取所有的游戏
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function games(Request $request)
    {
        // $games = GoodsTemplate::where('goods_templates.status', 1)
        //     ->where('goods_templates.service_id', 4)
        //     ->leftJoin('games', 'games.id', '=', 'goods_templates.game_id')
        //     ->pluck('games.name');
        
        $games = LevelingConfigure::pluck('game_name')->unique();

        if (isset($games) && ! empty($games)) {
            $games = $games->toArray();
        } else {
            $games = [];
        }

        return response()->ajax(1, $games);
    }

     /**
     * 获取游戏对应的类型
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function types(Request $request)
    {
    	$gameId = 0;
        $gameName = $request->game_name;
        if (isset($gameName) && ! empty($gameName)) {
            $gameId = Game::where('name', $gameName)->value('id');
        }
        // dd($gameId);
    	// 代练类型
    	// $types = DB::select("
     //        SELECT a.field_value as name FROM goods_template_widget_values a
     //        LEFT JOIN goods_template_widgets b
     //        ON a.goods_template_widget_id=b.id
     //        WHERE a.field_name='game_leveling_type' 
     //        AND a.goods_template_widget_id=
     //            (SELECT id FROM goods_template_widgets WHERE goods_template_id=
     //                (SELECT id FROM goods_templates WHERE game_id='$gameId' AND service_id=4 AND STATUS=1 LIMIT 1)
     //            AND field_name='game_leveling_type' LIMIT 1)
     //    ");

    	// $arr = [];
     //    if (isset($types) && ! empty($types)) {
     //    	foreach ($types as $type) {
     //    		$arr[] = $type->name;
     //    	}
     //    	$types = $arr;
     //    } else {
     //    	$types = [];
     //    }
    // dd($types);

        $types = LevelingConfigure::pluck('game_leveling_type')->unique();

        if (isset($types) && ! empty($types)) {
            $types = $types->toArray();
        } else {
            $types = [];
        }
    	return response()->ajax(1, $types);
    }

    /**
     * 获取代练目标（青铜-黄金）
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function targets(Request $request)
    {
    	$gameName = $request->game_name;
    	$type = $request->type;
    	// 代练目标
    	$targets = LevelingPriceConfigure::where('game_name', $gameName)
    		->where('game_leveling_type', $type)
    		->pluck('game_leveling_level', 'game_leveling_number');

        if (isset($targets) && ! empty($targets)) {
            $targets = $targets->toArray();
        } else {
            $targets = [];
        }

        return response()->ajax(1, $targets);
    }

    /**
     * 获取代练价格和时间
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function compute(Request $request)
    {
    	$gameName = $request->game_name;
    	$type = $request->type;
        $level = $request->level;

        if (empty($gameName) || empty($type) || empty($level)) {
            return response()->ajax(0, '参数缺失');
        }

        $arrTarget = explode(',', $level);

        if (! is_array($arrTarget) || count($arrTarget) < 2) {
            return response()->ajax(0, '请正确选择代练目标');
        }
        $startLevel = $arrTarget[0];
    	$endLevel = $arrTarget[1];

        // 找到层级
        $startNumber = LevelingPriceConfigure::where('game_name', $gameName)
            ->where('game_leveling_type', $type)
            ->where('game_leveling_level', $startLevel)
            ->value('game_leveling_number');

        $endNumber = LevelingPriceConfigure::where('game_name', $gameName)
            ->where('game_leveling_type', $type)
            ->where('game_leveling_level', $endLevel)
            ->value('game_leveling_number');

        if (empty($startNumber) || empty($endNumber)) {
            return response()->ajax(0, '请正确选择代练目标');
        }

        if ($startNumber >= $endNumber) {
            return response()->ajax(0, '请正确选择代练目标');
        }

    	$price = LevelingPriceConfigure::where('game_name', $gameName)
    		->where('game_leveling_type', $type)
    		->where('game_leveling_number', '>=', $startNumber)
            ->where('game_leveling_number', '<', $endNumber)
    		->sum('level_price');

    	$time = LevelingPriceConfigure::where('game_name', $gameName)
    		->where('game_leveling_type', $type)
    		->where('game_leveling_number', '>=', $startNumber)
            ->where('game_leveling_number', '<', $endNumber)
    		->sum('level_hour');

    	$count = LevelingPriceConfigure::where('game_name', $gameName)
    		->where('game_leveling_type', $type)
    		->where('game_leveling_number', '>=', $startNumber)
            ->where('game_leveling_number', '<', $endNumber)
    		->count();

    	$rebate = LevelingRebateConfigure::where('game_name', $gameName)
    		->where('game_leveling_type', $type)
    		->where('level_count', '<=', $count)
            ->min('rebate') ?? 100;
        // dd($rebate);
    	$staticRebate = LevelingConfigure::where('game_name', $gameName)
    		->where('game_leveling_type', $type)
    		->value('rebate') ?? 100;

        $securityDeposit = LevelingPriceConfigure::where('game_name', $gameName)
            ->where('game_leveling_type', $type)
            ->where('game_leveling_number', '>=', $startNumber)
            ->where('game_leveling_number', '<', $endNumber)
            ->sum('level_security_deposit');

        $efficiencyDeposit = LevelingPriceConfigure::where('game_name', $gameName)
            ->where('game_leveling_type', $type)
            ->where('game_leveling_number', '>=', $startNumber)
            ->where('game_leveling_number', '<', $endNumber)
            ->sum('level_efficiency_deposit');

        $day = intval(bcdiv($time, 60, 0))+0;
        $hour = intval($time%60)+0;
    	
        $payment = bcmul($price, $rebate*0.01, 2)+0;// 玩家支付价格
        if ($price*$rebate*0.01 > 0 && $price*$rebate*0.01 < 0.01) {
            $payment = 0.01;
        }
        $showPrice = bcmul($payment, 1.5, 2)+0;// 展示的优惠前价格
        if($showPrice > 0 && $showPrice < 0.01) {
            $showPrice = 0.01;
        }
    	
    	$price = bcmul($payment, $staticRebate*0.01, 2)+0;// 发单平台价格
        if($payment*$staticRebate*0.01 > 0 && $payment*$staticRebate*0.01 < 0.01) {
            $price = 0.01;
        }
    	$showTime = sec2Time($time*3600);// 需要代练的时间
        // dd($day, $hour, $securityDeposit, $efficiencyDeposit);
        return response()->ajax(1, ['showPrice' => $showPrice, 'payment' => $payment, 'price' => $price, 'showTime' => $showTime, 'startNumber' =>$startNumber, 'endNumber' => $endNumber, 'startLevel' => $startLevel, 'endLevel' => $endLevel, 'time' => $time, 'securityDeposit' => $securityDeposit, 'efficiencyDeposit' => $efficiencyDeposit,
            'day' => $day, 'hour' => $hour
        ]);
    }

    /**
     * 获取区
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function regions(Request $request)
    {
    	$gameName = $request->game_name ?? '';
        $gameId = Game::where('name', $gameName)->value('id');

        $templateId = GoodsTemplate::where('game_id', $gameId)->where('service_id', 4)->value('id'); //模板id

        $areaIds = GoodsTemplateWidget::where('goods_template_id', $templateId)
            ->where('field_name', 'region')
            ->pluck('id');

        $areas = GoodsTemplateWidgetValue::whereIn('goods_template_widget_id', $areaIds)
            ->where('field_name', 'region')
            ->pluck('field_value');

        if (! empty($areas)) {
            $areas = $areas->toArray();
        } else {
            $areas = [];
        }

        return response()->ajax(1, $areas);
    }

    /**
     * 获取服
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function servers(Request $request)
    {
        $gameName = $request->game_name ?? '';

        $gameId = Game::where('name', $gameName)->value('id');
        $region = $request->region;

        $templateId = GoodsTemplate::where('game_id', $gameId)->where('service_id', 4)->value('id'); //模板id

        $areaId = GoodsTemplateWidget::where('goods_template_id', $templateId)
            ->where('field_name', 'region')
            ->value('id');

        $goodsTemplateWidgetValueId = GoodsTemplateWidgetValue::where('goods_template_widget_id', $areaId)
            ->where('field_name', 'region')
            ->where('field_value', $region)
            ->value('id');

        $serverId = GoodsTemplateWidget::where('goods_template_id', $templateId)
            ->where('field_name', 'serve')
            ->value('id');

        $servers = GoodsTemplateWidgetValue::where('goods_template_widget_id', $serverId)
            ->where('field_name', 'serve')
            ->where('parent_id', $goodsTemplateWidgetValueId)
            ->pluck('field_value');

        if (! empty($servers)) {
            $servers = $servers->toArray();
        } else {
            $servers = [];
        }
        // dd($gameName, $gameId, $region, $areaId, $goodsTemplateWidgetValueId, $servers);
        return response()->ajax(1, $servers);
    }

    /**
     * 跳转到详细信息界面
     * @return [type] [description]
     */
    public function go(Request $request)
    {
        $gameName = $request->gameName;
        $type = $request->type;
        $price = $request->price;
        $payment = $request->payment;
        $time = $request->time;
        $startNumber = $request->startNumber;
        $endNumber = $request->endNumber;
        $startLevel = $request->startLevel;
        $endLevel = $request->endLevel;

        $securityDeposit = $request->securityDeposit;
        $efficiencyDeposit = $request->efficiencyDeposit;
        $day = $request->day;
        $hour = $request->hour;

        if (empty($gameName) || empty($type) || empty($price) || empty($payment) || empty($time) || empty($startNumber) || empty($endNumber) || empty($startLevel) || empty($endLevel)) {
            return response()->ajax(0, '请先完善填写信息');
        }
        return redirect(route('mobile.leveling.place-order'))->with(compact('gameName', 'type', 'price', 'payment', 'time', 'startLevel', 'endLevel', 'startNumber', 'endNumber', 'securityDeposit', 'efficiencyDeposit', 'day', 'hour'));
    }

     /**
     * 下单界面
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function placeOrder(Request $request)
    {
        $data = $request->all();
        // dd($data);
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

        return view('mobile.leveling.place-order', compact('gameName', 'type', 'price', 'payment', 'time', 'showPrice', 'showTime', 'data', 'startLevel', 'endLevel', 'securityDeposit', 'efficiencyDeposit', 'day', 'hour'));
    }

    /**
     * 去支付
     * @param  [type] $no [description]
     * @return [type]     [description]
     */
    public static function pay(Request $request)
    {
        // $aliAppid = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA7Dq65aKIvY/hPxPBt+14lWv3Bt8GJMifOlqRnGb78Mx5oMv17o9iC7JLNV0HodrIsBYVpdJQoQiQ6rRe9wIdAV0OXUIir3fQxKo6whvCyGecnDQGYXpac7zBMFaoVa256q/uSTidFJIFnLvajJuRXwWZBYXujy+mZ9AUZbmNax8RDytqKEv6E+AUuPrwNMW7BkI4f67j0BEgc4qhas347fnQ1yxQ0bMp9C9NuKboWlFJ+bVz6SImdVcfwBnLQ+DyILwPcSiVoNySskJx6XyKAYzCDd04+dXHzuerAGBFp88gbOM+mTqgPOMYkvW2LhQdqkAw+btLJ+gdHcK8xUuXqQIDAQAB';

        // $aesSecret = 'fZkOgJ6sbOFn439F/6cSDg==';
        try {
            $data = $request->all();
            $gameId = Game::where('name', $data['game_name'])->value('id');

            $levelingConfigure = LevelingConfigure::where('game_name', $data['game_name'])
                ->where('game_leveling_type', $data['game_leveling_type'])
                ->first();

            if ($data['channel'] == 1) {
                $data['no'] = 'XY'.generateOrderNo();
            } else {
                $data['no'] = 'XY'.generateOrderNo();
            }

            // 创建订单
            $data['game_id'] = $gameId;
            $data['status'] = 0; // 未支付
            $data['original_price'] = $data['payment']+0;
            $data['creator_user_id'] = 84573;
            $data['creator_user_name'] = User::find(84573)->useranme;
            $data['client_qq'] = '';
            $data['user_qq'] = $levelingConfigure->user_qq;
            $data['out_trade_no'] = '';
            $data['game_leveling_requirements'] = $levelingConfigure->game_leveling_requirements;
            $data['game_leveling_instructions'] = $levelingConfigure->game_leveling_instructions;
            $data['game_leveling_title'] = $data['game_name'].'-'.$data['game_leveling_type'].'-'.$data['startLevel'].'-'.$data['endLevel'];
            $data['price'] = $data['price']+0;
            $data['security_deposit'] = $data['security_deposit']+0;
            $data['efficiency_deposit'] = $data['efficiency_deposit']+0;
            $res = MobileOrder::create($data);

            if ($res && isset($data['pay_type'])) {
                if ($data['pay_type'] == 1) {
                    $orderConfig = [
                        'out_trade_no' => $data['no'],
                        'total_amount' => $data['original_price'],
                        'subject'      => '代练订单支付',
                    ];

                    $basicConfig = [
                        'app_id'         => config('alipay.app_id'),
                        'notify_url'     => route('mobile.leveling.alipay.notify'),
                        'return_url'     => route('mobile.leveling.alipay.return'),
                        'ali_public_key' => config('alipay.alipay_public_key'),
                        'private_key'    => config('alipay.merchant_private_key'),
                        'log'            => [
                            'file'       => './logs/alipay.log',
                            'level'      => 'debug',
                        ],
                        'mode'           => 'dev',
                    ];

                    $alipay = Pay::alipay($basicConfig)->wap($orderConfig);
                    // dd($alipay);
                    // return $alipay->send();
                    return $alipay;
                } elseif ($data['pay_type'] == 2) {
                    $orderConfig = [
                        'out_trade_no' => $data['no'],
                        'total_amount' => $data['original_price'],
                        'subject'      => '代练订单支付',
                    ];

                    $basicConfig = [
                        'app_id'         => config('wechat.app_id'),
                        'notify_url'     => route('mobile.leveling.notify'),
                        'return_url'     => route('mobile.leveling.return'),
                        'ali_public_key' => config('wechat.wechat_public_key'),
                        'private_key'    => config('wechat.merchant_private_key'),
                        'log'            => [
                            'file'       => './logs/wechat.log',
                            'level'      => 'debug',
                        ],
                        'mode'           => 'dev',
                    ];

                    $wechat = Pay::wechat($basicConfig)->wap($orderConfig);
                    // dd($wechat);
                    // return $wechat->send();
                    return $wechat;
                }
            } else {
                return back()->with(['miss' => '请填写完成的代练信息']);
            }
        } catch (Exception $e) {
            myLog('pay-error', ['message' => $e->getMessage(), 'no' => $data['no'] ?? '']);
            return back()->with(['miss' => '请填写完成的代练信息']);
        }
    }

    /**
     * 同步回调
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function alipayReturn(Request $request)
    {
        try {
            $basicConfig = [
                'app_id'         => config('alipay.app_id'),
                'notify_url'     => route('mobile.leveling.alipay.notify'),
                'return_url'     => route('mobile.leveling.alipay.return'),
                'ali_public_key' => config('alipay.alipay_public_key'),
                'private_key'    => config('alipay.merchant_private_key'),
                'log'            => [
                    'file'       => './logs/alipay.log',
                    'level'      => 'debug',
                ],
                'mode'           => 'dev',
            ];

            $data = Pay::alipay($basicConfig)->verify();

            $mobileOrder = MobileOrder::where('no', $data->out_trade_no)->first();
            if ($data) {
                return view('mobile.leveling.success', compact('mobileOrder'));
            } else {
                return view('mobile.leveling.demand');
            }
        } catch (Exception $e) {
            myLog('alipay-return-error', ['message' => $e->getMessage()]);
            return view('mobile.leveling.demand');
        }
    }

    /**
     * 异步通知
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function alipayNotify(Request $request)
    {
        DB::beginTransaction();
        try {
            $basicConfig = [
                'app_id'         => config('alipay.app_id'),
                'notify_url'     => route('mobile.leveling.alipay.notify'),
                'return_url'     => route('mobile.leveling.alipay.return'),
                'ali_public_key' => config('alipay.alipay_public_key'),
                'private_key'    => config('alipay.merchant_private_key'),
                'log'            => [
                    'file'       => './logs/alipay.log',
                    'level'      => 'debug',
                ],
                'mode'           => 'dev',
            ];
            $alipay = Pay::alipay($basicConfig);

            $data = $alipay->verify(); // 是的，验签就这么简单！

            // 成功更新订单状态
            if (isset($data) && ($data->trade_status == 'TRADE_SUCCESS' || $data->trade_status == 'TRADE_FINISHED')) {
                $mobileOrder = MobileOrder::where('no', $data->out_trade_no)->first();
                // 验证订单号
                if (! isset($mobileOrder) || empty($mobileOrder)) {
                    throw new Exception('订单号错误');
                }

                // 验证appid
                if ($data->app_id != $basicConfig['app_id']) {
                    throw new Exception('APPID错误');
                }

                // 验证价格
                if ($data->total_amount != $mobileOrder->original_price) {
                    throw new Exception('支付价格不一致');
                }

                // 创建 detail
                $goodsTemplateId = GoodsTemplate::where('service_id', 4)
                    ->where('status', 1)
                    ->where('game_id', $mobileOrder->game_id)
                    ->value('id');

                $orderDetailArr = [
                    'order_no'                            => '',
                    "source_order_no"                     => '',
                    "client_wang_wang"                    => '',
                    "seller_nick"                         => '',
                    "game_id"                             => $mobileOrder->game_id,
                    "region"                              => $mobileOrder->region,
                    "serve"                               => $mobileOrder->server,
                    "role"                                => $mobileOrder->role,
                    "account"                             => $mobileOrder->account,
                    "password"                            => $mobileOrder->password,
                    "game_leveling_type"                  => $mobileOrder->game_leveling_type,
                    "game_leveling_title"                 => $mobileOrder->game_leveling_title,
                    "game_leveling_requirements_template" => "",
                    "game_leveling_instructions"          => $mobileOrder->game_leveling_instructions,
                    "game_leveling_requirements"          => $mobileOrder->game_leveling_requirements,
                    "game_leveling_amount"                => $mobileOrder->price,
                    "security_deposit"                    => $mobileOrder->security_deposit,
                    "efficiency_deposit"                  => $mobileOrder->efficiency_deposit,
                    "game_leveling_day"                   => $mobileOrder->game_leveling_day,
                    "game_leveling_hour"                  => $mobileOrder->game_leveling_hour,
                    "client_phone"                        => $mobileOrder->client_phone,
                    "user_qq"                             => $mobileOrder->user_qq,
                    "markup_range"                        => '',
                    "markup_top_limit"                    => '',
                    "order_password"                      => '',
                    "source_order_no_1"                   => '',
                    "source_order_no_2"                   => '',
                    "source_price"                        => $mobileOrder->original_price,
                    "customer_service_remark"             => '',
                    "urgent_order"                        => 0,
                    "customer_service_name"               => "",
                    "order_source"                        => "",
                ];
                $order = OrderFacade::handle(new CreateLeveling($mobileOrder->game_id, $goodsTemplateId, $mobileOrder->creator_user_id, '', $mobileOrder->price, $mobileOrder->original_price, $orderDetailArr, $mobileOrder->remark, 7));

                $mobileOrder->out_trade_no = $order->no;
                $mobileOrder->status = 1; // （未接单）已支付
                $mobileOrder->save();

                myLog('alipay-notify-data', ['data' => $data, $goodsTemplateId]);

                // echo 'success';
            } else {
                throw new Exception('订单状态不正确');
            }
        } catch (Exception $e) {
            DB::rollback();
            myLog('alipay-notify-error', ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);

            // echo 'fail';
        }
        DB::commit();
        return $alipay->success();
    }

    /**
     * 详情页
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function show(Request $request)
    {
        $mobileOrder = MobileOrder::find($request->id);

        $order = Order::where('no', $mobileOrder->out_trade_no)->first();

        $orderDetail = OrderDetail::where('order_no', $order->no)->pluck('field_value', 'field_name')->toArray();

        return view('mobile.leveling.show', compact('mobileOrder', 'order', 'orderDetail'));
    }


        /**
     * 同步回调
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function wecahtReturn(Request $request)
    {
        try {
            $basicConfig = [
                'app_id'         => config('wechat.app_id'),
                'notify_url'     => route('mobile.leveling.wechat.notify'),
                'return_url'     => route('mobile.leveling.wechat.return'),
                'ali_public_key' => config('wechat.wechat_public_key'),
                'private_key'    => config('wechat.merchant_private_key'),
                'log'            => [
                    'file'       => './logs/wechat.log',
                    'level'      => 'debug',
                ],
                'mode'           => 'dev',
            ];

            $data = Pay::wechat($basicConfig)->verify();

            $mobileOrder = MobileOrder::where('no', $data->out_trade_no)->first();
            if ($data) {
                return view('mobile.leveling.success', compact('mobileOrder'));
            } else {
                return view('mobile.leveling.demand');
            }
        } catch (Exception $e) {
            myLog('wechat-return-error', ['message' => $e->getMessage()]);
            return view('mobile.leveling.demand');
        }
    }

    /**
     * 异步通知
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function wechatNotify(Request $request)
    {
        DB::beginTransaction();
        try {
            $basicConfig = [
                'app_id'         => config('wechat.app_id'),
                'notify_url'     => route('mobile.leveling.wechat.notify'),
                'return_url'     => route('mobile.leveling.wechat.return'),
                'ali_public_key' => config('wechat.wechat_public_key'),
                'private_key'    => config('wechat.merchant_private_key'),
                'log'            => [
                    'file'       => './logs/wechat.log',
                    'level'      => 'debug',
                ],
                'mode'           => 'dev',
            ];
            $wechat = Pay::wechat($basicConfig);

            $data = $wechat->verify(); // 是的，验签就这么简单！

            // 成功更新订单状态
            if (isset($data) && ($data->trade_status == 'TRADE_SUCCESS' || $data->trade_status == 'TRADE_FINISHED')) {
                $mobileOrder = MobileOrder::where('no', $data->out_trade_no)->first();
                // 验证订单号
                if (! isset($mobileOrder) || empty($mobileOrder)) {
                    throw new Exception('订单号错误');
                }

                // 验证appid
                if ($data->app_id != $basicConfig['app_id']) {
                    throw new Exception('APPID错误');
                }

                // 验证价格
                if ($data->total_amount != $mobileOrder->original_price) {
                    throw new Exception('支付价格不一致');
                }

                // 创建 detail
                $goodsTemplateId = GoodsTemplate::where('service_id', 4)
                    ->where('status', 1)
                    ->where('game_id', $mobileOrder->game_id)
                    ->value('id');

                $orderDetailArr = [
                    'order_no'                            => '',
                    "source_order_no"                     => '',
                    "client_wang_wang"                    => '',
                    "seller_nick"                         => '',
                    "game_id"                             => $mobileOrder->game_id,
                    "region"                              => $mobileOrder->region,
                    "serve"                               => $mobileOrder->server,
                    "role"                                => $mobileOrder->role,
                    "account"                             => $mobileOrder->account,
                    "password"                            => $mobileOrder->password,
                    "game_leveling_type"                  => $mobileOrder->game_leveling_type,
                    "game_leveling_title"                 => $mobileOrder->game_leveling_title,
                    "game_leveling_requirements_template" => "",
                    "game_leveling_instructions"          => $mobileOrder->game_leveling_instructions,
                    "game_leveling_requirements"          => $mobileOrder->game_leveling_requirements,
                    "game_leveling_amount"                => $mobileOrder->price,
                    "security_deposit"                    => $mobileOrder->security_deposit,
                    "efficiency_deposit"                  => $mobileOrder->efficiency_deposit,
                    "game_leveling_day"                   => $mobileOrder->game_leveling_day,
                    "game_leveling_hour"                  => $mobileOrder->game_leveling_hour,
                    "client_phone"                        => $mobileOrder->client_phone,
                    "user_qq"                             => $mobileOrder->user_qq,
                    "markup_range"                        => '',
                    "markup_top_limit"                    => '',
                    "order_password"                      => '',
                    "source_order_no_1"                   => '',
                    "source_order_no_2"                   => '',
                    "source_price"                        => $mobileOrder->original_price,
                    "customer_service_remark"             => '',
                    "urgent_order"                        => 0,
                    "customer_service_name"               => "",
                    "order_source"                        => "",
                ];
                $order = OrderFacade::handle(new CreateLeveling($mobileOrder->game_id, $goodsTemplateId, $mobileOrder->creator_user_id, '', $mobileOrder->price, $mobileOrder->original_price, $orderDetailArr, $mobileOrder->remark, 7));

                $mobileOrder->out_trade_no = $order->no;
                $mobileOrder->status = 1; // （未接单）已支付
                $mobileOrder->save();

                myLog('wechat-notify-data', ['data' => $data, $goodsTemplateId]);

                // echo 'success';
            } else {
                throw new Exception('订单状态不正确');
            }
        } catch (Exception $e) {
            DB::rollback();
            myLog('wechat-notify-error', ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);

            // echo 'fail';
        }
        DB::commit();
        return $wechat->success();
    }
}

<?php

namespace App\Http\Controllers\Mobile;

use DB;
use Agent;
use Exception;
use Carbon\Carbon;
use App\Models\Game;
use App\Models\Order;
use App\Models\GoodsTemplate;
use App\Models\GoodsTemplateWidget;
use App\Models\GoodsTemplateWidgetValue;
use App\Models\OrderDetail;
use App\Models\LevelingConfigure;
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
        $games = GoodsTemplate::where('goods_templates.status', 1)
            ->where('goods_templates.service_id', 4)
            ->leftJoin('games', 'games.id', '=', 'goods_templates.game_id')
            ->pluck('games.name');

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
    	$types = DB::select("
            SELECT a.field_value as name FROM goods_template_widget_values a
            LEFT JOIN goods_template_widgets b
            ON a.goods_template_widget_id=b.id
            WHERE a.field_name='game_leveling_type' 
            AND a.goods_template_widget_id=
                (SELECT id FROM goods_template_widgets WHERE goods_template_id=
                    (SELECT id FROM goods_templates WHERE game_id='$gameId' AND service_id=4 AND STATUS=1 LIMIT 1)
                AND field_name='game_leveling_type' LIMIT 1)
        ");

    	$arr = [];
        if (isset($types) && ! empty($types)) {
        	foreach ($types as $type) {
        		$arr[] = $type->name;
        	}
        	$types = $arr;
        } else {
        	$types = [];
        }
// dd($types);
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
    		->where('game_leveling_number', '>', $startNumber)
            ->where('game_leveling_number', '<=', $endNumber)
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
    		->where('level_count', $count)
    		->value('rebate') ?? 100;

    	$staticRebate = LevelingConfigure::where('game_name', $gameName)
    		->where('game_leveling_type', $type)
    		->value('rebate') ?? 100;
    	
    	$payment = bcmul($price, $rebate*0.01, 2)+0;// 玩家支付价格
    	$showPrice = bcmul($payment, 1.5, 2)+0;// 展示的优惠前价格
    	
    	$price = bcmul($payment, $staticRebate*0.01, 2)+0;// 发单平台价格
    	$showTime = sec2Time($time*60);// 需要代练的时间

        return response()->ajax(1, ['showPrice' => $showPrice, 'payment' => $payment, 'price' => $price, 'showTime' => $showTime, 'startNumber' =>$startNumber, 'endNumber' => $endNumber, 'startLevel' => $startLevel, 'endLevel' => $endLevel, 'time' => $time]);
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

        return response()->ajax(1, $servers);
    }

     /**
     * 下单
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function placeOrder(Request $request)
    {
        $data = $request->all();
// dd($data);
        $gameName = $data['game_name'];
        $type = $data['game_leveling_type'];
        $price = $data['price'];
        $payment = $data['payment'];
        $time = $data['time'];
        $showPrice = $data['showPrice'];
        $showTime = $data['showTime'];
        $startLevel = $data['startLevel'];
        $endLevel = $data['endLevel'];

        if (empty($gameName) || empty($type) || empty($data['price']) || empty($data['payment'])) {
            return response()->ajax(0, '请重新选择游戏');
        }

        return view('mobile.leveling.place-order', compact('gameName', 'type', 'price', 'payment', 'time', 'showPrice', 'showTime', 'data', 'startLevel', 'endLevel'));
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

        if (empty($gameName) || empty($type) || empty($price) || empty($payment) || empty($time) || empty($startNumber) || empty($endNumber) || empty($startLevel) || empty($endLevel)) {
            return response()->ajax(0, '请先完善填写信息');
        }

        return redirect(route('mobile.leveling.place-order'))->with(compact('gameName', 'type', 'price', 'payment', 'time', 'startLevel', 'endLevel', 'startNumber', 'endNumber'));
    }
}

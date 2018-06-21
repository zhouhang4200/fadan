<?php

namespace App\Http\Controllers\Mobile;

use Exception;
use Carbon\Carbon;
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
    public function getDemand(Request $request)
    {
    	$games = GoodsTemplate::where('goods_templates.status', 1)
        	->where('goods_templates.service_id', 4)
        	->leftJoin('games', 'games.id', '=', 'goods_templates.game_id')
        	->select('games.id', 'games.name')
        	->get();
    }

     /**
     * 获取游戏对应的类型
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function types(Request $request)
    {
    	$gameId = $request->game_id;

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

    	return json_encode(['status' => 1, 'types' => $types]);
    }

    /**
     * 获取代练层级（青铜-黄金）
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getTargets(Request $request)
    {
    	$gameId = $request->game_id;
    	$type = $request->type;
    	// 代练目标
    	$targets = LevelingPriceConfigure::where('game_id', $gameId)
    		->where('game_leveling_type', $type)
    		->get();
    }

    /**
     * 获取代练价格和时间
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function computePriceAndTime(Request $request)
    {
    	$gameId = $request->game_id;
    	$type = $request->type;
    	$startTarget = $request->startTarget;
    	$endTarget = $request->endTarget;

    	$price = LevelingPriceConfigure::where('game_id', $gameId)
    		->where('game_leveling_type', $type)
    		->where('game_leveling_number', '>', $startTarget)
            ->where('game_leveling_number', '<=', $endTarget)
    		->sum('level_price');

    	$time = LevelingPriceConfigure::where('game_id', $gameId)
    		->where('game_leveling_type', $type)
    		->where('game_leveling_number', '>=', $startTarget)
            ->where('game_leveling_number', '<', $endTarget)
    		->sum('level_hour');

    	$count = LevelingPriceConfigure::where('game_id', $gameId)
    		->where('game_leveling_type', $type)
    		->where('game_leveling_number', '>=', $startTarget)
            ->where('game_leveling_number', '<', $endTarget)
    		->count();

    	$rebate = LevelingRebateConfigure::where('game_id', $gameId)
    		->where('game_leveling_type', $type)
    		->where('level_count', $count)
    		->value('rebate') ?? 100;

    	$staticRebate = LevelingConfigure::where('game_id', $gameId)
    		->where('game_leveling_type', $type)
    		->value('rebate') ?? 100;
    	
    	$payment = bcmul($price, $rebate*0.01, 2);// 玩家支付价格
    	$showPrice = bcmul($payment, 1.5, 2);// 展示的优惠前价格
    	
    	$price = bcmul($payment, $staticRebate*0.01, 2);// 发单平台价格
    	$showTime = sec2Time($time*60);// 需要代练的时间
    }

    /**
     * 下单
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function placeOrder(Request $request)
    {
    	
    }

    public function getRegions(Request $request)
    {
    	$gameName = $request->game_name ?? '';

        $gameId = Game::where('name', $gameName)->value('id');

        // 游戏模板
        $goodsTemplateId = GoodsTemplate::where('game_id', $gameId)
            ->where('service_id', 4)
            ->where('status', 1)
            ->first();

        if (! $goodsTemplateId) {
            return response()->partner(0, '该代练游戏不存在');
        }

    	$regions = DB::select("
    		SELECT a.field_value as name FROM goods_template_widget_values a
			LEFT JOIN goods_template_widgets b
			ON a.goods_template_widget_id=b.id
			WHERE a.field_name='region' 
			AND a.goods_template_widget_id=
				(SELECT id FROM goods_template_widgets WHERE goods_template_id=
					(SELECT id FROM goods_templates WHERE game_id='$gameId' AND service_id=4 AND STATUS=1 LIMIT 1)
				AND field_name='region' LIMIT 1)
			") ?? '空';
    }

    public function getServers(Request $request)
    {
    	$templateId = GoodsTemplate::where('game_id', $request->game_id)->where('service_id', 4)->value('id'); //模板id
        // 我们的区
        $areaTemplateWidgetId = GoodsTemplateWidget::where('goods_template_id', $templateId)
            ->where('field_name', 'region')
            ->value('id');
        $areaId = GoodsTemplateWidgetValue::where('goods_template_widget_id', $areaTemplateWidgetId)
            ->where('field_name', 'region')
            ->where('field_value', $orderDetails['region'])
            ->value('id');
        // 我们的服
        $serverTemplateWidgetId = GoodsTemplateWidget::where('goods_template_id', $templateId)
            ->where('field_name', 'serve')
            ->value('id');
        $serverId = GoodsTemplateWidgetValue::where('goods_template_widget_id', $serverTemplateWidgetId)
            ->where('field_name', 'serve')
            ->where('parent_id', $areaId)
            ->where('field_value', $orderDetails['serve'])
            ->value('id');
    }
}

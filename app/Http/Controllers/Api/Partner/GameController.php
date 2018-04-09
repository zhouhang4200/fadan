<?php

namespace App\Http\Controllers\Api\Partner;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GameController extends Controller
{
	/**
	 * 获取代练游戏列表
	 * @return [type] [description]
	 */
    public function games()
    {
    	$games = DB::select("
    		SELECT DISTINCT(g.name) FROM games g
			LEFT JOIN goods_templates gt
			ON g.id = gt.game_id
			WHERE gt.service_id = 4 AND gt.status = 1
		") ?? '空';

		return response()->partner(1, '成功', $games);
    }

    /**
     * 获取某个游戏下面的区
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function regions(Request $request)
    {
    	$gameName = $request->game_name ?? '';

    	$regions = DB::select("
    		SELECT a.field_value as name FROM goods_template_widget_values a
			LEFT JOIN goods_template_widgets b
			ON a.goods_template_widget_id=b.id
			WHERE a.field_name='region' 
			AND a.goods_template_widget_id=
				(SELECT id FROM goods_template_widgets WHERE goods_template_id=
					(SELECT id FROM goods_templates WHERE game_id=(select id from games where name='$gameName' LIMIT 1) AND service_id=4 AND STATUS=1 LIMIT 1)
				AND field_name='region' LIMIT 1)
			") ?? '空';

    	return response()->partner(1, '成功', $regions);
    }

    /**
     * 获取某个区下面的服
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function servers(Request $request)
    {
    	$regionName = $request->region_name ?? '';

    	$servers = DB::select("
    		SELECT field_value as name FROM goods_template_widget_values 
			WHERE parent_id=(SELECT a.id FROM goods_template_widget_values a
			LEFT JOIN goods_template_widgets b
			ON a.goods_template_widget_id=b.id
			WHERE a.field_name='region' AND a.goods_template_widget_id=(SELECT id FROM goods_template_widgets 
			WHERE goods_template_id=(SELECT id FROM goods_templates WHERE game_id=1 AND service_id=4 AND STATUS=1 LIMIT 1) AND field_name='region' LIMIT 1)
			AND a.field_value='$regionName' LIMIT 1)
		") ?? '空';

		return response()->partner(1, '成功', $servers);
    }
}

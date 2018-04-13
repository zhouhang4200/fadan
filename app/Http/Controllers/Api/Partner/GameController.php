<?php

namespace App\Http\Controllers\Api\Partner;

use DB;
use App\Models\Game;
use Illuminate\Http\Request;
use App\Models\GoodsTemplate;
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

    	return response()->partner(1, '成功', $regions);
    }

    /**
     * 获取某个区下面的服
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function servers(Request $request)
    {
        $gameName = $request->game_name ?? '';
        $regionName = $request->region_name ?? '';

        $gameId = Game::where('name', $gameName)->value('id');
        // 游戏模板
        $goodsTemplateId = GoodsTemplate::where('game_id', $gameId)
            ->where('service_id', 4)
            ->where('status', 1)
            ->first();

        if (! $goodsTemplateId) {
            return response()->partner(0, '该代练游戏不存在');
        }

    	$servers = DB::select("
    		SELECT field_value as name FROM goods_template_widget_values 
			WHERE parent_id=(SELECT a.id FROM goods_template_widget_values a
			LEFT JOIN goods_template_widgets b
			ON a.goods_template_widget_id=b.id
			WHERE a.field_name='region' AND a.goods_template_widget_id=(SELECT id FROM goods_template_widgets 
			WHERE goods_template_id=(SELECT id FROM goods_templates WHERE game_id='$gameId' AND service_id=4 AND STATUS=1 LIMIT 1) AND field_name='region' LIMIT 1)
			AND a.field_value='$regionName' LIMIT 1)
		") ?? '空';

        if (! isset($servers) || ! is_array($servers) || count($servers) == 0) {
            return response()->partner(0, '该区名不存在');
        }

		return response()->partner(1, '成功', $servers);
    }

       /**
     * 获取某个游戏的代练类型
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function gameTypes(Request $request)
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

        $types = DB::select("
            SELECT a.field_value as name FROM goods_template_widget_values a
            LEFT JOIN goods_template_widgets b
            ON a.goods_template_widget_id=b.id
            WHERE a.field_name='game_leveling_type' 
            AND a.goods_template_widget_id=
                (SELECT id FROM goods_template_widgets WHERE goods_template_id=
                    (SELECT id FROM goods_templates WHERE game_id='$gameId' AND service_id=4 AND STATUS=1 LIMIT 1)
                AND field_name='game_leveling_type' LIMIT 1)
            ") ?? '空';

        return response()->partner(1, '成功', $types);
    }

    /**
     * 获取某个游戏下面的所有区，某个区下面的所有服
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function datas(Request $request)
    {
        // 获取所有的区
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
            SELECT a.field_value AS name FROM goods_template_widget_values a
            LEFT JOIN goods_template_widgets b
            ON a.goods_template_widget_id=b.id
            WHERE a.field_name='region' 
            AND a.goods_template_widget_id=
                (SELECT id FROM goods_template_widgets WHERE goods_template_id=
                    (SELECT id FROM goods_templates WHERE game_id='$gameId' AND service_id=4 AND STATUS=1 LIMIT 1)
                AND field_name='region' LIMIT 1)
            ") ?? '';

        if (! isset($regions) || ! is_array($regions) || count($regions) == 0) {
            return response()->partner(0, '该游戏下面没有区');
        }

        // 初始化数组
        $gameDatas = [];
        // 遍历所有的区
        if (isset($regions) && is_array($regions) && count($regions) > 0) {

            $gameDatas['game_region'] = array_map(function ($region) {
                return $region->name;
            }, $regions);

            foreach ($gameDatas['game_region'] as $k => $region) {
                // 根据区名找服
                $servers = DB::select("
                    SELECT field_value as name FROM goods_template_widget_values 
                    WHERE parent_id=(SELECT a.id FROM goods_template_widget_values a
                    LEFT JOIN goods_template_widgets b
                    ON a.goods_template_widget_id=b.id
                    WHERE a.field_name='region' AND a.goods_template_widget_id=(SELECT id FROM goods_template_widgets 
                    WHERE goods_template_id=(SELECT id FROM goods_templates WHERE game_id=(select id from games where name='$gameName' LIMIT 1) AND service_id=4 AND STATUS=1 LIMIT 1) AND field_name='region' LIMIT 1)
                    AND a.field_value='{$region}' LIMIT 1)
                ") ?? '';

                if ($servers && is_array($servers) && count($servers) > 0) {
                    //遍历服
                    foreach ($servers as $key => $server) {
                        $gameDatas[$region][$key] = $server->name;
                    }
                }
            }
            $gameDatas = array_merge(['game_name' => $gameName], $gameDatas);

            return response()->partner(1, '成功', $gameDatas);
        }
         return response()->partner(0, '失败');
    }
}

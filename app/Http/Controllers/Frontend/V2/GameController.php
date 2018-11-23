<?php

namespace App\Http\Controllers\Frontend\V2;

use App\Http\Controllers\Controller;
use App\Models\Game;


/**
 * 游戏
 * Class GameLevelingController
 * @package App\Http\Controllers\Frontend\V2\Order
 */
class GameController extends Controller
{
    /**
     * @return mixed
     */
    public function index()
    {
        $games = Game::select(['id', 'name'])->get();

        return response()->json(['status' => 1, 'message' => 'success', 'data' => $games]);
    }

    /*
     * 获取游戏区服所有数据
     */
    public function gameRegionServer()
    {
        $gameRegionServerGameType = Game::select('id', 'id as value', 'name as label')->with([
            'gameRegions' => function($query) {
                $query->select('game_id','id', 'id as value', 'name as label')
                    ->with(['gameServers' => function($query) {
                        $query->select('game_region_id', 'id as value', 'name as label');
                    }]);
            }
        ])->get()->toJson();

        // 替换游戏区关联关系名称
        $replaceRegionNameAfter = str_replace("game_regions", "children", $gameRegionServerGameType);
        // 替换游戏区关联关系名称
        $lastData = str_replace("game_servers", "children", $replaceRegionNameAfter);

        return response()->json(['status' => 1, 'message' => 'success', 'data' => json_decode($lastData)]);
    }
}
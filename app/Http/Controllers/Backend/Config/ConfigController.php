<?php

namespace App\Http\Controllers\Backend\Config;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Show91;

// 配置我们的游戏区服和第三方游戏区服的关系
class ConfigController extends Controller
{
	/**
	 * 配置我们的游戏和第三方游戏的关系
	 * @return [type] [description]
	 */
    public function game(Request $request)
    {
    	$thirdId = $request->third_id;
    	$gameId = $request->game_id;
    	$thirdGameId = $request->third_game_id;
    	$games = Show91::getGames(); // 91 所有的游戏

    	$gameArr = [];
    	foreach ($games['games'] as $k => $game) {
    		$gameArr[$game['id']] = $game['game_name'];
    	}
    	// dd($gameArr);
    	return view('backend.config.game', compact('gameArr'));
    	// dd($gameArr);
    	// 如果选了
    }

    /**
     * 根据第三方平台号搜索对应平台下面的游戏
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getThirdGames(Request $request)
    {
    	// 如果存在第三方平台
    	switch ($request->thirdId) {
    		case 1:
    			$games = Show91::getGames(); // 91 所有的游戏
    			$gameArr = [];
		    	foreach ($games['games'] as $k => $game) {
		    		$gameArr[$game['id']] = $game['game_name'];
		    	}
		    	// 页面注入
		    	view(route('backend.config.game'), compact('gameArr'));

		    	return true;
    		break;
    		case 2:
    		break;
    		case 3:
    		break;
    		case 4:
    		break;
    	}
    }
}

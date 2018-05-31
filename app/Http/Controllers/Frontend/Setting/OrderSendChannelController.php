<?php

namespace App\Http\Controllers\Frontend\Setting;

use Auth;
use App\Models\Game;
use App\Models\GoodsTemplate;
use Illuminate\Http\Request;
use App\Models\OrderSendChannel;
use App\Http\Controllers\Controller;

class OrderSendChannelController extends Controller
{
	/**
	 * 发单渠道设置列表
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
    public function index(Request $request)
    {
    	$primaryUserId = Auth::user()->getPrimaryUserId();
    	$gameIds = GoodsTemplate::where('status', 1)->where('service_id', 4)->pluck('game_id');
    	$games = Game::whereIn('id', $gameIds)->pluck('name', 'id');

    	if ($request->ajax()) {
    		return response()->json(view()->make('frontend.v1.setting.sending-assist.send-channel-list', [
                'games' => $games,
                'primaryUserId' => $primaryUserId,
            ])->render());
    	}

    	return view('frontend.v1.setting.sending-assist.send-channel', compact('games', 'primaryUserId'));
    }

    /**
     * 发单渠道设置
     * @param Request $request [description]
     */
    public function set(Request $request)
    {
    	$userId = Auth::user()->getPrimaryUserId();
    	$gameId = $request->game_id;
    	$gameName = $request->game_name;
    	$thirds = $request->thirds; // 白名单
    	$realThirds = config('leveling.third'); // 所有

    	// 至少选一个游戏
    	if (! $thirds) {
    		return response()->ajax(0, '请至少选择一个平台');
    	}
    	// 如果已经存在设置过的平台
    	$orderSendChannel = OrderSendChannel::where('user_id', $userId)->where('game_id', $gameId)->first();

    	$diffThirds = array_diff($realThirds, $thirds); //黑名单
    	
    	if (count($diffThirds) < 1 && $orderSendChannel) {
    		$orderSendChannel->delete();
    	} else {
	    	$datas['user_id'] = $userId;
	    	$datas['game_id'] = $gameId;
	    	$datas['game_name'] = $gameName;
	    	$datas['third'] = implode($diffThirds, '-');
	    	OrderSendChannel::updateOrCreate(['user_id' => $userId, 'game_id' => $gameId], $datas);
    	}

    	return response()->ajax(1, '设置成功');
    }
}

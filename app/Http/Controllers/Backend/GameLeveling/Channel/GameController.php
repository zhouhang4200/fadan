<?php

namespace App\Http\Controllers\Backend\GameLeveling\Channel;

use DB;
use Exception;
use App\Models\Game;
use App\Models\User;
use App\Models\GameLevelingType;
use App\Http\Controllers\Controller;
use App\Models\GameLevelingChannelGame;

/**
 * 游戏代练 渠道
 * Class GameController
 * @package App\Http\Controllers\Backend\Config
 */
class GameController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function index()
    {
        $games = Game::all();

    	$items = GameLevelingChannelGame::filter(request()->all())->paginate(10);

    	return view('backend.game-leveling.channel.game.index')->with([
    	    'items' => $items,
    	    'games' => $games,
        ]);
    }

    /**
     * 添加视图
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
    	return view('backend.game-leveling.channel.game.create')->with([
    	    'games' => Game::all(),
            'users' => User::all(),
        ]);
    }

    /**
     * 添加游戏
     * @return mixed
     */
    public function store()
    {
        $gameInfo = explode('-', request('game_id'));
        $typeInfo = explode('-', request('game_leveling_type_id'));

        try {
            $has = GameLevelingChannelGame::where('game_id', $gameInfo[0])
                ->where('game_leveling_type_id', $typeInfo[0])
                ->first();

            if ($has) {
                return response()->ajax(0, '已存在相同类型配置');
            }

            GameLevelingChannelGame::create([
                'user_id' => request('user_id'),
                'game_id' => $gameInfo[0],
                'game_name' => $gameInfo[1],
                'game_leveling_type_id' => $typeInfo[0],
                'game_leveling_type_name' => $typeInfo[1],
                'instructions' => request('instructions'),
                'requirements' => request('requirements'),
                'user_qq' => request('user_qq'),
                'rebate' => request('rebate'),
            ]);

            return back()->with('success', '添加成功');
        } catch (\Exception $exception) {
            request()->flash();
            return back()->with('fail', '添加失败')->with('fail', $exception->getMessage());
        }
    }

    /**
     * 编辑
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit()
    {
    	$item = GameLevelingChannelGame::find(request('id'));

    	return view('backend.game-leveling.channel.game.edit')->with([
            'item' => $item,
            'games' => Game::all(),
            'users' => User::all(),
            'types' => GameLevelingType::where('game_id', $item->game_id)->get()
        ]);
    }

    /*
     * 修改
     */
    public function update()
    {
        $gameInfo = explode('-', request('game_id'));
        $typeInfo = explode('-', request('game_leveling_type_id'));

        DB::beginTransaction();
        try{

            $gameLevelingChannelGame = GameLevelingChannelGame::where('id', request('id'))->first();

            if ($gameLevelingChannelGame->game_id != $gameInfo[0] || $gameLevelingChannelGame->game_leveling_type_id != $typeInfo[0]) {
                $exist = GameLevelingChannelGame::where('game_id', $gameInfo[0])->where('game_leveling_type_id', $typeInfo[0])->first();
                if ($exist) {
                    DB::rollback();
                    return back()->with('fail', '修改失败，对应该游戏类型存已经存在');
                }
            }

            // 更新数据
            $gameLevelingChannelGame->game_id = $gameInfo[0];
            $gameLevelingChannelGame->game_name = $gameInfo[1];
            $gameLevelingChannelGame->game_leveling_type_id = $typeInfo[0];
            $gameLevelingChannelGame->game_leveling_type_name = $typeInfo[1];
            $gameLevelingChannelGame->user_id = request('user_id');
            $gameLevelingChannelGame->rebate = request('rebate');
            $gameLevelingChannelGame->instructions = request('instructions');
            $gameLevelingChannelGame->requirements = request('requirements');
            $gameLevelingChannelGame->user_qq = request('user_qq');
            $gameLevelingChannelGame->save();

        } catch (Exception $e) {
            DB::rollback();
            return back()->with('fail', $e->getMessage());
        }
        DB::commit();
        return back()->with('success', '修改成功');
    }

    /**
     * 删除
     * @return mixed
     */
    public function delete()
    {
        GameLevelingChannelGame::where('id', request('id'))->delete();

        return response()->json([
            'status' => 1,
            'message' => '删除成功',
        ]);
    }
}

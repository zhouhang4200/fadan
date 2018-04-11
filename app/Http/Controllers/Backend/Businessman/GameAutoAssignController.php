<?php

namespace App\Http\Controllers\Backend\Businessman;

use App\Models\GameAutoAssign;
use App\Models\User;
use App\Repositories\Frontend\GameRepository;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GameAutoAssignController extends Controller
{

	/**
	 * 首页
	 * @param GameRepository $gameRepository
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function index(Request $request, GameRepository $gameRepository)
	{
		//多条件查找
		$where = function ($query) use ($request) {
			if ($request->has('game_name') and $request->game_name != '') {
				$name = "%" . $request->game_name . "%";
				$game_ids = \DB::table('games')->where('name', 'like', $name)->pluck('id');
				$query->whereIn('game_id', $game_ids);
			}

			if ($request->has('creator_primary_user_id') and $request->creator_primary_user_id != '') {
				$query->where('creator_primary_user_id', $request->creator_primary_user_id);
			}

			if ($request->has('gainer_primary_user_id') and $request->gainer_primary_user_id != '') {
				$query->where('gainer_primary_user_id', $request->gainer_primary_user_id);
			}
		};
		$orienteds = GameAutoAssign::with('game')->where($where)->paginate(20);
		$games = $gameRepository->available();
		return view('backend.businessman.oriented.index', compact('orienteds', 'services', 'games'));
	}

	/**
	 * 创建
	 * @param Request $request
	 * @return mixed
	 */
	public function store(Request $request)
	{
		try {
			if (GameAutoAssign::checkGame($request->game_id, $request->creator_primary_user_id, $request->gainer_primary_user_id)) {
				return response()->ajax(0, '该游戏主发单人，主接单人已经存在，请不要重复添加');
			};

			if (GameAutoAssign::creatorPrimaryCheckGame($request->game_id, $request->creator_primary_user_id)) {
				return response()->ajax(0, '该游戏主发单人已存在，请不要重复添加');
			}

			GameAutoAssign::create([
				'game_id'                 => $request->game_id,
				'creator_primary_user_id' => $request->creator_primary_user_id,
				'gainer_primary_user_id'  => $request->gainer_primary_user_id,
			]);
			return response()->ajax(1, '添加成功');
		} catch (\Exception $e) {
			return response()->ajax(0, $e->getMessage());
		}


	}

	/**
	 * 删除
	 * @param Request $request
	 * @return mixed
	 */
	public function delete(Request $request)
	{
		try {
			GameAutoAssign::destroy($request->id);
			return response()->ajax(1, '删除成功');
		} catch (\Exception $e) {
			return response()->ajax(0, $e->getMessage());
		}

	}
}

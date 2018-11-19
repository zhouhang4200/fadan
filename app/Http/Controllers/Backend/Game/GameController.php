<?php

namespace App\Http\Controllers\Backend\Game;

use App\Models\GameRegion;
use App\Models\GameServer;
use Exception;
use App\Models\Game;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
    /**
     * 游戏管理列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $name = request('name');

        $allGames = Game::get();

        $games = Game::filter(['name' => $name])->latest('id')->paginate(15);

        return view('backend.game.index', compact('games', 'name', 'allGames'));
    }

    /**
     * 游戏添加
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $game = Game::find(request('id'));

        return view('backend.game.create', compact('game'));
    }

    /**
     * 游戏添加
     * @return mixed
     */
    public function store()
    {
        try {
            Game::create([
                'name' => request('name'),
                'icon' => request('icon'),
                'sortord' => 999,
                'status' => 1,
                'created_admin_user_id' => Auth::guard('admin')->user()->id,
                'updated_admin_user_id' => Auth::guard('admin')->user()->id,
            ]);
        } catch (Exception $e) {
            myLog('game-store-error', [$e->getMessage(), $e->getLine()]);
            return response()->ajax(0, '添加失败：服务器错误!');
        }
        return response()->ajax(1, '添加成功!');
    }

    /**
     * 游戏编辑
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit()
    {
        try {
            $game = Game::find(request('id'));
            return view('backend.game.edit', compact('game'));
        } catch (Exception $e) {

        }
    }

    /**
     * 游戏修改
     * @return mixed
     */
    public function update()
    {
        try {
            $game = Game::find(request('id'));

            $game->name = request('name');
            $game->icon = request('icon');
            $game->save();
        } catch (Exception $e) {
            myLog('game-update-error', [$e->getMessage(), $e->getLine()]);
            return response()->ajax(0, "修改失败：服务器错误!");
        }
        return response()->ajax(1, '修改成功!');
    }

    /**
     * 删除游戏以及游戏的区服
     * @return mixed
     */
    public function delete()
    {
        DB::beginTransaction();
        try {
            $game = Game::find(request('id'));
            // 删除区,服
            $gameRegionIds = GameRegion::where('game_id', $game->id)
                ->pluck('id');
            foreach ($gameRegionIds as $gameRegionId) {
                GameServer::where('game_region_id', $gameRegionId)->delete();
            }
            GameRegion::destroy($gameRegionIds->toArray());

            $game->delete();
        } catch (Exception $e) {
            DB::rollback();
            myLog('game-delete-error', [$e->getMessage(), $e->getLine()]);
            return response()->ajax(0, '删除失败!');
        }
        DB::commit();
        return response()->ajax(1, '删除成功!');
    }

    /**
     * 游戏状态开关
     * @return mixed
     */
    public function status()
    {
        try {
            $game = Game::find(request('id'));
            $game->status = request('status');
            $game->save();
        } catch (Exception $e) {
            return response()->ajax(0, '服务器异常！');
        }
        return response()->ajax(1, '操作成功!');
    }

    /**
     * 图片上传
     * @return mixed
     */
    public function upload()
    {
        try {
            if (request()->hasFile('file')) {
                $file = request()->file('file');
                $path = public_path("/resources/game/".date('Ymd')."/");
                $extension = $file->getClientOriginalExtension();

                if ($extension && ! in_array(strtolower($extension), ['png', 'jpg', 'jpeg', 'gif'])) {
                    return response()->ajax(0, '上传失败!');
                }

                if (! $file->isValid()) {
                    return response()->ajax(0, '上传失败!');
                }

                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                $randNum = rand(1, 100000000) . rand(1, 100000000);
                $fileName = time().substr($randNum, 0, 6).'.'.$extension;
                $path = $file->move($path, $fileName);
                $path = strstr($path, '/resources');
                $path = str_replace('\\', '/', $path);

                return response()->ajax(1, $path);
            }
        } catch (Exception $e) {
            myLog('game-upload-error', [$e->getMessage(), $e->getLine()]);
            return response()->ajax(0, '上传失败!');
        }
        return response()->ajax(0, '上传失败!');
    }
}

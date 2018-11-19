<?php

namespace App\Http\Controllers\Backend\Game;

use Exception;
use Carbon\Carbon;
use App\Models\Game;
use App\Models\GameRegion;
use App\Models\GameServer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Overtrue\LaravelPinyin\Facades\Pinyin;

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

        // 删除的时候页面不刷新
        if (request()->ajax()) {
            return response()->json(view()->make('backend.game.list', [
                'games' => $games,
                'name' => $name,
                'allGames' => $allGames
            ])->render());
        }

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

    /**
     * 游戏区配置
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function regionIndex()
    {
        $name = request('name');
        $allRegions = GameRegion::get();

        $regions = GameRegion::filter(['name' => request('name')])->latest('id')->paginate(15);

        // 删除的时候页面不刷新
        if (request()->ajax()) {
            return response()->json(view()->make('backend.game.region.list', [
                'regions' => $regions,
                'name' => $name,
                'allRegions' => $allRegions
            ])->render());
        }

        return view('backend.game.region.index', compact('allRegions', 'regions', 'name'));
    }

    /**
     * 游戏区新增
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function regionCreate()
    {
        $games = Game::latest('id')->get();
        return view('backend.game.region.create', compact('games'));
    }

    /**
     * 新增区
     * @return mixed
     */
    public function regionStore()
    {
        try {
            $regionNames = explode(',', request('name'));

            $data = [];
            foreach ($regionNames as $regionName) {
                // 是否多次添加
                $region = GameRegion::where('game_id', request('game_id'))
                    ->where('name', $regionName)
                    ->first();

                if (! $region) {
                    $data[] = [
                        'game_id' => request('game_id'),
                        'name' => $regionName,
                        'initials' => substr(Pinyin::permalink($regionName), 0, 1),
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString(),
                    ];
                }
            }
            GameRegion::insert($data);
        } catch (Exception $e) {
            myLog('region-store-error', [$e->getMessage(), $e->getLine()]);
            return response()->ajax(0, '操作失败!');
        }
        return response()->ajax(1, '操作成功!');
    }

    /**
     * 游戏区编辑
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function regionEdit()
    {
        $region = GameRegion::find(request('id'));
        $games = Game::get();
        return view('backend.game.region.edit', compact('region', 'games'));
    }

    /**
     * 游戏区修改
     * @return mixed
     */
    public function regionUpdate()
    {
        try {
            $gameRegion = GameRegion::find(request('id'));

            $gameRegion->game_id = request('game_id');
            $gameRegion->name = request('name');
            $gameRegion->initials = substr(Pinyin::permalink(request('name')), 0, 1);
            $gameRegion->save();
        } catch (Exception $e) {
            return response()->ajax(0, '操作失败!');
        }
        return response()->ajax(1, '操作成功!');
    }

    /**
     * 游戏区删除
     * @return mixed
     */
    public function regionDelete()
    {
        try {
            $gameRegion = GameRegion::find(request('id'));
            $gameRegion->delete();
        } catch (Exception $e) {
            return response()->ajax(0, '操作失败!');
        }
        return response()->ajax(1, '操作成功!');
    }

    public function serverIndex()
    {

    }

    public function serverCreate()
    {

    }

    public function serverStore()
    {
        try {

        } catch (Exception $e) {
            return response()->ajax(0, '操作失败!');
        }
        return response()->ajax(1, '操作成功!');
    }

    public function serverEdit()
    {

    }

    public function serverUpdate()
    {
        try {

        } catch (Exception $e) {
            return response()->ajax(0, '操作失败!');
        }
        return response()->ajax(1, '操作成功!');
    }

    public function serverDelete()
    {
        try {

        } catch (Exception $e) {
            return response()->ajax(0, '操作失败!');
        }
        return response()->ajax(1, '操作成功!');
    }
}

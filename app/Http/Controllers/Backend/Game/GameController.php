<?php

namespace App\Http\Controllers\Backend\Game;

use Exception;
use Carbon\Carbon;
use App\Models\Game;
use App\Models\GameType;
use App\Models\GameRegion;
use App\Models\GameServer;
use Illuminate\Http\Request;
use App\Models\GameLevelingType;
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

        $games = Game::filter(['name' => $name])->latest('id')->paginate(15);

        // 删除的时候页面不刷新
        if (request()->ajax()) {
            return response()->json(view()->make('backend.game.list', [
                'games' => $games,
                'name' => $name,
            ])->render());
        }

        return view('backend.game.index', compact('games', 'name'));
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
        DB::beginTransaction();
        try {
            $game = Game::updateOrCreate(
                ['name' => request('name')],
                [
                    'name' => request('name'),
                    'icon' => request('icon'),
                    'sortord' => 999,
                    'status' => 1,
                    'created_admin_user_id' => Auth::guard('admin')->user()->id,
                    'updated_admin_user_id' => Auth::guard('admin')->user()->id,
                ]
            );

            // 游戏类型
            if (count(request('type', [])) > 0) {
                foreach (request('type') as $type) {
                    GameType::updateOrCreate(
                        ['type' => $type, 'game_id' => $game->id],
                        ['type' => $type, 'game_id' => $game->id]
                    );
                }
            }
        } catch (Exception $e) {
            DB::rollback();
            myLog('game-store-error', [$e->getMessage(), $e->getLine()]);
            return response()->ajax(0, '添加失败：服务器错误!');
        }
        DB::commit();
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
        DB::beginTransaction();
        try {
            $game = Game::find(request('id'));

            $game->name = request('name');
            $game->icon = request('icon');
            $game->save();

            GameType::where('game_id', $game->id)->delete();

            // 游戏类型
            if (count(request('type', [])) > 0) {
                foreach (request('type') as $type) {
                    GameType::updateOrCreate(
                        ['type' => $type, 'game_id' => $game->id],
                        ['type' => $type, 'game_id' => $game->id]
                    );
                }
            }
        } catch (Exception $e) {
            DB::rollback();
            myLog('game-update-error', [$e->getMessage(), $e->getLine()]);
            return response()->ajax(0, "修改失败：服务器错误!");
        }
        DB::commit();
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
                $path = public_path("/resources/game/" . date('Ymd') . "/");
                $extension = $file->getClientOriginalExtension();

                if ($extension && !in_array(strtolower($extension), ['png', 'jpg', 'jpeg', 'gif'])) {
                    return response()->ajax(0, '上传失败!');
                }

                if (!$file->isValid()) {
                    return response()->ajax(0, '上传失败!');
                }

                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                $randNum = rand(1, 100000000) . rand(1, 100000000);
                $fileName = time() . substr($randNum, 0, 6) . '.' . $extension;
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

        $regions = GameRegion::filter(['name' => request('name')])->latest('id')->paginate(15);

        // 删除的时候页面不刷新
        if (request()->ajax()) {
            return response()->json(view()->make('backend.game.region.list', [
                'regions' => $regions,
                'name' => $name,
            ])->render());
        }

        return view('backend.game.region.index', compact('regions', 'name'));
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

                if (!$region) {
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
        DB::beginTransaction();
        try {
            $gameRegion = GameRegion::find(request('id'));

            $gameServerIds = GameServer::where('game_region_id', $gameRegion->id)->pluck('id')->toArray();
            GameServer::destroy($gameServerIds);

            $gameRegion->delete();
        } catch (Exception $e) {
            DB::rollback();
            return response()->ajax(0, '操作失败!');
        }
        DB::commit();
        return response()->ajax(1, '操作成功!');
    }

    /**
     * 游戏服列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function serverIndex()
    {
        $name = request('name');

        $servers = GameServer::filter(['name' => request('name')])->latest('id')->paginate(15);

        // 删除的时候页面不刷新
        if (request()->ajax()) {
            return response()->json(view()->make('backend.game.server.list', [
                'servers' => $servers,
                'name' => $name,
            ])->render());
        }

        return view('backend.game.server.index', compact('servers', 'name'));
    }

    /**
     * 游戏服添加
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function serverCreate()
    {
        $games = Game::latest('id')->get();

        return view('backend.game.server.create', compact('games'));
    }

    /**
     * 游戏服新增
     * @return mixed
     */
    public function serverStore()
    {
        try {
            GameServer::create([
                'game_region_id' => request('game_region_id'),
                'name' => request('name'),
                'initials' => substr(Pinyin::permalink(request('name')), 0, 1),
            ]);
        } catch (Exception $e) {
            return response()->ajax(0, '操作失败!');
        }
        return response()->ajax(1, '操作成功!');
    }

    /**
     * 游戏服编辑
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function serverEdit()
    {
        $games = Game::latest('id')->get();
        $server = GameServer::find(request('id'));
        $regions = GameRegion::where('game_id', $server->gameRegion->game->id)->get();
        return view('backend.game.server.edit', compact('games', 'server', 'regions'));
    }

    /**
     * 游戏服编辑
     * @return mixed
     */
    public function serverUpdate()
    {
        try {
            $server = GameServer::find(request('id'));

            $server->name = request('name');
            $server->game_region_id = request('game_region_id');
            $server->initials = substr(Pinyin::permalink(request('name')), 0, 1);
            $server->save();
        } catch (Exception $e) {
            return response()->ajax(0, '操作失败!');
        }
        return response()->ajax(1, '操作成功!');
    }

    /**
     * 游戏服删除
     * @return mixed
     */
    public function serverDelete()
    {
        try {
            GameServer::destroy(request('id'));
        } catch (Exception $e) {
            return response()->ajax(0, '操作失败!');
        }
        return response()->ajax(1, '操作成功!');
    }

    /**
     * 游戏服的区
     * @return mixed
     */
    public function serverRegion()
    {
        try {
            $regions = GameRegion::where('game_id', request('game_id'))->pluck('name', 'id');
        } catch (Exception $e) {
            return response()->ajax(0, '操作失败!');
        }
        return response()->ajax(1, $regions);
    }

    /**
     * 代练类型列表页
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function levelingIndex()
    {
        $name = request('name');

        $types = GameLevelingType::filter(['name' => request('name')])->latest('id')->paginate(15);

        // 删除的时候页面不刷新
        if (request()->ajax()) {
            return response()->json(view()->make('backend.game.gamelevelingtype.list', [
                'types' => $types,
                'name' => $name,
            ])->render());
        }

        return view('backend.game.gamelevelingtype.index', compact('types', 'name'));
    }

    /**
     * 代练类型添加
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function levelingCreate()
    {
        $games = Game::latest('id')->get();

        return view('backend.game.gamelevelingtype.create', compact('games'));
    }

    /**
     * 代练类型新增
     * @return mixed
     */
    public function levelingStore()
    {
        try {
            $names = explode(',', request('name'));

            $data = [];
            foreach ($names as $name) {
                // 去重
                if (!GameLevelingType::where('game_id', request('game_id'))->where('name', $name)->first()) {
                    $data[] = [
                        'game_id' => request('game_id'),
                        'name' => $name,
                        'poundage' => request('poundage', 0),
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString(),
                    ];
                }
            }
            GameLevelingType::insert($data);
        } catch (Exception $e) {
            return response()->ajax(0, '操作失败!');
        }
        return response()->ajax(1, '操作成功!');
    }

    /**
     * 代练类型修改
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function levelingEdit()
    {
        $type = GameLevelingType::find(request('id'));
        $games = Game::latest('id')->get();

        return view('backend.game.gamelevelingtype.edit', compact('games', 'type'));
    }

    /**
     * 代练类型修改
     * @return mixed
     */
    public function levelingUpdate()
    {
        try {
            $type = GameLevelingType::find(request('id'));

            $type->name = request('name');
            $type->game_id = request('game_id');
            $type->poundage = request('poundage');
            $type->save();
        } catch (Exception $e) {
            return response()->ajax(0, '操作失败!');
        }
        return response()->ajax(1, '操作成功!');
    }

    /**
     * 代练类型删除
     * @return mixed
     */
    public function levelingDelete()
    {
        try {
            GameLevelingType::destroy(request('id'));
        } catch (Exception $e) {
            return response()->ajax(0, '操作失败!');
        }
        return response()->ajax(1, '操作成功!');
    }
}

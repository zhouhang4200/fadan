<?php

namespace App\Http\Controllers\Backend\Config;

use DB;
use Exception;
use App\Models\Game;
use App\Models\GoodsTemplate;
use App\Models\LevelingConfigure;
use App\Models\LevelingPriceConfigure;
use App\Models\LevelingRebateConfigure;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LevelingConfigureController extends Controller
{	
	/**
	 * 列表
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
    public function index(Request $request)
    {
    	$gameId = $request->game_id;
    	$filters = compact('gameId');

        $games = GoodsTemplate::where('goods_templates.status', 1)
        	->where('goods_templates.service_id', 4)
        	->leftJoin('games', 'games.id', '=', 'goods_templates.game_id')
        	->select('games.id', 'games.name')
        	->get();

    	$datas = LevelingConfigure::filter($filters)
    		->paginate(10);

    	if ($request->ajax()) {
    		return response()->json(view()->make('backend.leveling.config.list', compact('datas', 'gameId', 'games'))->render());
    	}

    	return view('backend.leveling.config.index', compact('datas', 'gameId', 'games'));
    }

    /**
     * 添加
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function create(Request $request)
    {
    	 // 所有宝贝
        $games = GoodsTemplate::where('goods_templates.status', 1)
        	->where('goods_templates.service_id', 4)
        	->leftJoin('games', 'games.id', '=', 'goods_templates.game_id')
        	->select('games.id', 'games.name')
        	->get();

    	return view('backend.leveling.config.create', compact('games'));
    }

    /**
     * 获取游戏对应的类型
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function types(Request $request)
    {
    	$gameId = $request->game_id;

    	$types = DB::select("
            SELECT a.field_value as name FROM goods_template_widget_values a
            LEFT JOIN goods_template_widgets b
            ON a.goods_template_widget_id=b.id
            WHERE a.field_name='game_leveling_type' 
            AND a.goods_template_widget_id=
                (SELECT id FROM goods_template_widgets WHERE goods_template_id=
                    (SELECT id FROM goods_templates WHERE game_id='$gameId' AND service_id=4 AND STATUS=1 LIMIT 1)
                AND field_name='game_leveling_type' LIMIT 1)
        ");

    	$arr = [];
        if (isset($types) && ! empty($types)) {
        	foreach ($types as $type) {
        		$arr[] = $type->name;
        	}
        	$types = $arr;
        } else {
        	$types = [];
        }

    	return json_encode(['status' => 1, 'types' => $types]);
    }

    /**
     * 添加
     * @param Request $request [description]
     */
    public function store(Request $request)
    {
    	$data = $request->data;

    	if (! isset($request->data['game_leveling_type']) || ! isset($request->data['game_id'])) {
    		return response()->ajax(0, '请选择游戏或代练类型');
    	}

        $has = LevelingConfigure::where('game_id', $data['game_id'])->where('game_leveling_type', $data['game_leveling_type'])->first();

        if ($has) {
            return response()->ajax(0, '已存在相同类型配置');
        }

    	if (isset($data) && ! empty($data)) {
    		$data['game_name'] = Game::find($data['game_id']) ? Game::find($data['game_id'])->name : '';
    	}
    	LevelingConfigure::create($data);
    	
    	return response()->ajax(1, '添加成功');
    }

    /**
     * 编辑
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function edit(Request $request)
    {
    	$data = LevelingConfigure::find($request->id);

    	// 所有宝贝
        $games = GoodsTemplate::where('goods_templates.status', 1)
        	->where('goods_templates.service_id', 4)
        	->leftJoin('games', 'games.id', '=', 'goods_templates.game_id')
        	->select('games.id', 'games.name')
        	->get();

    	$types = DB::select("
            SELECT a.field_value as name FROM goods_template_widget_values a
            LEFT JOIN goods_template_widgets b
            ON a.goods_template_widget_id=b.id
            WHERE a.field_name='game_leveling_type' 
            AND a.goods_template_widget_id=
                (SELECT id FROM goods_template_widgets WHERE goods_template_id=
                    (SELECT id FROM goods_templates WHERE game_id='{$data->game_id}' AND service_id=4 AND STATUS=1 LIMIT 1)
                AND field_name='game_leveling_type' LIMIT 1)
        ");

    	$arr = [];
        if (isset($types) && ! empty($types)) {
        	foreach ($types as $type) {
        		$arr[] = $type->name;
        	}
        	$types = $arr;
        } else {
        	$types = [];
        }

    	return view('backend.leveling.config.edit', compact('data', 'types', 'games'));
    }

    /**
     * 修改
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function update(Request $request)
    {
        DB::beginTransaction();
        try{
            $levelingConfigure = LevelingConfigure::find($request->id);

            if (! isset($request->data['game_leveling_type']) || ! isset($request->data['game_id'])) {
                return response()->ajax(0, '请选择游戏或代练类型');
            }

            $has = LevelingConfigure::where('game_id', $levelingConfigure->game_id)->where('game_leveling_type', $levelingConfigure->game_leveling_type)->count();

            if ($has > 1) {
                return response()->ajax(0, '已存在相同类型代练游戏以及代练类型配置');
            }
            $gameName = Game::find($request->data['game_id']) ? Game::find($request->data['game_id'])->name : '';

            if ($levelingConfigure) {
                // 其他的代练类型也变
                LevelingPriceConfigure::where('game_id', $levelingConfigure->game_id)
                    ->where('game_leveling_type', $levelingConfigure->game_leveling_type)
                    ->update([
                        'game_id' => $request->data['game_id'],
                        'game_name' => $gameName,
                        'game_leveling_type' => $request->data['game_leveling_type']
                    ]);

                LevelingRebateConfigure::where('game_id', $levelingConfigure->game_id)
                    ->where('game_leveling_type', $levelingConfigure->game_leveling_type)
                    ->update([
                        'game_id' => $request->data['game_id'],
                        'game_name' => $gameName,
                        'game_leveling_type' => $request->data['game_leveling_type']
                    ]);
                $levelingConfigure->game_id = $request->data['game_id'];
                $levelingConfigure->game_name = $gameName;
                $levelingConfigure->rebate = $request->data['rebate'];
                $levelingConfigure->game_leveling_type = $request->data['game_leveling_type'];
                $levelingConfigure->game_leveling_requirements = $request->data['game_leveling_requirements'];
                $levelingConfigure->game_leveling_instructions = $request->data['game_leveling_instructions'];
                $levelingConfigure->user_qq = $request->data['user_qq'];
                $levelingConfigure->save();
            }
        } catch (Exception $e) {
            DB::rollback();
            return response()->ajax(0, '修改失败');
        }
        DB::commit();
    	
    	return response()->ajax(1, '修改成功');
    }

    /**
     * 删除
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function delete(Request $request)
    {
    	DB::beginTransaction();
    	try {
	    	$levelingConfigure = LevelingConfigure::find($request->id);

	    	$levelingConfigure->delete();
	    	
	    	LevelingPriceConfigure::where('game_id', $levelingConfigure->game_id)
	    		->where('game_leveling_type', $levelingConfigure->game_leveling_type)
	    		->delete();

	    	LevelingRebateConfigure::where('game_id', $levelingConfigure->game_id)
	    		->where('game_leveling_type', $levelingConfigure->game_leveling_type)
	    		->delete();
    	} catch (Exception $e) {
    		DB::rollback();
    		return response()->ajax(0, '删除失败');
    	}
    	DB::commit();
    	return response()->ajax(1, '删除成功');
    }
}

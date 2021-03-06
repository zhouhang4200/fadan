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

class LevelingRebateConfigureController extends Controller
{
    /**
	 * 列表
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
    public function index(Request $request)
    {
    	$gameId = $request->game_id;
    	$type = $request->type;
    	$gameName = $request->game_name;
		// dd($gameId, $gameName, $type);
    	$datas = LevelingRebateConfigure::where('game_id', $gameId)
    		->where('game_leveling_type', $type)
    		->paginate(10);

    	if ($request->ajax()) {
    		return response()->json(view()->make('backend.leveling.rebate.list', compact('datas', 'gameId', 'type', 'gameName'))->render());
    	}

    	return view('backend.leveling.rebate.index', compact('datas', 'gameId', 'type', 'gameName'));
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
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function create(Request $request)
    {
    	$gameId = $request->game_id;
    	$type = $request->type;
    	$gameName = $request->game_name;
    	// dd($gameId, $type, $gameName);
    	return view('backend.leveling.rebate.create', compact('gameId', 'gameName', 'type'));
    }

    /**
     * 添加
     * @param Request $request [description]
     */
    public function store(Request $request)
    {
    	$data = $request->data;
    	$gameId = $request->game_id;
    	$type = $request->type;
    	$gameName = $request->game_name;
		// dd($gameId, $gameName, $type);
    	if (! isset($gameId) || ! isset($gameName) || ! isset($type) ) {
    		return response()->ajax(0, '未知错误');
    	}

    	if (isset($data) && ! empty($data)) {
    		$data['game_id'] = $gameId;
    		$data['game_name'] = $gameName;
    		$data['game_leveling_type'] = $type;
    	}
    	LevelingRebateConfigure::create($data);
    	
    	return response()->ajax(1, '添加成功');
    }

    /**
     * 编辑
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function edit(Request $request)
    {
    	$data = LevelingRebateConfigure::find($request->id);

    	return view('backend.leveling.rebate.edit', compact('data'));
    }

    /**
     * 修改
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function update(Request $request)
    {
    	try {
	    	$levelingConfigure = LevelingRebateConfigure::find($request->id);

	    	if ($levelingConfigure) {
	    		$levelingConfigure->level_count = $request->data['level_count'];
	    		$levelingConfigure->rebate = $request->data['rebate'];
	    		$levelingConfigure->save();
	    	}
	    	return response()->ajax(1, '修改成功');
    	} catch (Exception $e) {
    		return response()->ajax(0, '修改失败');
    	}
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
	    	LevelingRebateConfigure::destroy($request->id);
    	} catch (Exception $e) {
    		DB::rollback();
    		return response()->ajax(0, '删除失败');
    	}
    	DB::commit();
    	return response()->ajax(1, '删除成功');
    }
}

<?php

namespace App\Http\Controllers\Backend\Config;

use DB;
use Excel;
use Exception;
use Carbon\Carbon;
use App\Models\Game;
use App\Models\GoodsTemplate;
use App\Models\LevelingConfigure;
use App\Models\LevelingPriceConfigure;
use App\Models\LevelingRebateConfigure;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LevelingPriceConfigureController extends Controller
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
    	$gameLevelingNumber = $request->game_leveling_number;
    	$filters = compact('gameLevelingNumber');
		// dd($gameId, $gameName, $type);
    	$datas = LevelingPriceConfigure::filter($filters)
    		->where('game_id', $gameId)
    		->where('game_leveling_type', $type)
    		->paginate(10);

    	if ($request->ajax()) {
    		return response()->json(view()->make('backend.leveling.price.list', compact('datas', 'gameId', 'type', 'gameName'))->render());
    	}

    	return view('backend.leveling.price.index', compact('datas', 'gameId', 'type', 'gameLevelingNumber', 'gameName'));
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
    	return view('backend.leveling.price.create', compact('gameId', 'gameName', 'type'));
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
    	LevelingPriceConfigure::create($data);
    	
    	return response()->ajax(1, '添加成功');
    }

    /**
     * 编辑
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function edit(Request $request)
    {
    	$data = LevelingPriceConfigure::find($request->id);

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

    	return view('backend.leveling.price.edit', compact('data', 'types', 'games'));
    }

    /**
     * 修改
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function update(Request $request)
    {
    	try {
	    	$levelingConfigure = LevelingPriceConfigure::find($request->id);

	    	if ($levelingConfigure) {
	    		$levelingConfigure->game_leveling_number = $request->data['game_leveling_number'];
	    		$levelingConfigure->game_leveling_level = $request->data['game_leveling_level'];
	    		$levelingConfigure->level_price = $request->data['level_price'];
	    		$levelingConfigure->level_hour = $request->data['level_hour'];
	    		$levelingConfigure->level_security_deposit = $request->data['level_security_deposit'];
	    		$levelingConfigure->level_efficiency_deposit = $request->data['level_efficiency_deposit'];
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
	    	LevelingPriceConfigure::destroy($request->id);
    	} catch (Exception $e) {
    		DB::rollback();
    		return response()->ajax(0, '删除失败');
    	}
    	DB::commit();
    	return response()->ajax(1, '删除成功');
    }

    /**
     * 导入
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function import(Request $request)
    {
        try {
            if ($request->hasFile('file')) {
                $sheetName = ''; // 页脚,要求页脚名字必须为 区服配置 才能正常导入
                $excelDatas = []; // excel内容

                Excel::load($request->file('file')->path(), function ($reader) use (&$excelDatas, &$sheetName) {
                    $reader = $reader->getSheet(0);
                    $sheetName = $reader->getTitle();
                    $excelDatas = $reader->toArray();
                });

                $datas = [];
                foreach ($excelDatas as $k => $excelData) {
                    // 检测数据是否为空或为全字符标题
                    if (count($excelData) == 0 || array_sum($excelData) == 0) {
                        continue;
                    }

                    if (! is_numeric($excelData[0]) || ! is_numeric($excelData[3]) || ! is_numeric($excelData[4]) || ! is_numeric($excelData[5]) || ! is_numeric($excelData[6]) || ! is_numeric($excelData[7]) || ! is_numeric($excelData[8])) {
                        return response()->ajax(0, '第'.($k+1).'行数据必须为数字且不能为空!');
                    }
                    // 判断数据是否存在
                    $ifExists = LevelingPriceConfigure::where('game_id', $excelData[0])
                        ->where('game_leveling_type', $excelData[2])
                        ->where('game_leveling_number', $excelData[3])
                        ->first();

                    if ($ifExists) {
                        return response()->ajax(0, '第'.($k+1).'行数据已经存在!');
                    }
                    // 数组
                    $datas[$k]['game_id']                  = $excelData[0];
                    $datas[$k]['game_name']                = $excelData[1];
                    $datas[$k]['game_leveling_type']       = $excelData[2];
                    $datas[$k]['game_leveling_number']     = $excelData[3];
                    $datas[$k]['game_leveling_level']      = $excelData[4];
                    $datas[$k]['level_price']              = $excelData[5];
                    $datas[$k]['level_hour']               = $excelData[6];
                    $datas[$k]['level_security_deposit']   = $excelData[7];
                    $datas[$k]['level_efficiency_deposit'] = $excelData[8];
                    $datas[$k]['created_at']               = Carbon::now()->toDateTimeString();
                    $datas[$k]['updated_at']               = Carbon::now()->toDateTimeString();
                }
                $bool = LevelingPriceConfigure::insert($datas);

                if (! $bool) {
                    return response()->ajax(0, '导入失败');
                }
                return response()->ajax(1, '导入成功');
            } 
            return response()->ajax(0, '未找到上传文件！');
        } catch (Exception $e) {
            return response()->ajax(0, '文件上传错误，请重试!');
        }
    }
}

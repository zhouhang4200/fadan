<?php

namespace App\Http\Controllers\Backend\Config;

use Excel;
use DB, Log, Exception;
use App\Models\Game;
use App\Services\Show91;
use App\Models\ThirdGame;
use App\Models\ThirdArea;
use App\Models\ThirdServer;
use Illuminate\Http\Request;
use App\Models\GoodsTemplate;
use App\Exceptions\DailianException;
use App\Models\GoodsTemplateWidget;
use App\Http\Controllers\Controller;
use App\Models\GoodsTemplateWidgetValue;

// 配置我们的游戏区服和第三方游戏区服的关系
class ConfigController extends Controller
{
	/**
	 * 配置我们的游戏和第三方游戏的关系
	 * @return [type] [description]
	 */
    public function game(Request $request)
    {
    	$existGames = ThirdGame::pluck('game_id');
    	$games = Game::whereNotIn('id', $existGames)->get(); // 我们的游戏
    	$thirdGames = Show91::getGames(); // 91 所有的游戏

    	$gameArr = []; // 第三方游戏
    	foreach ($thirdGames['games'] as $k => $game) {
    		$gameArr[$game['id']] = $game['game_name'];
    	}
 
    	return view('backend.config.game', compact('gameArr', 'games'));
    }

    /**
     * 根据第三方平台号搜索对应平台下面的游戏
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getThirdGames(Request $request)
    {
    	try {
	    	$data = $request->data;
	    	$data['created_at'] = date('Y-m-d H:i:s', time());
	    	$data['updated_at'] = date('Y-m-d H:i:s', time());

	    	$has = ThirdGame::where('game_id', $data['game_id'])->first();

	    	if ($has) {
	    		throw new DailianException('数据已存在，请勿重复添加!');
	    	}
	    	ThirdGame::create($data);
    		return response()->ajax(1, '添加成功!');
    	} catch (DailianException $dailian) {
    		return response()->ajax(0, $dailian->getMessage());
    	} catch (Exception $e) {
    		return response()->ajax(0, '添加失败!');
    	} 
    }

    /**
     * 区添加列表
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function area(Request $request)
    {
    	$gameIds = ThirdGame::pluck('game_id'); // 我们的游戏
    	$games = Game::whereIn('id', $gameIds)->get();

    	return view('backend.config.area', compact('games'));
    }

    /**
     * 根据我们的游戏id，获取第三方和我们的区
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getAreas(Request $request)
    {
    	try {
    		$gameId = $request->game_id;
	    	// 获取我们的游戏区
	    	$goodsTemplateId = GoodsTemplate::where('game_id', $gameId)->where('service_id', 4)->value('id');

	        $goodsTemplateWidgetRegionId = GoodsTemplateWidget::where('goods_template_id', $goodsTemplateId)
	                            ->where('field_name', 'region')
	                            ->value('id');

	        $regions = GoodsTemplateWidgetValue::where('goods_template_widget_id', $goodsTemplateWidgetRegionId)
	                ->pluck('field_value', 'id')->toArray();

	        if (! $regions) {
	        	throw new DailianException('我们的区不存在!');
	        }
	        // 第三方游戏
	        $thirdGameId = ThirdGame::where('game_id', $gameId)->value('third_game_id');

	        $options = ['gid' => $thirdGameId];
	            
	        $res = Show91::getAreas($options);

	        $thirdAreas = [];
	        foreach ($res['areas'] as $key => $area) {
	            $thirdAreas[$area['id']] = $area['area_name'];
	        }

	        return json_encode(['status' => 1, 'our' => $regions, 'third' => $thirdAreas]);
    	} catch (DailianException $dailian) {
    		return response()->ajax(0, $dailian->getMessage());
    	} catch (Exception $e) {
    		return response()->ajax(0, '添加失败!');
    	} 	
    }

    /**
     * 写入third_areas表
     * @param Request $request [description]
     */
    public function addAreas(Request $request)
    {
    	DB::beginTransaction();
    	try {
	    	$datas = $request->data;
	    	$data['created_at'] = date('Y-m-d H:i:s', time());
	    	$data['updated_at'] = date('Y-m-d H:i:s', time());
	    	// 判断数据库是否已经存在相应的区
	    	$has = ThirdArea::where('game_id', $datas['game_id'])
		    	->where('area_id', $datas['area_id'])
		    	->where('third_area_id', $datas['third_area_id'])
		    	->first();

	    	if ($has) {
	    		throw new DailianException('数据库已存在该区，请勿重复添加');
	    	}
	    	ThirdArea::create($datas);
    	} catch (DailianException $dailian) {
    		DB::rollback();
    		return response()->ajax(0, $dailian->getMessage());
    	} catch (Exception $e) {
    		DB::rollback();
    		return response()->ajax(0, '添加失败!');
    		Log::info($e->getMessage());
    	} 
    	DB::commit();
    	return response()->ajax(1, '添加成功!');
    }

    /**
     * 服对应列表
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function server(Request $request)
    {
    	$existGames = ThirdGame::pluck('game_id');
    	$games = Game::whereIn('id', $existGames)->get(); // 我们的游戏

    	return view('backend.config.server', compact('games'));
    }

    /**
     * 获取我们的服和第三方的服
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getServers(Request $request)
    {
    	try {
    		$gameId = $request->game_id;
	    	// 获取我们的游戏区
	    	$goodsTemplateId = GoodsTemplate::where('game_id', $gameId)->where('service_id', 4)->value('id');
	    	$goodsTemplateWidgetRegionId = GoodsTemplateWidget::where('goods_template_id', $goodsTemplateId)
	                            ->where('field_name', 'region')
	                            ->value('id');
	        $goodsTemplateWidgetServerId = GoodsTemplateWidget::where('goods_template_id', $goodsTemplateId)
	                            ->where('field_name', 'serve')
	                            ->value('id');

	        // $servers = GoodsTemplateWidgetValue::where('goods_template_widget_id', $goodsTemplateWidgetServerId)
	        //         ->pluck('field_value', 'id')->toArray();
	        
	        $servers = DB::select("
	        	SELECT m.id, CONCAT(m.field_value, '(', n.field_value, ')') AS server_name FROM goods_template_widget_values m
				LEFT JOIN 
					(SELECT id, field_value FROM goods_template_widget_values
					WHERE goods_template_widget_id = '$goodsTemplateWidgetRegionId') n
				ON m.parent_id = n.id
				WHERE m.goods_template_widget_id = '$goodsTemplateWidgetServerId'
	        	");
	        
	        if (! $servers) {
	        	throw new DailianException('我们的服不存在!');
	        }

	        $ourServerArr = [];
	        foreach ($servers as $server) {
	        	$ourServerArr[$server->id] = $server->server_name;
	        }


	        // 第三方游戏
	        $thirdGameId = ThirdGame::where('game_id', $gameId)->value('third_game_id');

	        $options = ['gid' => $thirdGameId];
	            
	        $res = Show91::getAreas($options);

	        $thirdAreas = [];
	        foreach ($res['areas'] as $key => $area) {
	            $thirdAreas[$area['id']] = $area['area_name'];
	        }

	        // 遍历区找所有的服
	        $thirdServers = [];
	        foreach ($thirdAreas as $thirdAreaId => $thirdArea) {
	        	$options = ['aid' => $thirdAreaId];
	        	$res = Show91::getServer($options);

	        	foreach($res['servers'] as $thirdServerId => $thirdServer) {
	        		$thirdServers[$thirdServer['id']] = $thirdServer['server_name']."(".$thirdArea.")";
	        	}
	        }

	        return json_encode(['status' => 1, 'our' => $ourServerArr, 'third' => $thirdServers]);
    	} catch (DailianException $dailian) {
    		return response()->ajax(0, $dailian->getMessage());
    	} catch (Exception $e) {
    		return response()->ajax(0, '添加失败!');
    	} 
    }

    /**
     * 写入表
     * @param Request $request [description]
     */
    public function addServers(Request $request)
    {
    	DB::beginTransaction();
    	try {
	    	$datas = $request->data;
	    	$data['created_at'] = date('Y-m-d H:i:s', time());
	    	$data['updated_at'] = date('Y-m-d H:i:s', time());
	    	// 判断数据库是否已经存在相应的区
	    	$has = ThirdServer::where('game_id', $datas['game_id'])
		    	->where('server_id', $datas['server_id'])
		    	->where('third_server_id', $datas['third_server_id'])
		    	->first();

	    	if ($has) {
	    		throw new DailianException('数据库已存在该区，请勿重复添加');
	    	}
	    	ThirdServer::create($datas);
    	} catch (DailianException $dailian) {
    		DB::rollback();
    		return response()->ajax(0, $dailian->getMessage());
    	} catch (Exception $e) {
    		DB::rollback();
    		return response()->ajax(0, '添加失败!');
    	} 
    	DB::commit();
    	return response()->ajax(1, '添加成功!');
    }

    public function export(Request $request)
    {
    	$existGames = ThirdGame::pluck('game_id');
    	$games = Game::whereIn('id', $existGames)->get(); // 我们的游戏
    	$gameId = $request->game_id;

    	if ($gameId) {
	    	// 获取我们的游戏区
	    	$goodsTemplateId = GoodsTemplate::where('game_id', $gameId)->where('service_id', 4)->value('id');

	    	$goodsTemplateWidgetRegionId = GoodsTemplateWidget::where('goods_template_id', $goodsTemplateId)
                ->where('field_name', 'region')
                ->value('id');

	        $goodsTemplateWidgetServerId = GoodsTemplateWidget::where('goods_template_id', $goodsTemplateId)
                ->where('field_name', 'serve')
                ->value('id');

	        $ourAreas = GoodsTemplateWidgetValue::where('goods_template_widget_id', $goodsTemplateWidgetRegionId)
	                ->pluck('field_value', 'id')
	                ->toArray();

	        $servers = DB::select("
	        	SELECT m.id, CONCAT(m.field_value, '(', n.field_value, ')') AS server_name FROM goods_template_widget_values m
				LEFT JOIN 
					(SELECT id, field_value FROM goods_template_widget_values
					WHERE goods_template_widget_id = '$goodsTemplateWidgetRegionId') n
				ON m.parent_id = n.id
				WHERE m.goods_template_widget_id = '$goodsTemplateWidgetServerId'
	        	");
	        
	        if (! $servers) {
	        	throw new DailianException('我们的服不存在!');
	        }

	        // 我们的区
	        $ourAreaArr = [];
	        foreach ($ourAreas as $id => $area) {
	        	$ourAreaArr[$id]['area_id'] = $id;
	        	$ourAreaArr[$id]['area'] = $area;
	        }
			$ourAreaArr = array_values($ourAreaArr); // 我们的区

	        if (! $ourAreas) {
	        	throw new DailianException('我们的区不存在!');
	        }

	        $ourServers = collect($servers)->pluck('server_name', 'id');

	        $ourServerArr = [];
	        foreach ($ourServers as $ourServerId => $ourServer) {
	        	$ourServerArr[$ourServerId]['server_id'] = $ourServerId;
	        	$ourServerArr[$ourServerId]['server'] = $ourServer;
	        }
	        $ourServerArr = array_values($ourServerArr); // 我们的服
	        // 第三方服
	        $thirdGameId = ThirdGame::where('game_id', $gameId)->value('third_game_id');
	        $options = ['gid' => $thirdGameId];
	        $res = Show91::getAreas($options);

	        $thirdAreas = [];
	        $thirdAreaArr = [];
	        foreach ($res['areas'] as $key => $area) {
	            $thirdAreas[$area['id']] = $area['area_name'];
	            $thirdAreaArr[$area['id']]['third_area_id'] = $area['id'];
	            $thirdAreaArr[$area['id']]['third_area'] = $area['area_name'];
	        }
	        $thirdAreaArr = array_values($thirdAreaArr); // 第三方区
	        // 遍历区找所有的服
	        $thirdServers = [];
	        foreach ($thirdAreas as $thirdAreaId => $thirdArea) {
	        	$options = ['aid' => $thirdAreaId];
	        	$res = Show91::getServer($options);

	        	foreach($res['servers'] as $thirdServerId => $thirdServer) {
	        		$thirdServers[$thirdServer['id']]['third_server_id'] = $thirdServer['id'];
	        		$thirdServers[$thirdServer['id']]['third_server'] = $thirdServer['server_name']."(".$thirdArea.")";
	        	}
	        }
	        $thirdServers = array_values($thirdServers);
	        // 导出
	        return static::exports($gameId, $ourAreaArr, $thirdAreaArr, $ourServerArr, $thirdServers);
    	} else {
    		return view('backend.config.export', compact('games'));
    	}
    }

    /**
     * 奖惩列表导出.多分页导出
     * @param  [type] $filters [description]
     * @return [type]          [description]
     */
    public static function exports($gameId, $ourAreaArr, $thirdAreaArr, $ourServerArr, $thirdServers)
    {
    	$game = Game::find($gameId);
        $ourAreaTitle = ['序号', '我们的区名'];
        $thirdAreaTitle = ['序号', '第三方区名'];
        $ourServerTitle = ['序号', '我们的服名'];
        $thirdServerTitle = ['序号', '第三方服名'];

        $importArea = ['游戏id', '第三方id', '我们的区id', '第三方区id'];
        $importServer = ['游戏id', '第三方id', '我们的服id', '第三方服id'];

        array_unshift($ourAreaArr, $ourAreaTitle);
        array_unshift($thirdAreaArr, $thirdAreaTitle);
        array_unshift($ourServerArr, $ourServerTitle);
        array_unshift($thirdServers, $thirdServerTitle);

        Excel::create("$game->name($game->id)：$game->name", function ($excel) use ($ourAreaArr, $thirdAreaArr, $ourServerArr, $thirdServers, $importArea, $importServer) {
            $excel->sheet("我们的区", function ($sheet) use ($ourAreaArr) {
                $sheet->rows($ourAreaArr);
            });
            $excel->sheet("第三方区", function ($sheet) use ($thirdAreaArr) {
                $sheet->rows($thirdAreaArr);
            });
            $excel->sheet("我们的服", function ($sheet) use ($ourServerArr) {
                $sheet->rows($ourServerArr);
            });
            $excel->sheet("第三方服", function ($sheet) use ($thirdServers) {
                $sheet->rows($thirdServers);
            });
        })->export('xls');
    }

    /**
     * 导入excel数据
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function import(Request $request)
    {
    	try {
    		if ($request->hasFile('file')) {
	    		$file = $request->file('file');
	    		$path = $file->path();
		    	$res = [];
		    	$title = '';
		    	Excel::load($path, function ($reader) use (&$res, &$title) {
		            $reader = $reader->getSheet(0);
		            $title = $reader->getTitle();
		            $res = $reader->toArray();
		        });
		    	if (! $res) {
		    		return response()->ajax(0, '导入数据为空!');
		    	}

		    	if (! $title) {
		    		return response()->ajax(0, '请写明文件标题!');
		    	}
		    	switch ($title) {
		    		case '区':
		    			$thirdAreas = [];
		    			foreach ($res as $k => $thirdArea) {
		    				if (! $thirdArea) {
		    					return response()->ajax(0, '第'.($k+1).'行数据不存在!');
		    				}

		    				if (! $thirdArea[0] || !$thirdArea[1] || !$thirdArea[2] || !$thirdArea[3]) {
		    					return response()->ajax(0, '第'.($k+1).'行数据存在空值!');
		    				}

		    				if (! is_numeric($thirdArea[0]) || ! is_numeric($thirdArea[1]) || ! is_numeric($thirdArea[2]) || ! is_numeric($thirdArea[3])) {
		    					return response()->ajax(0, '第'.($k+1).'行数据存在不是一个整数的值!');
		    				}

		    				$has = ThirdArea::where('game_id', $thirdArea[0])
			    				->where('third_id', $thirdArea[1])
			    				->where('area_id', $thirdArea[2])
			    				->where('third_area_id', $thirdArea[3])
			    				->first();

			    			if ($has) {
			    				return response()->ajax(0, '第'.($k+1).'行数据已经存在!');
			    			}
			    			$thirdAreas[$k]['game_id'] = $thirdArea[0];
			    			$thirdAreas[$k]['third_id'] = $thirdArea[1];
			    			$thirdAreas[$k]['area_id'] = $thirdArea[2];
			    			$thirdAreas[$k]['third_area_id'] = $thirdArea[3];
			    			$thirdAreas[$k]['created_at'] = date('Y-m-d H:i:s', time());
			    			$thirdAreas[$k]['updated_at'] = date('Y-m-d H:i:s', time());
		    			}
		    			$bool = ThirdArea::insert($thirdAreas);

		    			if ($bool) {
		    				return response()->ajax(1, '导入成功');
		    			} else {
		    				return response()->ajax(0, '导入失败');
		    			}
		    		break;
		    		case '服':
		    			$thirdServers = [];
		    			foreach ($res as $k => $thirdServer) {
		    				if (! $thirdServer) {
		    					return response()->ajax(0, '第'.($k+1).'行数据不存在!');
		    				}

		    				if (! $thirdServer[0] || !$thirdServer[1] || !$thirdServer[2] || !$thirdServer[3]) {
		    					return response()->ajax(0, '第'.($k+1).'行数据存在空值!');
		    				}

		    				if (! is_numeric($thirdServer[0]) || ! is_numeric($thirdServer[1]) || ! is_numeric($thirdServer[2]) || ! is_numeric($thirdServer[3])) {
		    					return response()->ajax(0, '第'.($k+1).'行数据存在不是一个整数的值!');
		    				}

		    				$has = ThirdServer::where('game_id', $thirdServer[0])
			    				->where('third_id', $thirdServer[1])
			    				->where('server_id', $thirdServer[2])
			    				->where('third_server_id', $thirdServer[3])
			    				->first();

			    			if ($has) {
			    				return response()->ajax(0, '第'.($k+1).'行数据已经存在!');
			    			}
			    			$thirdServers[$k]['game_id'] = $thirdServer[0];
			    			$thirdServers[$k]['third_id'] = $thirdServer[1];
			    			$thirdServers[$k]['server_id'] = $thirdServer[2];
			    			$thirdServers[$k]['third_server_id'] = $thirdServer[3];
			    			$thirdServers[$k]['created_at'] = date('Y-m-d H:i:s', time());
			    			$thirdServers[$k]['updated_at'] = date('Y-m-d H:i:s', time());
		    			}
		    			$bool = ThirdServer::insert($thirdServers);

		    			if ($bool) {
		    				return response()->ajax(1, '导入成功');
		    			} else {
		    				return response()->ajax(0, '导入失败');
		    			}
		    		break;
		    		default:
		    			return response()->ajax(0, '文件标题不正确!');
		    	}
	    	} else {
	    		return response()->ajax(0, '未找到上传文件！');
	    	}
    	} catch (Exception $e) {
    		throw new Exception($e->getMessage());
    	}
    }
}

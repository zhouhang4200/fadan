<?php

namespace App\Http\Controllers\Backend\Config;

use DB;
use Log;
use Excel;
use Exception;
use App\Models\Game;
use GuzzleHttp\Client;
use App\Services\Show91;
use App\Models\ThirdGame;
use App\Models\ThirdArea;
use App\Models\ThirdServer;
use Illuminate\Http\Request;
use App\Models\GoodsTemplate;
use App\Services\DailianMama;
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
    	$third = $request->third;

    	if ($gameId && $third) {
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
	        // 带区名的服
	   //      $servers = DB::select("
	   //      	SELECT m.id, CONCAT(m.field_value, '(', n.field_value, ')') AS server_name FROM goods_template_widget_values m
				// LEFT JOIN 
				// 	(SELECT id, field_value FROM goods_template_widget_values
				// 	WHERE goods_template_widget_id = '$goodsTemplateWidgetRegionId') n
				// ON m.parent_id = n.id
				// WHERE m.goods_template_widget_id = '$goodsTemplateWidgetServerId'
	   //      	");
	        // 一个包含我们的游戏id，我们的区id，区名，服id，服名的集合, 样子如下
	        /**  5 => array:5 [
			    "game_id" => 78
			    "area_id" => 2153
			    "area_name" => "电信"
			    "server_id" => 2162
			    "server_name" => "皮尔特沃夫"
			 ]*/
	       	$servers = DB::select("
	        	select y.game_id, x.area_id, x.area_name, x.server_id, x.server_name from (select j.*, k.goods_template_id from (SELECT m.goods_template_widget_id, m.id as server_id, m.field_value AS server_name, n.id as area_id, n.field_value as area_name 
	        	FROM goods_template_widget_values m
				LEFT JOIN 
					(SELECT id, field_value 
					FROM goods_template_widget_values
					WHERE goods_template_widget_id = '$goodsTemplateWidgetRegionId') n
				ON m.parent_id = n.id
				WHERE m.goods_template_widget_id = '$goodsTemplateWidgetServerId'
				) j 
				left join goods_template_widgets k
				on j.goods_template_widget_id = k.id
				) x
				left join goods_templates y
				on x.goods_template_id = y.id
	        ");

	        // 判断我们的数据库是否存在值
	        if (! $servers) {
	        	throw new DailianException('我们的服不存在!');
	        }

	        // 将数据库查到的值转为纯数组
	        $ourServers = array_map(function ($server) {
	        	return (array) $server;
	        }, $servers);

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

	        // $ourServers = collect($servers)->pluck('server_name', 'id');

	        // $ourServerArr = [];
	        // foreach ($ourServers as $ourServerId => $ourServer) {
	        // 	$ourServerArr[$ourServerId]['server_id'] = $ourServerId;
	        // 	$ourServerArr[$ourServerId]['server'] = $ourServer;
	        // }
	        // $ourServerArr = array_values($ourServerArr); // 我们的服
	        // 第三方服
	        $thirdGameId = ThirdGame::where('game_id', $gameId)->value('third_game_id');
	        $options = ['gid' => $thirdGameId];

	        switch ($request->third) {
	        	case 1: // 91平台
	        		$res = Show91::getAreas($options);
	        	break;
	        	case 2: // 代练妈妈
	        		// $client = new Client;
			        // $response = $client->request('GET', config('dailianmama.url.gameInfo'));
			        // $res = $response->getBody()->getContents();

			        // if (! $res) {
			        // 	throw new DailianException('请求接口错误!');
			        // }
			        // $res = json_decode($res, true);
	        	break;
	        }

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
	        foreach ($thirdAreas as $thirdAreaId => &$thirdArea) {
	        	$options = ['aid' => $thirdAreaId];
	        	$res = Show91::getServer($options);

	        	foreach($res['servers'] as $thirdServerId => $thirdServer) {
	        		$thirdServers[$thirdServer['id']]['third_area_id'] = $thirdAreaId;
	        		$thirdServers[$thirdServer['id']]['third_area_name'] = $thirdArea;
	        		$thirdServers[$thirdServer['id']]['third_server_id'] = $thirdServer['id'];
	        		$thirdServers[$thirdServer['id']]['third_server'] = $thirdServer['server_name'];
	        	}
	        }
	        $thirdServers = array_values($thirdServers);
	        // dd($gameId, $ourAreaArr, $thirdAreaArr, $ourServers, $thirdServers);
	        // 导出
	        // return static::exports($gameId, $ourAreaArr, $thirdAreaArr, $ourServerArr, $thirdServers);
	        return static::exports($gameId, $ourAreaArr, $thirdAreaArr, $ourServers, $thirdServers);
    	} else {
    		return view('backend.config.export', compact('games'));
    	}
    }

    /**
     * @param  [type] $filters [description]
     * @return [type]          [description]
     */
    public static function exports($gameId, $ourAreaArr, $thirdAreaArr, $ourServerArr, $thirdServers)
    {
    	$game = Game::find($gameId);
        $ourAreaTitle = ['区ID', '区名'];
        $thirdAreaTitle = ['第三方区ID', '第三方区名'];
        // $ourServerTitle = ['序号', '代练平台服名'];
        $ourServerTitle = ['游戏ID', '区ID', '区名', '服ID', '服名'];
        $thirdServerTitle = ['第三方区ID', '第三方区名', '第三方服ID', '第三方服名'];
        // $thirdServerTitle = ['序号', '第三方服名'];

        $importArea = [['游戏ID', '第三方ID', '代练平台区ID', '第三方区ID'], ['0', '0', '0', '0']];
        $importServer = [['游戏ID', '第三方ID', '代练平台服ID', '第三方服ID'], ['0', '0', '0', '0']];

        array_unshift($ourAreaArr, $ourAreaTitle);
        array_unshift($thirdAreaArr, $thirdAreaTitle);
        array_unshift($ourServerArr, $ourServerTitle);
        array_unshift($thirdServers, $thirdServerTitle);

        Excel::create("$game->name($game->id)", function ($excel) use ($ourAreaArr, $thirdAreaArr, $ourServerArr, $thirdServers, $importArea, $importServer) {
            $excel->sheet("代练平台区", function ($sheet) use ($ourAreaArr) {
                $sheet->rows($ourAreaArr);
            });
            $excel->sheet("第三方区", function ($sheet) use ($thirdAreaArr) {
                $sheet->rows($thirdAreaArr);
            });
            $excel->sheet("代练平台服", function ($sheet) use ($ourServerArr) {
                $sheet->rows($ourServerArr);
            });
            $excel->sheet("第三方服", function ($sheet) use ($thirdServers) {
                $sheet->rows($thirdServers);
            });
            /**  可以优化的,先放着 
            $excel->sheet("区", function ($sheet) use ($importArea) {
                $sheet->rows($importArea);
            });
            $excel->sheet("服", function ($sheet) use ($importServer) {
                $sheet->rows($importServer);
            });
            **/
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
    			/** @var [type] [可以优化的，先放着] 
    			$file = $request->file('file');
	    		$path = $file->path();
	    		$areaArr = [];
	    		$serverArr = [];
	    		$areaTitle = '';
	    		$serverTitle = '';
	    		Excel::load($path, function ($reader) use (&$areaArr, &$serverArr, &$areaTitle, &$serverTitle) {
		            $readerArea = $reader->getSheet(0);
		            $readerServer= $reader->getSheet(1);
		            $areaTitle = $readerArea->getTitle(0);
		            $serverTitle = $readerServer->getTitle(1);
		            $areaArr = $readerArea->toArray();
		            $serverArr = $readerServer->toArray();
		        });
		        dd($areaTitle, $serverTitle, $areaArr, $serverArr);
	    		if (! $areaTitle || $areaTitle != '区') {
	    			return response()->ajax(0, '第一页页脚名称必须为‘区’!');
	    		}

	    		if (! $serverTitle || $serverTitle != '服') {
	    			return response()->ajax(0, '第二页页脚名称必须为‘服’!');
	    		}

	    		if (! $areaArr) {
	    			return response()->ajax(0, '导入区数据为空!');
	    		}

	    		if (! $serverArr) {
	    			return response()->ajax(0, '导入服数据为空!');
	    		}
				**/
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
		    				if (count($thirdArea) == 0 || array_sum($thirdArea) == 0) {
		    					continue;
		    				}

		    				if (! is_numeric($thirdArea[0]) || ! is_numeric($thirdArea[1])) {
		    					return response()->ajax(0, '第'.($k+1).'行数据必须全部为数字且不能为空!');
		    				}

		    				$has = ThirdArea::where('game_id', $thirdArea[0])
			    				->where('third_id', $thirdArea[1])
			    				->where('area_id', $thirdArea[2])
			    				->where('third_area_id', $thirdArea[4])
			    				->first();

			    			if ($has) {
			    				return response()->ajax(0, '第'.($k+1).'行数据已经存在!');
			    			}
			    			$thirdAreas[$k]['game_id'] = $thirdArea[0];
			    			$thirdAreas[$k]['third_id'] = $thirdArea[1];
			    			$thirdAreas[$k]['area_id'] = $thirdArea[2];
			    			$thirdAreas[$k]['area_name'] = $thirdArea[3];
			    			$thirdAreas[$k]['third_area_id'] = $thirdArea[4];
			    			$thirdAreas[$k]['third_area_name'] = $thirdArea[5];
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
		    				// 去除第一行标题和最后一行空数据
		    				if (count($thirdServer) == 0 || array_sum($thirdServer) == 0) {
		    					continue;
		    				}
		    				// 检查必填项是否为空
		    				if (! is_numeric($thirdServer[0]) || ! is_numeric($thirdServer[1])) {
		    					return response()->ajax(0, '第'.($k+1).'行数据必须全部为数字且不能为空!');
		    				}
		    				// 检查是否重复添加
		    				$has = ThirdServer::where('game_id', $thirdServer[0])
			    				->where('third_id', $thirdServer[1])
			    				->where('server_id', $thirdServer[2])
			    				->where('third_server_id', $thirdServer[4])
			    				->first();

			    			if ($has) {
			    				return response()->ajax(0, '第'.($k+1).'行数据已经存在!');
			    			}

			    			// 写入数据
			    			$thirdServers[$k]['game_id'] = $thirdServer[0];
			    			$thirdServers[$k]['third_id'] = $thirdServer[1];
			    			$thirdServers[$k]['server_id'] = $thirdServer[2];
			    			$thirdServers[$k]['server_name'] = $thirdServer[3];
			    			$thirdServers[$k]['third_server_id'] = $thirdServer[4];
				    		$thirdServers[$k]['third_server_name'] = $thirdServer[5];
			    			$thirdServers[$k]['created_at'] = date('Y-m-d H:i:s', time());
			    			$thirdServers[$k]['updated_at'] = date('Y-m-d H:i:s', time());
			    			// 去除括号
			    			preg_match('~(.*?)(\(.*\))~', $thirdServer[3], $matchServerNames);
			    			preg_match('~(.*?)(\(.*\))~', $thirdServer[5], $matchThirdServerNames);

			    			if ($matchServerNames) {
				    			$thirdServers[$k]['server_name'] = $matchServerNames[1];
			    			}

			    			if ($matchThirdServerNames) {
				    			$thirdServers[$k]['third_server_name'] = $matchThirdServerNames[1];
			    			}
		    			}
		    			// 插入数据
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

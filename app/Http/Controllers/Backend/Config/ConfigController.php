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
use App\Models\OrderRegionCorrespondence;

// 配置我们的游戏区服和第三方游戏区服的关系
class ConfigController extends Controller
{
	/**
	 * 配置我们的游戏和第三方游戏的关系
	 * @return [type] [description]
	 */
    public function game(Request $request)
    {
    	// 代练平台的游戏
    	$games = DB::select("SELECT a.game_id as id, b.name FROM goods_templates a
			LEFT JOIN games b
			ON a.game_id = b.id 
			WHERE a.service_id = 4");

    	return view('backend.config.game', compact('games'));
    }

    public function getThirdGames(Request $request)
    {
    	// dd($request->third_id);
    	// 根据平台自动匹配对应的游戏
    	switch ($request->third_id) {
    		case 1:
		    	$thirdGames = Show91::getGames(); // 91 所有的游戏

		    	$gameArr = []; // 第三方游戏
		    	foreach ($thirdGames['games'] as $k => $game) {
		    		$gameArr[$game['id']] = $game['id'].'-'.$game['game_name'];
		    	}
	    		return json_encode(['status' => 1, 'third_games' => $gameArr]);
    			break;
    		case 2:
    			// 这是代练妈妈所有的游戏集合
        		$client = new Client;
		        $response = $client->request('GET', config('dailianmama.url.gameInfo'));
		        $res = $response->getBody()->getContents();

		        if (! $res) {
		        	return response()->ajax(0, '请求接口错误!');
		        }
		        $allGameInfos = json_decode($res, true);

		        $thirdGames = [];
		        foreach ($allGameInfos as $k => $allGameInfo) {
		        	$thirdGames[$allGameInfo['id']] = $allGameInfo['id'].'-'.$allGameInfo['name'];
		        }
		        return ['status' => 1, 'third_games' => $thirdGames];
    			break;
    		default:
    			break;
    	}
    }
    /**
     * 根据第三方平台号搜索对应平台下面的游戏
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function thirdGames(Request $request)
    {
    	try {
	    	$data['third_id'] = $request->data['third_id'];
	    	$data['created_at'] = date('Y-m-d H:i:s', time());
	    	$data['updated_at'] = date('Y-m-d H:i:s', time());
		    $data['game_id'] = explode('-', $request->data['game_id'])[0];
		    $data['game_name'] = explode('-', $request->data['game_id'])[1];
		    $data['third_game_id'] = explode('-', $request->data['third_game_id'])[0];
		    $data['third_game_name'] = explode('-', $request->data['third_game_id'])[1];

		    // 最开的是91没有写游戏名
	    	$thirdGame = ThirdGame::where('game_id', $data['game_id'])
		    	->where('third_id', $data['third_id'])
		    	->first();
		    // 如果没有写游戏名，name写进去
		     if ($thirdGame) {
		    	if (! $thirdGame->game_name || ! $thirdGame->third_game_name) {
		    		$thirdGame->game_name = $data['game_name'];
		    		$thirdGame->third_game_name = $data['third_game_name'];
		    		$thirdGame->save();
		    	} else {
		    		return response()->ajax(0, '数据已存在，请勿重复添加!');
		    	}
		     }
	    	ThirdGame::create($data);
    		return response()->ajax(1, '添加成功!');
    	} catch (DailianException $dailian) {
    		return response()->ajax(0, $dailian->getMessage());
    	} catch (Exception $e) {
    		return response()->ajax(0, '添加失败!');
    	} 
    }

    public function export(Request $request)
    {
    	// 第三方配置了的我们的游戏
    	$existGames = ThirdGame::pluck('game_id'); 
    	$games = Game::whereIn('id', $existGames)->get(); // 我们的游戏
    	$gameId = $request->game_id; // 我们的游戏id
    	$third = $request->third; // 第三方平台

    	if ($gameId && $third) {
	    	// 获取商品模板
	    	$goodsTemplateId = GoodsTemplate::where('game_id', $gameId)->where('service_id', 4)->value('id');
	    	// 商品模板区id
	    	$goodsTemplateWidgetRegionId = GoodsTemplateWidget::where('goods_template_id', $goodsTemplateId)
                ->where('field_name', 'region')
                ->value('id');
            // 商品模板服id
	        $goodsTemplateWidgetServerId = GoodsTemplateWidget::where('goods_template_id', $goodsTemplateId)
                ->where('field_name', 'serve')
                ->value('id');
            // 获取我们的区
	        $ourAreas = GoodsTemplateWidgetValue::where('goods_template_widget_id', $goodsTemplateWidgetRegionId)
	                ->pluck('field_value', 'id')
	                ->toArray();
	                
	        if (! $ourAreas) {
	        	return '我们的区不存在!';
	        }

	        $ourAreaArr = [];
	        foreach ($ourAreas as $id => $area) {
	        	$ourAreaArr[$id]['area_id'] = $id;
	        	$ourAreaArr[$id]['area'] = $area;
	        }
			$ourAreas = array_values($ourAreaArr);
	        // 获取我们的游戏服，包含游戏id，游戏名字的集合
	       	$servers = DB::select("
	       		SELECT p.id, p.name, o.area_id, o.area_name, o.server_id, o.server_name
	       		FROM
		        	(SELECT y.game_id, x.area_id, x.area_name, x.server_id, x.server_name 
		        		FROM (SELECT j.*, k.goods_template_id FROM (SELECT m.goods_template_widget_id, m.id AS server_id, m.field_value AS server_name, n.id AS area_id, n.field_value AS area_name 
		        	FROM goods_template_widget_values m
					LEFT JOIN 
						(SELECT id, field_value 
						FROM goods_template_widget_values
						WHERE goods_template_widget_id = '$goodsTemplateWidgetRegionId') n
					ON m.parent_id = n.id
					WHERE m.goods_template_widget_id = '$goodsTemplateWidgetServerId'
					) j 
					LEFT JOIN goods_template_widgets k
					ON j.goods_template_widget_id = k.id
					) X
					LEFT JOIN goods_templates Y
					ON x.goods_template_id = y.id) o
					LEFT JOIN games p
				ON o.game_id = p.id
	        ");

	        // 判断我们的数据库是否存在服
	        if (! $servers) {
	        	return '我们的服不存在!';
	        }
	        // 将数据库查到的值转为纯数组
	        $ourServers = array_map(function ($server) {
	        	return (array) $server;
	        }, $servers);

	        switch ($request->third) {
	        	case 1: // 91平台
	        		// 第三方游戏信息
			        $thirdGame = ThirdGame::where('game_id', $gameId)
			        	->where('third_id', 1)
			        	->first();
			        // 获取第三方游戏区
	        		$res = Show91::getAreas(['gid' => $thirdGame->third_game_id]);
	        		// 第三方区的二维数组
			        $thirdAreas = [];
			        foreach ($res['areas'] as $key => $area) {
			            $thirdAreas[$area['id']]['third_area_id'] = $area['id'];
			            $thirdAreas[$area['id']]['third_area'] = $area['area_name'];
			        }
			        $thirdAreas = array_values($thirdAreas); 
			        // 遍历区找所有的服
			        $thirdServers = [];
			        foreach ($thirdAreas as $thirdAreaId => $thirdArea) {
			        	$options = ['aid' => $thirdArea['third_area_id']];
			        	$res = Show91::getServer($options);
			        	// 用区来找91下面的服
			        	foreach($res['servers'] as $thirdServerId => $thirdServer) {
			        		$thirdServers[$thirdServer['id']]['third_game_id'] = $thirdGame->third_game_id;
			        		$thirdServers[$thirdServer['id']]['third_game_name'] = $thirdGame->third_game_name;
			        		$thirdServers[$thirdServer['id']]['third_area_id'] = $thirdArea['third_area_id'];
			        		$thirdServers[$thirdServer['id']]['third_area_name'] = $thirdArea['third_area'];
			        		$thirdServers[$thirdServer['id']]['third_server_id'] = $thirdServer['id'];
			        		$thirdServers[$thirdServer['id']]['third_server'] = $thirdServer['server_name'];
			        	}
			        }
			        $thirdServers = array_values($thirdServers);
	        		break;
	        	case 2: // 代练妈妈
	        		// 这是代练妈妈所有的游戏集合
	        		$client = new Client;
			        $response = $client->request('GET', config('dailianmama.url.gameInfo'));
			        $res = $response->getBody()->getContents();

			        if (! $res) {
			        	return '请求接口错误!';
			        }
			        $allGameInfos = json_decode($res, true);
	        		// 第一步获取代练妈妈的游戏id
	        		$dailianMamaGameId = ThirdGame::where('third_id', 2)
	        			->where('game_id', $request->game_id)
	        			->value('third_game_id');
	        		// 根据代练妈妈游戏id找对应的区服信息
	        		switch ($dailianMamaGameId) {
	        			case 'G604'; // 王者荣耀
	        				$index = 0;
	        				break;
	        			case 'G603': // 英雄联盟
	        				$index = 1; // 这是游戏总信息里面游戏的下标
	        				break;
	        			case 'G614'; // QQ飞车手游
	        				$index = 2;
	        				break;
	        			case 'G617'; // 全军出击
	        				$index = 3;
	        				break;
	        			case 'G618'; // 刺激战场
	        				$index = 4;
	        				break;
	        			case 'G615'; // 决战平安京
	        				$index = 5;
	        				break;
	        			case 'G606'; // 球球大作战
	        				$index = 6;
	        				break;
	        			case 'G616'; // CF:枪战王者
	        				$index = 7;
	        				break;
	        			case 'G613'; // 绝地求生
	        				$index = 8;
	        				break;
	        			case 'G605'; // 守望先锋
	        				$index = 9;
	        				break;
	        			default:
	        				return '请联系运营配置代练妈妈游戏!';
	        				break;
	        		}
	        		// 获取代练妈妈的区和服
	        			// 代练类型,以下所有信息均为代练妈妈的游戏信息
        				$dlTypes = $allGameInfos[$index]['dltype']; // 代练类型,一维数组
        				$gameName = $allGameInfos[$index]['name']; // 游戏名字
        				$areaAndServers = $allGameInfos[$index]['list']; // 区和游戏的三维数组
        				// dd($areaAndServers);
        				// 获取代练妈妈的游戏区
        				$thirdAreas = []; // 二维数组
        				$dailianMamaServers = []; // 二维数组
        				foreach ($areaAndServers as $k => $areas) {
        					// 去掉第一个全区全服
        					if (0 == $k) {
        						continue;
        					}
        					$thirdAreas[$k]['third_area_id'] = $areas['id'];
        					$thirdAreas[$k]['third_area_name'] = $areas['name'];
        					// 获取对应区下面的服
        					foreach ($areas['list'] as $key => $server) {
        						// 去除第一个全服
        						if (0 == $key) {
        							continue;
        						}
        						$dailianMamaServers[$k][$key]['third_game_id'] = $areas['gameid'];
        						$dailianMamaServers[$k][$key]['third_game_name'] = $gameName;
        						$dailianMamaServers[$k][$key]['third_area_id'] = $areas['id'];
        						$dailianMamaServers[$k][$key]['third_area_name'] = $areas['name'];
        						$dailianMamaServers[$k][$key]['third_servre_id'] = $server['id'];
        						$dailianMamaServers[$k][$key]['third_server_name'] = $server['name'];
        					}

        				}
        				// 将服缩小为二维数组
        				$thirdServers = [];
        				foreach ($dailianMamaServers as $thirdServer) {
        					foreach ($thirdServer as $server) {
        						$thirdServers[] = $server;
        					}
        				}
	        		break;
	        }

	        return static::exports($gameId, $ourAreas, $thirdAreas, $ourServers, $thirdServers);
    	} else {
    		return view('backend.config.export', compact('games'));
    	}
    }

    /**
     * @param  [type] $filters [description]
     * @return [type]          [description]
     */
    public static function exports($gameId, $ourAreas, $thirdAreas, $ourServers, $thirdServers)
    {
    	$game = Game::find($gameId);
        $ourAreaTitle = ['我们的区ID', '我们的区名'];
        $thirdAreaTitle = ['第三方区ID', '第三方区名'];
        $ourServerTitle = ['我们的游戏ID', '我们的游戏名', '我们的区ID', '我们的区名', '我们的服ID', '我们的服名'];
        $thirdServerTitle = ['第三方游戏ID', '第三方游戏名', '第三方区ID', '第三方区名', '第三方服ID', '第三方服名'];

        array_unshift($ourAreas, $ourAreaTitle);
        array_unshift($thirdAreas, $thirdAreaTitle);
        array_unshift($ourServers, $ourServerTitle);
        array_unshift($thirdServers, $thirdServerTitle);

        Excel::create("$game->name($game->id)", function ($excel) use ($ourAreas, $thirdAreas, $ourServers, $thirdServers) {
            $excel->sheet("代练平台区", function ($sheet) use ($ourAreas) {
                $sheet->rows($ourAreas);
            });
            $excel->sheet("第三方区", function ($sheet) use ($thirdAreas) {
                $sheet->rows($thirdAreas);
            });
            $excel->sheet("代练平台服", function ($sheet) use ($ourServers) {
                $sheet->rows($ourServers);
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
		    	$excelDatas = [];
		    	$title = '';
		    	Excel::load($path, function ($reader) use (&$excelDatas, &$title) {
		            $reader = $reader->getSheet(0);
		            $title = $reader->getTitle();
		            $excelDatas = $reader->toArray();
		        });

		    	if (! $excelDatas || ! $title) {
		    		return response()->ajax(0, '导入数据为空或文件标题缺失!');
		    	}

		    	switch ($title) {
		    		case '区服配置':
		    			$datas = [];
		    			foreach ($excelDatas as $k => $excelData) {
		    				// 检测数据是否为龙或为全字符标题
		    				if (count($excelData) == 0 || array_sum($excelData) == 0) {
		    					continue;
		    				}

		    				if (! is_numeric($excelData[0]) || ! is_numeric($excelData[2]) || ! is_numeric($excelData[5]) || ! is_numeric($excelData[7])) {
		    					return response()->ajax(0, '第'.($k+1).'行数据必须全部为数字且不能为空!');
		    				}
		    				// 判断数据是否存在
		    				$ifExists = OrderRegionCorrespondence::where('game_id', $excelData[0])
			    				->where('third', $excelData[2])
			    				->where('third_game_id', $excelData[3])
			    				->where('area_id', $excelData[5])
			    				->where('server_id', $excelData[9])
			    				->first();

			    			if ($ifExists) {
			    				return response()->ajax(0, '第'.($k+1).'行数据已经存在!');
			    			}
			    			// 数组
							$datas[$k]['game_id']           = $excelData[0];
							$datas[$k]['game_name']         = $excelData[1];
							$datas[$k]['third']             = $excelData[2];
							$datas[$k]['third_game_id']     = $excelData[3];
							$datas[$k]['third_game_name']   = $excelData[4];
							$datas[$k]['area_id']           = $excelData[5];
							$datas[$k]['area_name']         = $excelData[6];
							$datas[$k]['server_id']         = $excelData[7];
							$datas[$k]['server_name']       = $excelData[8];
							$datas[$k]['third_area_id']     = $excelData[9];
							$datas[$k]['third_area_name']   = $excelData[10];
							$datas[$k]['third_server_id']   = $excelData[11];
							$datas[$k]['third_server_name'] = $excelData[12];
		    			}
		    			$yes = OrderRegionCorrespondence::insert($datas);

		    			if (! $yes) {
		    				return response()->ajax(0, '导入失败');
		    			}
		    			return response()->ajax(1, '导入成功');
		    			break;
		    		default:
		    			return response()->ajax(0, '请正确填写数据信息!');
		    	}
	    	} else {
	    		return response()->ajax(0, '未找到上传文件！');
	    	}
    	} catch (Exception $e) {
    		return response()->ajax(0, '未知错误!');
    	}
    	// try {
    	// 	if ($request->hasFile('file')) {
	    // 		$file = $request->file('file');
	    // 		$path = $file->path();
		   //  	$res = [];
		   //  	$title = '';
		   //  	Excel::load($path, function ($reader) use (&$res, &$title) {
		   //          $reader = $reader->getSheet(0);
		   //          $title = $reader->getTitle();
		   //          $res = $reader->toArray();
		   //      });
		   //  	if (! $res) {
		   //  		return response()->ajax(0, '导入数据为空!');
		   //  	}

		   //  	if (! $title) {
		   //  		return response()->ajax(0, '请写明文件标题!');
		   //  	}
		   //  	switch ($title) {
		   //  		case '区':
		   //  			$thirdAreas = [];
		   //  			foreach ($res as $k => $thirdArea) {
		   //  				if (count($thirdArea) == 0 || array_sum($thirdArea) == 0) {
		   //  					continue;
		   //  				}

		   //  				if (! is_numeric($thirdArea[0]) || ! is_numeric($thirdArea[1])) {
		   //  					return response()->ajax(0, '第'.($k+1).'行数据必须全部为数字且不能为空!');
		   //  				}

		   //  				$has = ThirdArea::where('game_id', $thirdArea[0])
			  //   				->where('third_id', $thirdArea[1])
			  //   				->where('area_id', $thirdArea[2])
			  //   				->where('third_area_id', $thirdArea[4])
			  //   				->first();

			  //   			if ($has) {
			  //   				return response()->ajax(0, '第'.($k+1).'行数据已经存在!');
			  //   			}
			  //   			$thirdAreas[$k]['game_id'] = $thirdArea[0];
			  //   			$thirdAreas[$k]['third_id'] = $thirdArea[1];
			  //   			$thirdAreas[$k]['area_id'] = $thirdArea[2];
			  //   			$thirdAreas[$k]['area_name'] = $thirdArea[3];
			  //   			$thirdAreas[$k]['third_area_id'] = $thirdArea[4];
			  //   			$thirdAreas[$k]['third_area_name'] = $thirdArea[5];
			  //   			$thirdAreas[$k]['created_at'] = date('Y-m-d H:i:s', time());
			  //   			$thirdAreas[$k]['updated_at'] = date('Y-m-d H:i:s', time());
		   //  			}
		   //  			$bool = ThirdArea::insert($thirdAreas);

		   //  			if ($bool) {
		   //  				return response()->ajax(1, '导入成功');
		   //  			} else {
		   //  				return response()->ajax(0, '导入失败');
		   //  			}
		   //  			break;
		   //  		case '服':
		   //  			$thirdServers = [];
		   //  			foreach ($res as $k => $thirdServer) {
		   //  				// 去除第一行标题和最后一行空数据
		   //  				if (count($thirdServer) == 0 || array_sum($thirdServer) == 0) {
		   //  					continue;
		   //  				}
		   //  				// 检查必填项是否为空
		   //  				if (! is_numeric($thirdServer[0]) || ! is_numeric($thirdServer[1])) {
		   //  					return response()->ajax(0, '第'.($k+1).'行数据必须全部为数字且不能为空!');
		   //  				}
		   //  				// 检查是否重复添加
		   //  				$has = ThirdServer::where('game_id', $thirdServer[0])
			  //   				->where('third_id', $thirdServer[1])
			  //   				->where('server_id', $thirdServer[2])
			  //   				->where('third_server_id', $thirdServer[4])
			  //   				->first();

			  //   			if ($has) {
			  //   				return response()->ajax(0, '第'.($k+1).'行数据已经存在!');
			  //   			}

			  //   			// 写入数据
			  //   			$thirdServers[$k]['game_id'] = $thirdServer[0];
			  //   			$thirdServers[$k]['third_id'] = $thirdServer[1];
			  //   			$thirdServers[$k]['server_id'] = $thirdServer[2];
			  //   			$thirdServers[$k]['server_name'] = $thirdServer[3];
			  //   			$thirdServers[$k]['third_server_id'] = $thirdServer[4];
				 //    		$thirdServers[$k]['third_server_name'] = $thirdServer[5];
			  //   			$thirdServers[$k]['created_at'] = date('Y-m-d H:i:s', time());
			  //   			$thirdServers[$k]['updated_at'] = date('Y-m-d H:i:s', time());
			  //   			// 去除括号
			  //   			preg_match('~(.*?)(\(.*\))~', $thirdServer[3], $matchServerNames);
			  //   			preg_match('~(.*?)(\(.*\))~', $thirdServer[5], $matchThirdServerNames);

			  //   			if ($matchServerNames) {
				 //    			$thirdServers[$k]['server_name'] = $matchServerNames[1];
			  //   			}

			  //   			if ($matchThirdServerNames) {
				 //    			$thirdServers[$k]['third_server_name'] = $matchThirdServerNames[1];
			  //   			}
		   //  			}
		   //  			// 插入数据
		   //  			$bool = ThirdServer::insert($thirdServers);

		   //  			if ($bool) {
		   //  				return response()->ajax(1, '导入成功');
		   //  			} else {
		   //  				return response()->ajax(0, '导入失败');
		   //  			}
		   //  			break;
		   //  		default:
		   //  			return response()->ajax(0, '文件标题不正确!');
		   //  	}
	    // 	} else {
	    // 		return response()->ajax(0, '未找到上传文件！');
	    // 	}
    	// } catch (Exception $e) {
    	// 	throw new Exception($e->getMessage());
    	// }
    }

        /**
     * 区添加列表
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    // public function area(Request $request)
    // {
    // 	$gameIds = ThirdGame::pluck('game_id'); // 我们的游戏
    // 	$games = Game::whereIn('id', $gameIds)->get();

    // 	return view('backend.config.area', compact('games'));
    // }

    /**
     * 根据我们的游戏id，获取第三方和我们的区
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    // public function getAreas(Request $request)
    // {
    // 	try {
    // 		$gameId = $request->game_id;
	   //  	// 获取我们的游戏区
	   //  	$goodsTemplateId = GoodsTemplate::where('game_id', $gameId)->where('service_id', 4)->value('id');

	   //      $goodsTemplateWidgetRegionId = GoodsTemplateWidget::where('goods_template_id', $goodsTemplateId)
	   //                          ->where('field_name', 'region')
	   //                          ->value('id');

	   //      $regions = GoodsTemplateWidgetValue::where('goods_template_widget_id', $goodsTemplateWidgetRegionId)
	   //              ->pluck('field_value', 'id')->toArray();

	   //      if (! $regions) {
	   //      	throw new DailianException('我们的区不存在!');
	   //      }
	   //      // 第三方游戏
	   //      $thirdGameId = ThirdGame::where('game_id', $gameId)->value('third_game_id');

	   //      $options = ['gid' => $thirdGameId];
	            
	   //      $res = Show91::getAreas($options);

	   //      $thirdAreas = [];
	   //      foreach ($res['areas'] as $key => $area) {
	   //          $thirdAreas[$area['id']] = $area['area_name'];
	   //      }

	   //      return json_encode(['status' => 1, 'our' => $regions, 'third' => $thirdAreas]);
    // 	} catch (DailianException $dailian) {
    // 		return response()->ajax(0, $dailian->getMessage());
    // 	} catch (Exception $e) {
    // 		return response()->ajax(0, '添加失败!');
    // 	} 	
    // }

    /**
     * 写入third_areas表
     * @param Request $request [description]
     */
    // public function addAreas(Request $request)
    // {
    // 	DB::beginTransaction();
    // 	try {
	   //  	$datas = $request->data;
	   //  	$data['created_at'] = date('Y-m-d H:i:s', time());
	   //  	$data['updated_at'] = date('Y-m-d H:i:s', time());
	   //  	// 判断数据库是否已经存在相应的区
	   //  	$has = ThirdArea::where('game_id', $datas['game_id'])
		  //   	->where('area_id', $datas['area_id'])
		  //   	->where('third_area_id', $datas['third_area_id'])
		  //   	->first();

	   //  	if ($has) {
	   //  		throw new DailianException('数据库已存在该区，请勿重复添加');
	   //  	}
	   //  	ThirdArea::create($datas);
    // 	} catch (DailianException $dailian) {
    // 		DB::rollback();
    // 		return response()->ajax(0, $dailian->getMessage());
    // 	} catch (Exception $e) {
    // 		DB::rollback();
    // 		return response()->ajax(0, '添加失败!');
    // 		Log::info($e->getMessage());
    // 	} 
    // 	DB::commit();
    // 	return response()->ajax(1, '添加成功!');
    // }

    /**
     * 服对应列表
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    // public function server(Request $request)
    // {
    // 	$existGames = ThirdGame::pluck('game_id');
    // 	$games = Game::whereIn('id', $existGames)->get(); // 我们的游戏

    // 	return view('backend.config.server', compact('games'));
    // }

    /**
     *                                                              
     * 获取我们的服和第三方的服
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    // public function getServers(Request $request)
    // {
    // 	try {
    // 		$gameId = $request->game_id;
	   //  	// 获取我们的游戏区
	   //  	$goodsTemplateId = GoodsTemplate::where('game_id', $gameId)->where('service_id', 4)->value('id');
	   //  	$goodsTemplateWidgetRegionId = GoodsTemplateWidget::where('goods_template_id', $goodsTemplateId)
	   //                          ->where('field_name', 'region')
	   //                          ->value('id');
	   //      $goodsTemplateWidgetServerId = GoodsTemplateWidget::where('goods_template_id', $goodsTemplateId)
	   //                          ->where('field_name', 'serve')
	   //                          ->value('id');

	   //      // $servers = GoodsTemplateWidgetValue::where('goods_template_widget_id', $goodsTemplateWidgetServerId)
	   //      //         ->pluck('field_value', 'id')->toArray();
	        
	   //      $servers = DB::select("
	   //      	SELECT m.id, CONCAT(m.field_value, '(', n.field_value, ')') AS server_name FROM goods_template_widget_values m
				// LEFT JOIN 
				// 	(SELECT id, field_value FROM goods_template_widget_values
				// 	WHERE goods_template_widget_id = '$goodsTemplateWidgetRegionId') n
				// ON m.parent_id = n.id
				// WHERE m.goods_template_widget_id = '$goodsTemplateWidgetServerId'
	   //      	");
	        
	   //      if (! $servers) {
	   //      	throw new DailianException('我们的服不存在!');
	   //      }

	   //      $ourServerArr = [];
	   //      foreach ($servers as $server) {
	   //      	$ourServerArr[$server->id] = $server->server_name;
	   //      }


	   //      // 第三方游戏
	   //      $thirdGameId = ThirdGame::where('game_id', $gameId)->value('third_game_id');

	   //      $options = ['gid' => $thirdGameId];
	            
	   //      $res = Show91::getAreas($options);

	   //      $thirdAreas = [];
	   //      foreach ($res['areas'] as $key => $area) {
	   //          $thirdAreas[$area['id']] = $area['area_name'];
	   //      }

	   //      // 遍历区找所有的服
	   //      $thirdServers = [];
	   //      foreach ($thirdAreas as $thirdAreaId => $thirdArea) {
	   //      	$options = ['aid' => $thirdAreaId];
	   //      	$res = Show91::getServer($options);

	   //      	foreach($res['servers'] as $thirdServerId => $thirdServer) {
	   //      		$thirdServers[$thirdServer['id']] = $thirdServer['server_name']."(".$thirdArea.")";
	   //      	}
	   //      }

	   //      return json_encode(['status' => 1, 'our' => $ourServerArr, 'third' => $thirdServers]);
    // 	} catch (DailianException $dailian) {
    // 		return response()->ajax(0, $dailian->getMessage());
    // 	} catch (Exception $e) {
    // 		return response()->ajax(0, '添加失败!');
    // 	} 
    // }

    /**
     * 写入表
     * @param Request $request [description]
     */
    // public function addServers(Request $request)
    // {
    // 	DB::beginTransaction();
    // 	try {
	   //  	$datas = $request->data;
	   //  	$data['created_at'] = date('Y-m-d H:i:s', time());
	   //  	$data['updated_at'] = date('Y-m-d H:i:s', time());
	   //  	// 判断数据库是否已经存在相应的区
	   //  	$has = ThirdServer::where('game_id', $datas['game_id'])
		  //   	->where('server_id', $datas['server_id'])
		  //   	->where('third_server_id', $datas['third_server_id'])
		  //   	->first();

	   //  	if ($has) {
	   //  		throw new DailianException('数据库已存在该区，请勿重复添加');
	   //  	}
	   //  	ThirdServer::create($datas);
    // 	} catch (DailianException $dailian) {
    // 		DB::rollback();
    // 		return response()->ajax(0, $dailian->getMessage());
    // 	} catch (Exception $e) {
    // 		DB::rollback();
    // 		return response()->ajax(0, '添加失败!');
    // 	} 
    // 	DB::commit();
    // 	return response()->ajax(1, '添加成功!');
    // }
}

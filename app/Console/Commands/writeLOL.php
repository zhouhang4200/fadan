<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\GoodsTemplate;
use App\Models\GoodsTemplateWidget;
use App\Models\GoodsTemplateWidgetValue;
use App\Models\ThirdServer;
use App\Models\ThirdArea;
use App\Models\ThirdGame;
use App\Services\Show91;
use DB, Exception;

class writeLOL extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'write:lol';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::beginTransaction();
        try {
            // $options = [
            //     'aid' => 1,
            // ];
            
            // $res = Show91::getServer($options);

            // $goodsTemplateId = GoodsTemplate::where('game_id', 78)->value('id');
            // $goodsTemplateWidgetRegionId = GoodsTemplateWidget::where('goods_template_id', $goodsTemplateId)
            //                     ->where('field_name', 'region')
            //                     ->value('id');

            // $goodsTemplateWidgetServeId = GoodsTemplateWidget::where('goods_template_id', $goodsTemplateId)
            //                     ->where('field_name', 'serve')
            //                     ->value('id');

            // $serves = GoodsTemplateWidgetValue::where('goods_template_widget_id', $goodsTemplateWidgetServeId)
            //         ->pluck('field_value', 'id')->toArray();

            // $regions = GoodsTemplateWidgetValue::where('goods_template_widget_id', $goodsTemplateWidgetRegionId)
            //         ->pluck('field_value', 'id')->toArray();

            // // dd($serves, $regions);
            // // dd($res['servers']);
            // // $count = count($servers);
            // // dd($count);
            // $arr = [];
            // foreach ($res['servers'] as $key => $server) {
            //     $arr[' '.$server['id']] = $server['server_name'];
            // }

            // // dd($arr);

            // $options1 = [
            //     'aid' => 2,
            // ];
            
            // $res1 = Show91::getServer($options1);

            // $arr1 = [];
            // foreach ($res1['servers'] as $key => $server) {
            //     $arr1[' '.$server['id']] = $server['server_name'];
            // }
            // //
            // $options2 = [
            //     'aid' => 30,
            // ];

            // $res2 = Show91::getServer($options2);

            // $arr2 = [];
            // foreach ($res2['servers'] as $key => $server) {
            //     $arr2[' '.$server['id']] = $server['server_name'];
            // }
            // //
            // $options3 = [
            //     'aid' => 437,
            // ];

            // $res3 = Show91::getServer($options3);

            // $arr3 = [];
            // foreach ($res3['servers'] as $key => $server) {
            //     $arr3[' '.$server['id']] = $server['server_name'];
            // }

            // $twoArr = array_merge($arr, $arr1);
            // $threeArr = array_merge($twoArr, $arr2);
            // $thirdArrs = array_merge($threeArr, $arr3);

            // // dd($thirdArrs);
            // // 
            // $keyArr = [];
            // foreach ($thirdArrs as $key => $thirdArr) {
            //     foreach ($serves as $key1 => $serve) {
            //         if ($thirdArr == $serve) {
            //             $keyArr[trim($key)] = $key1;
            //         }
            //     }
            // }
            // //
            // $serverDatas = [];
            // foreach ($keyArr as $thirdServeId => $serveId) {
            //     $serverDatas[$thirdServeId]['game_id'] = 78;
            //     $serverDatas[$thirdServeId]['third_id'] = 1;
            //     $serverDatas[$thirdServeId]['server_id'] = $serveId;
            //     $serverDatas[$thirdServeId]['third_server_id'] = $thirdServeId;
            //     $serverDatas[$thirdServeId]['created_at'] = date('Y-m-d H:i:s', time());
            //     $serverDatas[$thirdServeId]['updated_at'] = date('Y-m-d H:i:s', time());
            // }
            // // dd($serverDatas);
            // $serverDatas = array_values($serverDatas);
            // // dd($serverDatas);
            // ThirdServer::insert($serverDatas);

            // // 区
            // $regionDatas = [];

            // $options = [
            //     'gid' => 1,
            // ];
            
            // $res = Show91::getAreas($options);
            // $thirdRegions = [];
            // foreach ($res['areas'] as $key => $value) {
            //     $thirdRegions[$value['id']] = $value['area_name'];
            // }

            // // 我们的区
            // $regionDatas = [];
            // foreach ($thirdRegions as $thirdId => $thirdRegion) {
            //     foreach ($regions as $id => $region) {
            //         if ($thirdRegion == $region) {
            //             $regionDatas[$thirdId] = $id;
            //         }
            //     }
            // }

            // $insertDatas = [];
            // foreach ($regionDatas as $third => $id) {
            //     $insertDatas[$third]['game_id'] = 78;
            //     $insertDatas[$third]['third_id'] = 1;
            //     $insertDatas[$third]['area_id'] = $id;
            //     $insertDatas[$third]['third_area_id'] = $third;
            //     $insertDatas[$third]['created_at'] = date('Y-m-d H:i:s', time());
            //     $insertDatas[$third]['updated_at'] = date('Y-m-d H:i:s', time());
            // }

            // $insertDatas = array_values($insertDatas);

            // ThirdArea::insert($insertDatas);

            // 游戏
            $gameDatas = [
                'third_id' => 1,
                'game_id' => 78,
                'third_game_id' => 1,
                'created_at' => date('Y-m-d H:i:s', time()),
                'updated_at' => date('Y-m-d H:i:s', time()),
            ];
            ThirdGame::create($gameDatas);
        } catch (Exception $e) {
            DB::rollback();
        }
        DB::commit();
        echo '写入成功';
    }
}

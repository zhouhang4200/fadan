<?php

namespace App\Http\Controllers\Backend\GameLeveling\Channel;

use DB;
use Excel;
use Exception;
use App\Http\Controllers\Controller;
use App\Models\GameLevelingChannelPrice;

/**
 * Class PriceController
 * @package App\Http\Controllers\Backend\GameLeveling\Channel
 */
class PriceController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $items = GameLevelingChannelPrice::filter(request()->all())->orderBy('sort')->paginate(10);

        return view('backend.game-leveling.channel.price.index')->with([
            'items' => $items,
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('backend.game-leveling.channel.price.create');
    }

    /**
     * 添加
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        try {
            GameLevelingChannelPrice::create(request()->all());

            return back()->with('success', '添加成功');
        } catch (\Exception $exception) {
            request()->flash();
            return back()->with('fail', '添加失败')->with('fail', $exception->getMessage());
        }
    }

    /**
     * 编辑
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit()
    {
        return view('backend.game-leveling.channel.price.edit')->with([
            'item' => GameLevelingChannelPrice::find(request('id'))
        ]);
    }

    /**
     * 修改
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update()
    {
        DB::beginTransaction();
        try{
             GameLevelingChannelPrice::where('id', request('id'))->update(request()->except('_token'));
        } catch (Exception $e) {
            DB::rollback();
            return back()->with('fail', $e->getMessage());
        }
        DB::commit();
        return back()->with('success', '修改成功');
    }

    /**
     * 删除
     * @return mixed
     */
    public function delete()
    {
        GameLevelingChannelPrice::where('id', request('id'))->delete();

        return response()->ajax(1, '删除成功');
    }

//    /**
//     * 导入
//     * @param   [description]
//     * @return [type]           [description]
//     */
//    public function import()
//    {
//        try {
//            if ($request->hasFile('file')) {
//                $sheetName = ''; // 页脚,要求页脚名字必须为 区服配置 才能正常导入
//                $excelDatas = []; // excel内容
//
//                Excel::load($request->file('file')->path(), function ($reader) use (&$excelDatas, &$sheetName) {
//                    $reader = $reader->getSheet(0);
//                    $sheetName = $reader->getTitle();
//                    $excelDatas = $reader->toArray();
//                });
//
//                $datas = [];
//                foreach ($excelDatas as $k => $excelData) {
//                    // 检测数据是否为空或为全字符标题
//                    if (count($excelData) == 0 || array_sum($excelData) == 0) {
//                        continue;
//                    }
//
//                    if (!is_numeric($excelData[0]) || !is_numeric($excelData[3]) || !is_numeric($excelData[4]) || !is_numeric($excelData[5]) || !is_numeric($excelData[6]) || !is_numeric($excelData[7]) || !is_numeric($excelData[8])) {
//                        return response()->ajax(0, '第' . ($k + 1) . '行数据必须为数字且不能为空!');
//                    }
//                    // 判断数据是否存在
//                    $ifExists = LevelingPriceConfigure::where('game_id', $excelData[0])
//                        ->where('game_leveling_type', $excelData[2])
//                        ->where('game_leveling_number', $excelData[3])
//                        ->first();
//
//                    if ($ifExists) {
//                        return response()->ajax(0, '第' . ($k + 1) . '行数据已经存在!');
//                    }
//                    // 数组
//                    $datas[$k]['game_id'] = $excelData[0];
//                    $datas[$k]['game_name'] = $excelData[1];
//                    $datas[$k]['game_leveling_type'] = $excelData[2];
//                    $datas[$k]['game_leveling_number'] = $excelData[3];
//                    $datas[$k]['game_leveling_level'] = $excelData[4];
//                    $datas[$k]['level_price'] = $excelData[5];
//                    $datas[$k]['level_hour'] = $excelData[6];
//                    $datas[$k]['level_security_deposit'] = $excelData[7];
//                    $datas[$k]['level_efficiency_deposit'] = $excelData[8];
//                    $datas[$k]['created_at'] = Carbon::now()->toDateTimeString();
//                    $datas[$k]['updated_at'] = Carbon::now()->toDateTimeString();
//                }
//                $bool = LevelingPriceConfigure::insert($datas);
//
//                if (!$bool) {
//                    return response()->ajax(0, '导入失败');
//                }
//                return response()->ajax(1, '导入成功');
//            }
//            return response()->ajax(0, '未找到上传文件！');
//        } catch (Exception $e) {
//            return response()->ajax(0, '文件上传错误，请重试!');
//        }
//    }
}

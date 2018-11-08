<?php

namespace App\Http\Controllers\Backend\GameLeveling\Channel;

use App\Models\GameLevelingChannelDiscount;
use DB;
use Exception;
use App\Models\Game;
use App\Models\GoodsTemplate;
use App\Models\LevelingConfigure;
use App\Models\LevelingPriceConfigure;
use App\Models\LevelingRebateConfigure;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DiscountController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $items = GameLevelingChannelDiscount::filter(request()->all())->paginate(10);

        return view('backend.game-leveling.channel.discount.index')->with([
            'items' => $items,
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('backend.game-leveling.channel.discount.create');
    }

    /**
     * 添加
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        try {
            GameLevelingChannelDiscount::create(request()->all());

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
        return view('backend.game-leveling.channel.discount.edit')->with([
            'item' => GameLevelingChannelDiscount::find(request('id'))
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
            GameLevelingChannelDiscount::where('id', request('id'))->update(request()->except('_token'));
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
        GameLevelingChannelDiscount::where('id', request('id'))->delete();

        return response()->ajax(1, '删除成功');
    }
}

<?php

namespace App\Http\Controllers\Frontend\Setting;

use App\Exceptions\CustomException;
use App\Models\AutomaticallyGrabGoods;
use App\Models\Game;
use App\Models\TaobaoShopAuthorization;
use App\Models\UserSetting;
use App\Repositories\Frontend\GameRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Repositories\Frontend\ServiceRepository;

/**
 * 自动抓取淘宝订单设置
 * Class AutomaticallyGrabController
 * @package App\Http\Controllers\Frontend\Setting
 */
class AutomaticallyGrabController extends Controller
{
    /**
     * 配置列表
     * @param Request $request
     * @param ServiceRepository $serviceRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function goods(Request $request,  GameRepository $gameRepository)
    {
        $foreignGoodsId = $request->foreign_goods_id;
        $game = $gameRepository->availableByServiceId(4);
        $shop = TaobaoShopAuthorization::where('user_id', auth()->user()->getPrimaryUserId())->pluck('wang_wang');

        $automaticallyGrabGoods = AutomaticallyGrabGoods::where('user_id', Auth::user()->getPrimaryUserId())
            ->filter(compact('foreignGoodsId'))
            ->with('game')
            ->orderBy('id', 'desc')
            ->paginate(20);


        return view('frontend.v1.setting.automatically-grab.index')->with([
            'game' => $game,
            'automaticallyGrabGoods' => $automaticallyGrabGoods,
            'foreignGoodsId' => $foreignGoodsId,
            'shop' => $shop,
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, GameRepository $gameRepository)
    {
        $game = $gameRepository->availableByServiceId(4);
        $shop = TaobaoShopAuthorization::where('user_id', auth()->user()->getPrimaryUserId())->pluck('wang_wang');

        $automaticallyGrabGoods = AutomaticallyGrabGoods::where('user_id', Auth::user()->getPrimaryUserId())
            ->where('id', $request->id)
            ->first();

        if ($automaticallyGrabGoods) {
            return response()->json(\View::make('frontend.v1.setting.automatically-grab.edit', [
                'automaticallyGrabGoods' => $automaticallyGrabGoods,
                'game' => $game,
                'shop' => $shop,
            ])->render());
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function edit(Request $request)
    {
        try {

            $automaticallyGrabGoods = AutomaticallyGrabGoods::where(['user_id'=> Auth::user()->getPrimaryUserId(), 'id'=> $request->id])->first();

            if (is_null($automaticallyGrabGoods->id)) {
                return response()->ajax(0, '商品不存在');
            } else {
                $automaticallyGrabGoods->foreign_goods_id = $request->foreign_goods_id;
                $automaticallyGrabGoods->remark = $request->remark;
                $automaticallyGrabGoods->game_id = $request->game_id;
                $automaticallyGrabGoods->game_name = Game::where('id', $request->game_id)->value('name');
                $automaticallyGrabGoods->seller_nick = $request->seller_nick;
                $automaticallyGrabGoods->type = $request->type;
                $automaticallyGrabGoods->save();
            }
            return response()->ajax(1, '修改成功');
        } catch (CustomException $exception){
            return response()->ajax(0, '修改失败');
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function add(Request $request, ServiceRepository $serviceRepository)
    {
        $goodsId = $request->foreign_goods_id;
        $serviceId = $request->service_id;
        $gameId = $request->game_id;
        $sellerNick= $request->seller_nick;
        $type = $request->type;

        if (!is_numeric($goodsId)) {
            return response()->ajax(0, '商品ID不合法');
        }

        if (!in_array($serviceId, array_flip($serviceRepository->available()->toArray()))) {
            return response()->ajax(0, '类型不合法');
        }

        $exist = AutomaticallyGrabGoods::where('service_id', $serviceId)
            ->where('foreign_goods_id', $goodsId)
            ->first();

        if ($exist) {
            return response()->ajax(0, '该商品ID已存在');
        }
        try {
            AutomaticallyGrabGoods::create([
                'user_id' => Auth::user()->getPrimaryUserId(),
                'service_id' => $serviceId,
                'foreign_goods_id' => $goodsId,
                'game_id' => $gameId,
                'game_name' => Game::where('id', $gameId)->value('name'),
                'seller_nick' => $sellerNick,
                'type' => $type,
                'remark' => $request->remark,
            ]);
            return response()->ajax(1, '添加成功');
        } catch (CustomException $exception){
            return response()->ajax(0, '添加失败');
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function delete(Request $request)
    {
        AutomaticallyGrabGoods::where('user_id', Auth::user()->getPrimaryUserId())->where('id', $request->id)->delete();
        return response()->ajax(1, '删除成功');
    }

    /**
     * 提验是否自动发货
     * @param Request $request
     * @return mixed
     */
    public function delivery(Request $request)
    {
        try {
            AutomaticallyGrabGoods::where('user_id', Auth::user()->getPrimaryUserId())->where('id', $request->id)->update([
                'delivery' => $request->delivery
            ]);
        } catch (\Exception $exception) {
            return response()->ajax(0, '更新失败');
        }
        return response()->ajax(1, '更新成功');
    }
}
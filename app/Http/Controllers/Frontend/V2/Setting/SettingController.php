<?php

namespace App\Http\Controllers\Frontend\V2\Setting;

use Exception;
use App\Models\Game;
use App\Models\SmsTemplate;
use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\AutomaticallyGrabGoods;
use App\Models\TaobaoShopAuthorization;
use App\Repositories\Frontend\GameRepository;
use App\Repositories\Frontend\ServiceRepository;

class SettingController extends Controller
{
    /**
     * 短信管理页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function message()
    {
        return view('frontend.v2.setting.message');
    }

    /**
     * 短信管理开关设置
     * @return mixed
     */
    public function messageStatus()
    {
        try {
            if (in_array(request('status'), [0, 1, 2])) {
                $template = SmsTemplate::where(['user_id'=> Auth::user()->getPrimaryUserId(), 'id'=> request('id')])->first();

                if (! $template) {
                    return response()->ajax(0, '模板不存在!');
                }

                $template->status = request('status');
                $template->save();

                return response()->ajax(1, '设置成功!');
            }
        } catch (CustomException $e) {
            return response()->ajax(0, '服务器异常!');
        } catch (Exception $e) {
            return response()->ajax(0, '服务器异常!');
        }
        return response()->ajax(0, '数据信息有误!');
    }

    /**
     * 短信管理表单数据
     * @return mixed
     */
    public function messageDataList()
    {
        return SmsTemplate::where('user_id', auth()->user()->getPrimaryUserId())->where('type', 1)->paginate(10);
    }

    /**
     * 短信管理修改
     * @return mixed
     */
    public function messageUpdate()
    {
        try {
            $template = SmsTemplate::where(['user_id'=> Auth::user()->getPrimaryUserId(), 'id'=> request('id')])->first();

            if (! $template) {
                return response()->ajax(0, '模板不存在!');
            }

            $template->name = request('name');
            $template->contents = request('contents');
            $template->save();

        } catch (CustomException $e){
            return response()->ajax(0, '服务器异常!');
        } catch (Exception $e) {
            return response()->ajax(0, '服务器异常!');
        }
        return response()->ajax(1, '修改成功!');
    }

    /**
     * 抓取商品配置页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function goods()
    {
        return view('frontend.v2.setting.goods');
    }

    /**
     * 抓取商品配置页面数据
     * @param GameRepository $gameRepository
     * @return mixed
     */
    public function goodsDataList(GameRepository $gameRepository)
    {
        $foreignGoodsId = request('foreign_goods_id');

        return AutomaticallyGrabGoods::where('user_id', Auth::user()->getPrimaryUserId())
            ->filter(compact('foreignGoodsId'))
            ->with('game')
            ->orderBy('id', 'desc')
            ->paginate(20);
    }

    /**
     * 抓取商品开关设置
     * @return mixed
     */
    public function goodsDelivery()
    {
        try {
            AutomaticallyGrabGoods::where('user_id', Auth::user()->getPrimaryUserId())
                ->where('id', request('id'))
                ->update([
                    'delivery' => request('delivery')
                ]);
        } catch (Exception $e) {
            return response()->ajax(0, '设置失败！');
        }
        return response()->ajax(1, '设置成功！');
    }

    /**
     * 抓取商品添加
     * @param ServiceRepository $serviceRepository
     * @return mixed
     */
    public function goodsAdd(ServiceRepository $serviceRepository)
    {
        $goodsId = request('foreign_goods_id');
        $serviceId = request('service_id', 4);
        $gameId = request('game_id');
        $sellerNick= request('seller_nick');

        if (!is_numeric($goodsId)) {
            return response()->ajax(0, '商品ID不合法!');
        }

        if (!in_array($serviceId, array_flip($serviceRepository->available()->toArray()))) {
            return response()->ajax(0, '类型不合法!');
        }

        $exist = AutomaticallyGrabGoods::where('service_id', $serviceId)
            ->where('foreign_goods_id', $goodsId)
            ->first();

        if ($exist) {
            return response()->ajax(0, '该商品ID已存在!');
        }
        try {
            AutomaticallyGrabGoods::create([
                'user_id' => Auth::user()->getPrimaryUserId(),
                'service_id' => $serviceId,
                'foreign_goods_id' => $goodsId,
                'game_id' => $gameId,
                'game_name' => Game::where('id', $gameId)->value('name'),
                'seller_nick' => $sellerNick,
                'remark' => request('remark'),
            ]);
        } catch (Exception $e) {
            return response()->ajax(0, '服务器异常!');
        }
        return response()->ajax(1, '添加成功!');
    }

    /**
     * 抓取商品游戏
     * @return mixed
     */
    public function goodsGame()
    {
        return Game::get();
    }

    /**
     * 抓取商品旺旺
     * @return mixed
     */
    public function goodsSellerNick()
    {
        return TaobaoShopAuthorization::where('user_id', auth()->user()->getPrimaryUserId())->pluck('wang_wang');
    }

    /**
     * 抓取商品修改
     * @return mixed
     */
    public function goodsUpdate()
    {
        try {
            $automaticallyGrabGoods = AutomaticallyGrabGoods::where(['user_id'=> Auth::user()->getPrimaryUserId(), 'id'=> request('id')])->first();

            if (!$automaticallyGrabGoods) {
                return response()->ajax(0, '商品不存在!');
            }

            $automaticallyGrabGoods->foreign_goods_id = request('foreign_goods_id');
            $automaticallyGrabGoods->remark = request('remark');
            $automaticallyGrabGoods->game_id = request('game_id');
            $automaticallyGrabGoods->game_name = Game::where('id', request('game_id'))->value('name');
            $automaticallyGrabGoods->seller_nick = request('seller_nick');
            $automaticallyGrabGoods->type = request('type');
            $automaticallyGrabGoods->save();
        } catch (Exception $e){
            return response()->ajax(0, '修改失败!');
        }
        return response()->ajax(1, '修改成功!');
    }

    /**
     * 抓取商品删除
     * @return mixed
     */
    public function goodsDelete()
    {
        try {
            AutomaticallyGrabGoods::where('user_id', Auth::user()->getPrimaryUserId())->where('id', request('id'))->delete();
        } catch (Exception $e) {
            return response()->ajax(0, '服务器异常！');
        }
        return response()->ajax(1, '删除成功！');
    }
}

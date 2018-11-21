<?php

namespace App\Http\Controllers\Frontend\V2\Setting;

use App\Models\OrderSendChannel;
use Exception;
use App\Models\Game;
use App\Models\SmsTemplate;
use Illuminate\Http\Request;
use App\Models\OrderAutoMarkup;
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
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function message()
    {
        return view('frontend.v2.setting.message');
    }

    /**
     * 短信管理开关设置
     *
     * @return mixed
     */
    public function messageStatus()
    {
        try {
            if (in_array(request('status'), [0, 1, 2])) {
                $template = SmsTemplate::where(['user_id'=> Auth::user()->getPrimaryUserId(), 'id'=> request('id')])->first();

                if (! $template) {
                    return response()->ajax(0, '设置失败：模板不存在!');
                }

                $template->status = request('status');
                $template->save();

                return response()->ajax(1, '设置成功!');
            }
        } catch (Exception $e) {
            return response()->ajax(0, '设置失败：服务器异常!');
        }
        return response()->ajax(0, '数据信息有误!');
    }

    /**
     * 短信管理表单数据
     *
     * @return mixed
     */
    public function messageDataList()
    {
        return SmsTemplate::where('user_id', auth()->user()->getPrimaryUserId())->where('type', 1)->paginate(10);
    }

    /**
     * 短信管理修改
     *
     * @return mixed
     */
    public function messageUpdate()
    {
        try {
            $template = SmsTemplate::where(['user_id'=> Auth::user()->getPrimaryUserId(), 'id'=> request('id')])->first();

            if (! $template) {
                return response()->ajax(0, '修改失败：模板不存在!');
            }

            $template->name = request('name');
            $template->contents = request('contents');
            $template->save();

        } catch (Exception $e) {
            return response()->ajax(0, '修改失败：服务器异常!');
        }
        return response()->ajax(1, '修改成功!');
    }

    /**
     * 抓取商品配置页面数据
     *
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
     *
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
            return response()->ajax(0, '设置失败：服务器异常！');
        }
        return response()->ajax(1, '设置成功！');
    }

    /**
     * 抓取商品添加
     *
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
            return response()->ajax(0, '添加失败：商品ID不合法!');
        }

        if (!in_array($serviceId, array_flip($serviceRepository->available()->toArray()))) {
            return response()->ajax(0, '添加失败：类型不合法!');
        }

        $exist = AutomaticallyGrabGoods::where('service_id', $serviceId)
            ->where('foreign_goods_id', $goodsId)
            ->first();

        if ($exist) {
            return response()->ajax(0, '添加失败：该商品ID已存在!');
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
            return response()->ajax(0, '添加失败：服务器异常!');
        }
        return response()->ajax(1, '添加成功!');
    }

    /**
     * 抓取商品游戏
     *
     * @return mixed
     */
    public function goodsGame()
    {
        return Game::get();
    }

    /**
     * 抓取商品旺旺
     *
     * @return mixed
     */
    public function goodsSellerNick()
    {
        return TaobaoShopAuthorization::where('user_id', auth()->user()->getPrimaryUserId())->pluck('wang_wang');
    }

    /**
     * 抓取商品修改
     *
     * @return mixed
     */
    public function goodsUpdate()
    {
        try {
            $automaticallyGrabGoods = AutomaticallyGrabGoods::where(['user_id'=> Auth::user()->getPrimaryUserId(), 'id'=> request('id')])->first();

            if (!$automaticallyGrabGoods) {
                return response()->ajax(0, '修改失败：商品不存在!');
            }

            $automaticallyGrabGoods->foreign_goods_id = request('foreign_goods_id');
            $automaticallyGrabGoods->remark = request('remark');
            $automaticallyGrabGoods->game_id = request('game_id');
            $automaticallyGrabGoods->game_name = Game::where('id', request('game_id'))->value('name');
            $automaticallyGrabGoods->seller_nick = request('seller_nick');
            $automaticallyGrabGoods->type = request('type');
            $automaticallyGrabGoods->save();
        } catch (Exception $e){
            return response()->ajax(0, '修改失败：服务器异常!');
        }
        return response()->ajax(1, '修改成功!');
    }

    /**
     * 抓取商品删除
     *
     * @return mixed
     */
    public function goodsDelete()
    {
        try {
            AutomaticallyGrabGoods::where('user_id', Auth::user()->getPrimaryUserId())->where('id', request('id'))->delete();
        } catch (Exception $e) {
            return response()->ajax(0, '删除失败：服务器异常！');
        }
        return response()->ajax(1, '删除成功！');
    }

    /**
     * 店铺授权页面
     *
     * @return \Illuminate\Auth\Access\Response|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function authorizeIndex()
    {
        return view('frontend.v2.setting.authorize');
    }

    /**
     * 店铺授权数据
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function authorizeDataList()
    {
        try {
            $id = request('id');
            $sign = request('sign');
            $wangWang = request('retMsg');

            $taoBaoShopAuth = TaobaoShopAuthorization::where('user_id', auth()->user()->getPrimaryUserId())->paginate(15);

            $bindResult = 0;
            if ($id && $sign && $wangWang) {
                if ($sign == md5(Auth::user()->id . Auth::user()->name)) {

                    $exist = TaobaoShopAuthorization::where('wang_wang', $wangWang)->first();

                    if (!$exist) {
                        $userExist = TaobaoShopAuthorization::where('wang_wang', $wangWang)
                            ->where('user_id', auth()->user()->getPrimaryUserId())
                            ->first();

                        if ( !$userExist) {
                            TaobaoShopAuthorization::create([
                                'wang_wang'  => $wangWang,
                                'user_id'  => auth()->user()->getPrimaryUserId(),
                            ]);
                        }
                    }
                    $bindResult = 1;
                }
            }
        } catch (Exception $e) {
            return response()->ajax(0, '获取数据失败：数据异常!');
        }
        return response()->json(['status' => 1, 'data' => $taoBaoShopAuth, 'bind' => $bindResult]);
    }

    /**
     * 店铺授权地址
     *
     * @return string
     */
    public function authorizeUrl()
    {
        $callBack = route('frontend.setting.tb-auth.store') . '?id=' .  auth()->user()->id . '&sign=' . md5(auth()->user()->id . auth()->user()->name);
        return 'http://api.kamennet.com/API/CallBack/TOP/SiteInfo_New.aspx?SitID=90347&Sign=b7753b8d55ba79fcf2d190de120a5229&CallBack=' . urlencode($callBack);
    }

    /**
     * 店铺授权删除
     *
     * @return mixed
     */
    public function authorizeDelete()
    {
        try {
            TaobaoShopAuthorization::where('user_id', auth()->user()->getPrimaryUserId())
                ->where('id', request('id'))
                ->delete();
        } catch (Exception $e) {
            return response()->ajax(0, '删除失败：服务器异常!');
        }
        return response()->ajax(1, '删除成功!');
    }

    /**
     * 代练发单辅助页面
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function auxiliary()
    {
        return view('frontend.v2.setting.auxiliary');
    }

    /**
     * 订单数据
     *
     * @return mixed
     */
    public function markupDataList()
    {
        return OrderAutoMarkup::where('user_id', Auth::user()->getPrimaryUserId())
            ->oldest('markup_amount')
            ->paginate(15);
    }

    /**
     * 自动加价添加
     *
     * @return mixed
     */
    public function markupAdd()
    {
        try {
            $data['user_id'] = Auth::user()->getPrimaryUserId();
            $requestMarkupAmount = is_numeric(request('markup_amount')) ? round(request('markup_amount'), 2) : 0;
            $requestMarkupMoney = is_numeric(request('markup_money')) ? round(request('markup_money'), 2) : 0;
            // 如果填的值不合法则
            if (! $requestMarkupAmount || !$requestMarkupMoney) {
                return response()->ajax(0, '添加失败：发单价或增加金额必须为数字且大于0!');
            }
            // 查看发单价是否有重复
            $sameMarkupAmount = OrderAutoMarkup::where('user_id', Auth::user()->getPrimaryUserId())
                ->where('markup_amount', $requestMarkupAmount)
                ->count();

            if ($sameMarkupAmount > 0) {
                return response()->ajax(0, '添加失败：发单价已存在!');
            }

            // 数据
            $data['markup_amount'] = $requestMarkupAmount;
            $data['markup_time'] = request('markup_time', 0);
            $data['markup_type'] = request('markup_type');
            $data['markup_money'] = is_numeric(request('markup_money')) ? round(request('markup_money'), 2) : 0;
            $data['markup_frequency'] = is_numeric(request('markup_frequency')) ? intval(request('markup_frequency')) : 0;
            $data['markup_number'] = is_numeric(request('markup_number')) ? intval(request('markup_number')) : 0;

            OrderAutoMarkup::create($data);
        } catch (Exception $e) {
            return response()->ajax(0, '添加失败：服务器异常!');
        }
        return response()->ajax(1, '添加成功!');
    }

    /**
     * 自动加价修改
     *
     * @return mixed
     */
    public function markupUpdate()
    {
        try {
            $orderAutoMarkup = OrderAutoMarkup::find(request('id'));
            $requestMarkupAmount = is_numeric(request('markup_amount')) ? round(request('markup_amount'), 2) : 0;
            $requestMarkupMoney = is_numeric(request('markup_money')) ? round(request('markup_money'), 2) : 0;

            // 如果填的值不合法则
            if (!$requestMarkupAmount || !$requestMarkupMoney) {
                return response()->ajax(0, '添加失败：发单价或增加金额必须为数字且大于0!');
            }

            // 查看发单价是否有重复
            $sameMarkupAmount = OrderAutoMarkup::where('user_id', $orderAutoMarkup->user_id)
                ->where('markup_amount', $requestMarkupAmount)
                ->count();

            if ($sameMarkupAmount > 0 && $orderAutoMarkup->markup_amount != $requestMarkupAmount) {
                return response()->ajax(0, '修改失败：发单价已存在');
            }

            // 数据
            $orderAutoMarkup->markup_amount = $requestMarkupAmount;
            $orderAutoMarkup->markup_time = request('markup_time', 0);
            $orderAutoMarkup->markup_type = request('markup_type');
            $orderAutoMarkup->markup_money = is_numeric(request('markup_money')) ? round(request('markup_money'), 2) : 0;
            $orderAutoMarkup->markup_frequency = is_numeric(request('markup_frequency')) ? intval(request('markup_frequency')) : 0;
            $orderAutoMarkup->markup_number = is_numeric(request('markup_number')) ? intval(request('markup_number')) : 0;

            $orderAutoMarkup->save();
        } catch (Exception $e) {
            return response()->ajax(0, '修改失败：服务器异常!');
        }
        return response()->ajax(1, '修改成功!');
    }

    /**
     * 自动加价删除
     *
     * @return mixed
     */
    public function markupDelete()
    {
        try {
            OrderAutoMarkup::destroy(request('id'));
        } catch (Exception $e) {
            return response()->ajax(0, '删除失败：服务器异常!');
        }
        return response()->ajax(1, '删除成功!');
    }

    /**
     * 渠道设置
     *
     * @return mixed
     */
    public function channelDataList()
    {
        $games = Game::get();

        $orderSendChannels = OrderSendChannel::where('user_id', Auth::user()->getPrimaryUserId())
            ->pluck('game_id');

        $orderSendChannels = $orderSendChannels ? $orderSendChannels->toArray() : [];

        foreach ($games as $k => $game) {
            if (in_array($game->id, $orderSendChannels)) {
                $orderSendChannel = OrderSendChannel::where('user_id', Auth::user()->getPrimaryUserId())
                    ->where('game_id', $game->id)
                    ->first();

                $game->hasModel = explode('-', $orderSendChannel->third);
                $arr = [];
                foreach ($game->hasModel as $v) {
                    $arr[] = (int)$v;
                }
                $diffThirds = array_diff([1, 3, 4, 5], $arr); //黑名单
                $game->hasModel = $diffThirds;
            } else {
                $game->hasModel = [1, 3, 4, 5];
            }

            $game->allChannel = [
                ['id' => 1, 'name' => 'show91平台'],
                ['id' => 3, 'name' => '蚂蚁代练'],
                ['id' => 4, 'name' => 'dd373平台'],
                ['id' => 5, 'name' => '丸子代练']
            ];
        }
        return $games;
    }

    /**
     * 多选框事件
     *
     * @return mixed
     */
    public function channelSwitch()
    {
        try {
            $userId = Auth::user()->getPrimaryUserId();
            $gameId = request('game_id');
            $gameName = request('game_name');
            $thirds = request('thirds'); // 白名单
            $realThirds = config('leveling.third'); // 所有

            // 至少选一个游戏
            if (! $thirds) {
                return response()->ajax(0, '设置失败：请至少选择一个平台！');
            }

            // 如果已经存在设置过的平台
            $orderSendChannel = OrderSendChannel::where('user_id', $userId)->where('game_id', $gameId)->first();

            $diffThirds = array_diff($realThirds, $thirds); //黑名单

            if (count($diffThirds) < 1 && $orderSendChannel) {
                $orderSendChannel->delete();
            } else {
                $data['user_id'] = $userId;
                $data['game_id'] = $gameId;
                $data['game_name'] = $gameName;
                $data['third'] = implode($diffThirds, '-');
                OrderSendChannel::updateOrCreate(['user_id' => $userId, 'game_id' => $gameId], $data);
            }
        } catch (Exception $e) {
            return response()->ajax(1, '设置失败：服务器异常！');
        }
        return response()->ajax(1, '设置成功');
    }
}

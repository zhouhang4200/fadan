<?php

namespace App\Http\Controllers\Frontend\Setting;

use App\Models\AutomaticallyGrabGoods;
use App\Models\UserSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function goods(Request $request)
    {
        $foreignGoodsId = $request->foreign_goods_id;

        $AutomaticallyGrabGoods = AutomaticallyGrabGoods::where('user_id', Auth::user()->getPrimaryUserId())->paginate(30);

        return view('frontend.setting.automatically-grab.index', compact('AutomaticallyGrabGoods', 'foreignGoodsId'));
    }
}
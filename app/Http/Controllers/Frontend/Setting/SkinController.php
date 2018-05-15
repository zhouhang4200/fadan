<?php

namespace App\Http\Controllers\Frontend\Setting;

use App\Models\UserSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

/**
 * Class SkinController
 * @package App\Http\Controllers\Frontend\Setting
 */
class SkinController extends Controller
{
    /**
     * 展示皮肤交易QQ
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        // 获取用户的接单权限设置
        $skinTradeQQ = isset(Auth::user()->getUserSetting()['skin_trade_qq']) ?
            Auth::user()->getUserSetting()['skin_trade_qq'] : '';
        $skinTradeWX = isset(Auth::user()->getUserSetting()['skin_trade_wx']) ?
            Auth::user()->getUserSetting()['skin_trade_wx'] : '';

        return view('frontend.setting.skin.index', compact('skinTradeQQ', 'skinTradeWX'));
    }

    /**
     * 保存QQ号设置
     * @param Request $request
     */
    public function set(Request $request)
    {
        if (is_numeric($request->qq)) {
            // 写入或更新设置数据
            UserSetting::updateOrCreate(['user_id' => Auth::user()->getPrimaryUserId(), 'option' => 'skin_trade_qq'], [
                'option' => 'skin_trade_qq',
                'value' => $request->qq,
                'user_id' => Auth::user()->id,
            ]);
            UserSetting::updateOrCreate(['user_id' => Auth::user()->getPrimaryUserId(), 'option' => 'skin_trade_wx'], [
                'option' => 'skin_trade_wx',
                'value' => $request->wx,
                'user_id' => Auth::user()->id,
            ]);
            // 刷新用户设置缓存
            refreshUserSetting();
            return response()->ajax(1, '设置成功');
        }
        return response()->ajax(0, '非法参数');
    }
}

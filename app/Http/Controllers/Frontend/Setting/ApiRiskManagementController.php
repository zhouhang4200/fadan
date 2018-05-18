<?php

namespace App\Http\Controllers\Frontend\Setting;

use App\Models\UserSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

/**
 * Class ApiRiskManagementController
 * @package App\Http\Controllers\Frontend\Setting
 */
class ApiRiskManagementController extends Controller
{
    /**
     * 展示风控值
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        // 获取用户的接单权限设置
        $riskRate = isset(Auth::user()->getUserSetting()['api_risk_rate']) ?
            Auth::user()->getUserSetting()['api_risk_rate'] : config('order.apiRiskRate');

        return view('frontend.v1.setting.api-risk-management.index', compact('riskRate'));
    }

    /**
     * 保存设置值
     * @param Request $request
     */
    public function set(Request $request)
    {
        if (is_numeric($request->rate)) {
            // 写入或更新设置数据
            UserSetting::updateOrCreate(['user_id' => Auth::user()->getPrimaryUserId(), 'option' => 'api_risk_rate'], [
                'option' => 'api_risk_rate',
                'value' => $request->rate,
                'user_id' => Auth::user()->id,
            ]);
            // 刷新用户设置缓存
            refreshUserSetting();
            return response()->ajax(1, '设置成功');
        }
        return response()->ajax(0, '非法参数');
    }
}

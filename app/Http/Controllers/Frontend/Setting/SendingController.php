<?php

namespace App\Http\Controllers\Frontend\Setting;

use Auth;
use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SendingController extends Controller
{
	/**
	 * 设置 - 重发订单的发单客服（发单子账号）
	 * @return [type] [description]
	 */
	public function index()
	{
		// 获取该登录账号的主账号
		$user = User::find(Auth::user()->getPrimaryUserId());
		$sendingControl = UserSetting::where('user_id', $user->id)->where('option', 'sending_control')->value('value');

		return view('frontend.v1.setting.sending-control.index', compact('sendingControl'));
	}

	/**
	 * 改变设置
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function change(Request $request)
	{
		// type = 页面传过来的值 0/1
		$res = UserSetting::updateOrCreate(['user_id' => Auth::user()->getPrimaryUserId(), 'option' => 'sending_control'], [
            'option' => 'sending_control',
            'value' => $request->type,
            'user_id' => Auth::user()->getPrimaryUserId(),
        ]);

        return response()->ajax(1, '设置成功');
	}
}

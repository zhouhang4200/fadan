<?php

namespace App\Http\Controllers\Frontend\Setting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderSendChannelController extends Controller
{
	/**
	 * 发单渠道设置列表
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
    public function index(Request $request)
    {
    	$primaryUserId = Auth::user()->getPrimaryUserId();
    	$orderSendChannels = OrderSendChannel::where('user_id', $primaryUserId)->get();

    	if ($request->ajax()) {
    		return response()->json(view()->make('frontend.v1.setting.sending-assist.send-channel-list', [
                'orderSendChannels' => $orderSendChannels,
            ])->render());
    	}

    	return view('frontend.v1.setting.sending-assist.send-channel', compact('orderSendChannels'));
    }

    /**
     * 发单渠道设置
     * @param Request $request [description]
     */
    public function set(Request $request)
    {

    }
}

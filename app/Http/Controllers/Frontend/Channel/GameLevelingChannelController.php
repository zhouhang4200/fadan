<?php

namespace App\Http\Controllers\Frontend\Channel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GameLevelingChannelController extends Controller
{
    /**
     * 渠道下单首页
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('channel.leveling.index');
    }
}

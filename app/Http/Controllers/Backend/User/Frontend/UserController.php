<?php

namespace App\Http\Controllers\Backend\User\Frontend;

use App\Exceptions\CustomException;
use Illuminate\Http\Request;

use Asset, Auth;
use App\Models\User;
use App\Extensions\Asset\Recharge;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $name = $request->name;
    	$users = User::where('parent_id', 0)->latest('id')->with('asset')->paginate(config('frontend.page'));

    	return view('backend.user.frontend.index', compact('users', 'name'));
    }

    /**
     * 手动加款
     * @param Request $request
     */
    public function recharge(Request $request)
    {
        try {
            Asset::handle(new Recharge($request->amount, Recharge::TRADE_SUBTYPE_MANUAL, generateOrderNo(), '手动加款', $request->id, Auth::user()->id));
            return response()->ajax(1, '加款成功');
        } catch (CustomException $exception) {
            return response()->ajax(0, $exception->getMessage());
        }
    }
}

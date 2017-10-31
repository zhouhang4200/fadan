<?php

namespace App\Http\Controllers\Backend\User\Frontend;

use App\Exceptions\CustomException;
use App\Repositories\Api\UserRechargeOrderRepository;
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
     * @param Request $request
     * @param UserRechargeOrderRepository $userRechargeOrderRepository
     * @return mixed
     */
    public function recharge(Request $request, UserRechargeOrderRepository $userRechargeOrderRepository)
    {
        try {
            $userRechargeOrderRepository->store($request->amount, $request->id, '手动加款', generateOrderNo(), '', false);
            return response()->ajax(1, '加款成功');
        } catch (CustomException $exception) {
            return response()->ajax(0, $exception->getMessage());
        }
    }
}
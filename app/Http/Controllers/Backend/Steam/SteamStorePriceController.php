<?php

namespace App\Http\Controllers\Backend\Steam;

use App\Models\SteamStorePrice;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

/**
 * Class OrderController
 * @package App\Http\Controllers\Backend
 */
class SteamStorePriceController extends Controller
{
	/**
	 * UserGoodsController constructor.
	 */
	public function __construct()
	{
		$userNames = User::pluck('name','id');
		view()->share(['userNames' => $userNames]);
	}

    public function index(Request $request)
    {
        //多条件查找
        $where = function ($query) use ($request) {
            if ($request->has('user_id') and $request->user_id != '') {
                $query->where('user_id', $request->user_id);
            }
        };
        $steamStorePrices = SteamStorePrice::where($where)->orderBy('id', 'desc')->paginate(config('backend.page'));
        return view('backend.steam.store-price.store-price', compact('steamStorePrices'));
    }

	/**
	 * 创建商户密价
	 * @param Request $request
	 * @return mixed
	 */
	public function insertStorePrice(Request $request)
	{
		if(SteamStorePrice::checkUserId($request->user_id)){
			return response()->ajax('0', '该商户密价已存在，请勿重复添加');
		}
		$steamStorePrices = SteamStorePrice::create([
			'user_id'=> $request->user_id,
			'clone_price'=> $request->clone_price,
			'username'=> auth('admin')->user()->name,
		]);
		if(!$steamStorePrices){
			return response()->ajax('0', '添加失败');
		}
		return response()->ajax('1', '添加成功');
	}

	public function editSomething(Request $request)
	{
		$attr = $request->attr;
		$goods = SteamStorePrice::find($request->id);
		$goods->$attr = $request->value;
		if ($goods->save()) {
			return response()->ajax('1', '修改成功');
		} else {
			return response()->ajax('0', '修改失败');
		};
	}

}

<?php

namespace App\Http\Controllers\Frontend\Setting;

use App\Models\TaobaoShopAuthorization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Psy\Exception\ErrorException;

/**
 * Class SkinController
 * @package App\Http\Controllers\Frontend\Setting
 */
class TbAuthController extends Controller
{
    /**
     * 绑定加款旺旺
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $id = $request->input('id');
        $sign = $request->input('sign');
        $wangWang = $request->input('retMsg');

        $bindResult = 0;
        if ($id && $sign && $wangWang) {
            if ($sign == md5(Auth::user()->id . Auth::user()->name)) {
                $exist = Auth::user()->where('wang_wang', $wangWang)->first();

                if (!$exist) {
                    $user = Auth::user();
                    $user->wang_wang = $wangWang;
                    $user->save();
                    $bindResult = 1;
                }
                $bindResult = 2;
            }
        }
        return view('frontend.v1.setting.tb-auth.index', compact('bindResult'));
    }

    /**
     * 绑定店铺旺旺
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function store(Request $request)
    {
        $id = $request->input('id');
        $sign = $request->input('sign');
        $wangWang = $request->input('retMsg');

        $taobaoShopAuth = TaobaoShopAuthorization::where('user_id', auth()->user()->getPrimaryUserId())->get();

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
        return view('frontend.v1.setting.tb-auth.store', compact('bindResult', 'taobaoShopAuth'));
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function destroy(Request $request)
    {
        try {
            TaobaoShopAuthorization::where('user_id', auth()->user()->getPrimaryUserId())->where('id', $request->id)->delete();
            return response()->ajax(1, '删除成功');
        } catch (ErrorException $exception) {
            return response()->ajax(0, '删除失败');
        }
    }
}
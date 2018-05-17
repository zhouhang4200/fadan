<?php

namespace App\Http\Controllers\Backend\Businessman;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Backend\TaobaoShopAuthorizationRepository;
use App\Exceptions\CustomException;

class TaobaoShopAuthController extends Controller
{
    public function index(Request $request)
    {
        $wangWang = $request->wang_wang ? explode(',', $request->wang_wang) : null;
        $dataList = TaobaoShopAuthorizationRepository::getList($request->user_id, $wangWang);

        // 不重复的选项
        $shops = TaobaoShopAuthorizationRepository::getShops();
        $shopsJson = json_encode($shops);

        return view('backend.businessman.taobao-shop-auth.index', compact('dataList', 'shopsJson'));
    }

    public function store(Request $request)
    {
        $userId = $request->user_id;
        $wangWang = $request->wang_wang;
        if (empty($userId)) {
            return response()->ajax(0, '商户ID不正确');
        }
        if (empty($wangWang)) {
            return response()->ajax(0, '请选择店铺');
        }
        $wangWang = explode(',', $wangWang);

        try {
            TaobaoShopAuthorizationRepository::store($userId, $wangWang);
        } catch (CustomException $e) {
            return response()->ajax(0, $e->getMessage());
        }

        return response()->ajax(1);
    }

    public function destroy($id)
    {
        try {
            TaobaoShopAuthorizationRepository::destroy($id);
        } catch (CustomException $e) {
            return response()->ajax(0, $e->getMessage());
        }

        return response()->ajax(1);
    }
}

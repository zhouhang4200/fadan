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
        $dataList = TaobaoShopAuthorizationRepository::getList();
        // 不重复的选项
        $shops = TaobaoShopAuthorizationRepository::getShops();
        $shopsJson = json_encode($shops);

        return view('backend.businessman.taobao-shop-auth.index', compact('dataList', 'shopsJson'));
    }

    public function store(Request $request)
    {
        $userId = $request->user_id;
        $ids = $request->ids;
        if (empty($userId)) {
            return response()->ajax(0, '商户ID不正确');
        }
        if (empty($ids)) {
            return response()->ajax(0, '请选择店铺');
        }
        $ids = explode(',', $ids);

        try {
            TaobaoShopAuthorizationRepository::store($userId, $ids);
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

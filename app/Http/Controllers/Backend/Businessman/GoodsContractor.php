<?php

namespace App\Http\Controllers\Backend\Businessman;

use App\Models\GoodsContractorConfig;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \Exception;

/**
 * 商品承包
 * Class GoodsContractor
 * @package App\Http\Controllers\Backend\Businessman
 */
class GoodsContractor extends Controller
{
    /**
     * 商品承包列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $kmGoodsId = $request->km_goods_id;

        $goodsContractor = GoodsContractorConfig::filter(compact('kmGoodsId'))->paginate(30);

        return view('backend.businessman.goods-contractor.index', compact('goodsContractor', 'kmGoodsId'));
    }

    /**
     * 新增
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $data = $request->data;
            $data['created_admin_user_id'] = auth('admin')->user()->id;
            GoodsContractorConfig::create($data);
            return response()->json(['code' => 1, 'message' => '添加成功']);
        } catch (Exception $exception) {
            return response()->json(['code' => 0, 'message' => '添加失败']);
        }
    }

    /**
     * 删除
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        try {
            GoodsContractorConfig::destroy($request->id);
            return response()->json(['code' => 1, 'message' => '删除成功']);
        } catch (Exception $exception) {
            return response()->json(['code' => 0, 'message' => '删除失败']);
        }
    }

}

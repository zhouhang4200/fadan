<?php

namespace App\Http\Controllers\Backend\Goods;

use App\Models\GoodsTemplate;
use Auth, Config, \Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\GoodsCategory;

/**
 * Class CategoryController
 * @package App\Http\Controllers\Backend\Goods
 */
class CategoryController extends Controller
{
    /**
     * 类目列表
     * @param Request $request
     * @param int  $parentId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, int $parentId= 0)
    {
        $name = $request->name;
        $categories = GoodsCategory::with(['parent', 'createdAdmin', 'updatedAdmin'])
            ->name($name)
            ->parentId($parentId)
            ->orderBy('sortord')
            ->paginate(30);

        $goodsTemplates = GoodsTemplate::where('status', 1)->get();

        return view('backend.goods.category.index', compact('categories', 'parentId', 'goodsTemplates', 'name'));
    }

    /**
     * 获取类目信息
     * @param $id
     */
    public function show($id)
    {
        $category = GoodsCategory::find($id);

        return response()->json(['code' => 1, 'message' => '获取成功', 'data' =>[
            'category' => $category,
            'topCategory' =>[]]
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->data;
        $data['created_admin_user_id'] = Auth::user()->id;
        try {
            GoodsCategory::create($data);
            return response()->json(['code' => 1, 'message' => '添加成功']);
        } catch (Exception $exception) {
            return response()->json(['code' => 0, 'message' => '添加失败']);
        }
    }

    /**
     * 分类状态
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function status(Request $request)
    {
        $goodsCategory = GoodsCategory::find($request->id);
        if ($goodsCategory) {
            $goodsCategory->status = $request->status;
            $goodsCategory->created_admin_user_id = Auth::user()->id;
            $goodsCategory->save();
            return response()->json(['code' => 1, 'message' => '修改成功']);
        } else {
            return response()->json(['code' => 0, 'message' => '修改失败']);
        }
    }

    /**
     * @param Request $request
     * @param $Id
     */
    public function destroy(Request $request, $Id)
    {

    }
}
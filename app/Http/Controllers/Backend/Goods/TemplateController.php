<?php

namespace App\Http\Controllers\Backend\Goods;

use Auth, Config, \Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\GoodsTemplate;

/**
 * Class TemplateController
 * @package App\Http\Controllers\Backend
 */
class TemplateController extends Controller
{
    /**
     * 商品模版
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $name = $request->name;
        $goodsTemplates = GoodsTemplate::orderBy('id', 'desc')->name($name)->paginate(30);

        return view('backend.goods.template.index', compact('goodsTemplates', 'name'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request, $id)
    {
        $filedName = Config::get('goodsTemplate.field_name');
        $filedType= Config::get('goodsTemplate.field_type');

        return view('backend.goods.template.show', compact('filedName', 'filedType'));
    }

    /**
     * @param Request $request
     */
    public function create(Request $request)
    {

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            GoodsTemplate::create($request->data);
            return response()->json(['code' => 1, 'message' => '添加成功']);
        } catch (Exception $exception) {
            return response()->json(['code' => 0, 'message' => '添加失败']);
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
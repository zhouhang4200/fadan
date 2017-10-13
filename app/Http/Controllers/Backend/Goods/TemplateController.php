<?php

namespace App\Http\Controllers\Backend\Goods;

use Auth, Config;
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
        $goodsTemplates = GoodsTemplate::all();

        return view('backend.goods.template.index', compact('goodsTemplates'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request, $id)
    {
        $filedName = Config::get('goodstemplate.filed_name');
        $filedType= Config::get('goodstemplate.filed_type');

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
     */
    public function store(Request $request)
    {

    }

    /**
     * @param Request $request
     * @param $Id
     */
    public function destroy(Request $request, $Id)
    {

    }
}
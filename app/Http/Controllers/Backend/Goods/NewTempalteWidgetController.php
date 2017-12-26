<?php

namespace App\Http\Controllers\Backend\Goods;

use App\Models\WidgetType;
use App\Repositories\Backend\GameRepository;
use App\Repositories\Backend\ServiceRepository;
use Auth, Config, \Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Game;
use App\Models\Service;
use App\Models\GoodsTemplate;

/**
 * Class NewTemplateController
 * @package App\Http\Controllers\Backend\Goods
 */
class NewTemplateController extends Controller
{
    /**
     * 配置模版
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function config(Request $request)
    {
        return view('backend.goods.template.config');
    }
}
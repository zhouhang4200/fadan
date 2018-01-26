<?php
namespace App\Http\Controllers\Backend\Businessman;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * 商户保证金
 * Class CautionMoneyController
 * @package App\Http\Controllers\Backend\Businessman
 */
class CautionMoneyController extends Controller
{
    /**
     * 保证金列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('backend.businessman.caution-money.index');
    }

    /**
     * 添加
     * @param Request $request
     */
    public function store(Request $request)
    {

    }
}

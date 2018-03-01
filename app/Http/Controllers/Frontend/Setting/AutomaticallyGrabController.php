<?php

namespace App\Http\Controllers\Frontend\Setting;

use App\Exceptions\CustomException;
use App\Models\AutomaticallyGrabGoods;
use App\Models\UserSetting;
use App\Repositories\Backend\ServiceRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

/**
 * 自动抓取淘宝订单设置
 * Class AutomaticallyGrabController
 * @package App\Http\Controllers\Frontend\Setting
 */
class AutomaticallyGrabController extends Controller
{
    /**
     * 配置列表
     * @param Request $request
     * @param ServiceRepository $serviceRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function goods(Request $request, ServiceRepository $serviceRepository)
    {
        $foreignGoodsId = $request->foreign_goods_id;
        $services = $serviceRepository->available();

        $automaticallyGrabGoods = AutomaticallyGrabGoods::where('user_id', Auth::user()->getPrimaryUserId())
            ->orderBy('id', 'desc')
            ->paginate(20);

        if ($request->ajax()) {
            return response()->json(\View::make('frontend.setting.automatically-grab.list', [
                'automaticallyGrabGoods' => $automaticallyGrabGoods,
                'foreignGoodsId' => $foreignGoodsId,
            ])->render());
        }

        return view('frontend.setting.automatically-grab.index', compact('automaticallyGrabGoods', 'foreignGoodsId', 'services'));
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function add(Request $request, ServiceRepository $serviceRepository)
    {
        $goodsId = $request->foreign_goods_id;
        $serviceId = $request->service_id;

        if (!is_numeric($goodsId)) {
            return response()->ajax(0, '商品ID不合法');
        }

        if (!in_array($serviceId, array_flip($serviceRepository->available()->toArray()))) {
            return response()->ajax(0, '类型不合法');
        }

        try {
            AutomaticallyGrabGoods::create([
                'user_id' => Auth::user()->getPrimaryUserId(),
                'service_id' => $serviceId,
                'foreign_goods_id' => $goodsId,
                'remark' => $request->remark,
            ]);
            return response()->ajax(1, '添加成功');
        } catch (CustomException $exception){
            return response()->ajax(0, '添加失败');
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function delete(Request $request)
    {
         AutomaticallyGrabGoods::where('user_id', Auth::user()->getPrimaryUserId())->where('id', $request->id)->delete();
        return response()->ajax(1, '删除成功');
    }
}
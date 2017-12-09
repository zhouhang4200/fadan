<?php

namespace App\Http\Controllers\Backend\Order;

use Auth, View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\AfterService;
use App\Models\Order as OrderModel;
use App\Repositories\Backend\AfterServiceRepository;

/**
 * 订单售后处理
 * Class OrderController
 * @package App\Http\Controllers\Backend
 */
class AfterServiceController extends Controller
{
    /**
     * @var AfterServiceRepository
     */
    protected  $afterServiceRepository;

    /**
     * AfterServiceController constructor.
     * @param AfterServiceRepository $afterServiceRepository
     */
    public function __construct(AfterServiceRepository $afterServiceRepository)
    {
        $this->afterServiceRepository = $afterServiceRepository;
    }

    /**
     * 售后订单
     * @param Request $request
     * @return mixed
     * @internal param AfterServiceRepository $afterServiceRepository
     */
    public function index(Request $request)
    {
        $orderNo = $request->order_no;
        $status = $request->status;
        $orderCreatorUserId = $request->order_creator_user_id;

        $orders = $this->afterServiceRepository->dataList(compact('orderNo', 'status', 'orderCreatorUserId'));

        return view('backend.order.after-service.index')->with([
            'orders' => $orders,
            'orderNo' => $orderNo,
            'status' => $status,
            'orderCreatorUserId' => $orderCreatorUserId,
        ]);
    }

    /**
     * 申请售后
     * @param Request $request
     * @return mixed
     */
    public function apply(Request $request)
    {
        $order = OrderModel::where('no', $request->no)->first();

        if ($order) {
            if ($order->amount < $request->amount) {
                return response()->ajax(0, "退款金额不可大于订单总金额");
            }
            if (empty($request->remark)) {
                return response()->ajax(0, "说明不能为空");
            }
            // 创建退款申请记录
            AfterService::create([
                'order_no' => $order->no,
                'order_creator_user_id' => $order->creator_user_id,
                'order_gainer_user_id' => $order->gainer_user_id,
                'apply_admin_user_id' => Auth::user()->id,
                'original_amount' => $order->amount,
                'refund_amount' => $request->amount,
                'apply_remark' => $request->remark,
                'apply_date' => date('Y-m-d H:i:s'),
            ]);
            return response()->ajax(1, "申请成功等待运营人员审核");
        }
        return response()->ajax(0, "订单不存在");
    }

    /**
     * 审核申请
     */
    public function auditing(Request $request)
    {
        $orderNo = $request->no;
        $status = $request->status;
        $remark = $request->remark;

        if (in_array($status, [2, 3])) {
            $afterService = AfterService::where(['order_no' => $orderNo, 'status' => 1])->first();
            if ($afterService) {
                $afterService->auditing_admin_user_id = Auth::user()->id;
                $afterService->auditing_date = date('Y-m-d H:i:s');
                $afterService->auditing_remark = $remark;
                $afterService->status = $status;
                $afterService->save();
                return response()->ajax(1, "审核完成");
            }
            return response()->ajax(0, "订单不存在");
        }
        return response()->ajax(0, "类型错误");
    }

    /**
     * 确认申请
     * @param Request $request
     */
    public function confirm(Request $request)
    {
        $orderNo = $request->no;

        $afterService = AfterService::where(['order_no' => $orderNo, 'status' => 2])->first();
        if ($afterService) {
            $afterService->confirm_admin_user_id = Auth::user()->id;
            $afterService->confirm_date = date('Y-m-d H:i:s');
            $afterService->status = 4;
            $afterService->save();
            return response()->ajax(1, "完成");
        }
        return response()->ajax(0, "订单不存在");
    }
}

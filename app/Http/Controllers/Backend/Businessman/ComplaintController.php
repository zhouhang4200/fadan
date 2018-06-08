<?php

namespace App\Http\Controllers\Backend\Businessman;

use App\Models\Order;
use Auth, Asset, DB;
use App\Extensions\Asset\Expend;
use App\Extensions\Asset\Income;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BusinessmanComplaint;
use App\Exceptions\AssetException;
use \Exception;

/**
 * 商户投诉
 * Class ComplaintController
 * @package App\Http\Controllers\Backend\Businessman
 */
class ComplaintController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $complaint = BusinessmanComplaint::filter(['orderNo' => $request->order_no])->paginate(30);

        return view('backend.businessman.complaint.index')->with([
           'complaint' => $complaint,
        ]);
    }

    /**
     * 添加视图
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('backend.businessman.complaint.create');
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function store(Request $request)
    {
        $amount = $request->amount; // 要求赔偿金额
        $orderNo = $request->order_no; // 订单号
        $complaintPrimaryUserId = $request->complaint_primary_user_id; // 投诉商户ID
        $beComplaintPrimaryUserId = $request->be_complaint_primary_user_id; // 被投诉商户ID

        $this->validate($request, [
            'complaint_primary_user_id' => 'bail|required|integer',
            'be_complaint_primary_user_id' => 'bail|required|integer|different:complaint_primary_user_id',
            'order_no'    => 'bail|required|min:22|max:22',
            'amount'  => 'bail|required|numeric',
            'remark'  => 'bail|required|string|max:200',
        ],[],[
            'complaint_primary_user_id' => '投诉人ID',
            'be_complaint_primary_user_id' => '被投诉人ID',
            'order_no' => '订单号',
            'amount' => '要求赔偿金额',
            'remark' => '备注',
        ]);

        DB::beginTransaction();
        try {
            // 扣被商户投诉钱
            Asset::handle(new Expend($amount, 9, $orderNo, '订单投诉支出', $beComplaintPrimaryUserId));
            // 增加投诉商户钱
            Asset::handle(new Income($amount, 17, $orderNo, '订单投诉收入', $complaintPrimaryUserId));
            // 创建记录
            BusinessmanComplaint::create($request->all());
        } catch (AssetException $exception)  {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors($exception->getMessage());
        } catch (Exception $exception)  {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors($exception->getMessage());
        }
        DB::commit();

        return redirect(route('frontend.user.complaint.index'))->withInput()->with([
            'message' => '添加成功'
        ]);
    }

    /**
     * 查询订单
     * @param Request $request
     */
    public function queryOrder(Request $request)
    {
//        try {
            return response()->ajax(1, 'success', Order::where('no', $request->no)->first());
//        } catch (\Exception $exception) {
//
//        }
    }
}
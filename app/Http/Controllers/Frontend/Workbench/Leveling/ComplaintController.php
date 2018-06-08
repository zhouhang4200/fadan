<?php

namespace App\Http\Controllers\Frontend\Workbench\Leveling;

use App\Models\Order;
use Auth, Asset, DB;
use App\Extensions\Asset\Expend;
use App\Extensions\Asset\Income;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BusinessmanComplaint;
use App\Exceptions\AssetException;
use \Exception;
use App\Repositories\Frontend\GameRepository;

/**
 * 商户投诉
 * Class ComplaintController
 * @package App\Http\Controllers\Frontend\Workbench\Leveling
 */
class ComplaintController extends Controller
{
    /**
     * @param GameRepository $gameRepository
     * @return $this
     */
    public function index(GameRepository $gameRepository)
    {
        $games = $gameRepository->availableByServiceId(4);

        return view('frontend.v1.workbench.leveling.complaint', compact('games'));
    }

    /**
     * @param Request $request
     * @return array
     */
    public function listData(Request $request)
    {
        $complaint = BusinessmanComplaint::filter([
            'orderNo' => $request->order_no,
            'gameId' => $request->game_id,
            'startDate' => $request->start_date,
            'endDate' => $request->end_date,
            'status' => $request->status,
        ])
            ->with(['order', 'orderDetail', 'taobaoTrade'])
            ->where('complaint_primary_user_id', auth()->user()->getPrimaryUserId())
            ->paginate(50);

        $complaintCount =  BusinessmanComplaint::filter([
            'orderNo' => $request->order_no,
            'gameId' => $request->game_id,
            'startDate' => $request->start_date,
            'endDate' => $request->end_date,
        ])
            ->select(\DB::raw('status, count(1) as count'))
            ->where('complaint_primary_user_id', auth()->user()->getPrimaryUserId())
            ->groupBy('status')->pluck('count', 'status')->toArray();

        $countInit = [0 => 0, 1=> 0, 2 => 0, 3 => 0, 4 => 0];

        foreach ($countInit as $key => $item) {
            if (!isset($complaintCount[$key])) {
                $complaintCount[$key] = 0;
            }
        }

        $complaintArr = [];
        foreach ($complaint as $item) {
            $orderDetail = $item->orderDetail->pluck('field_value', 'field_name');
            $complaintArr[] = [
                'status_text' => $item->statusText(),
                'amount' => $item->amount,
                'order_status' => ! is_null(optional($item->order)->status) ? config('order.status_leveling')[optional($item->order)->status] : '',
                'taobao_status' => ! is_null(optional($item->taobaoTrade)->trade_status) ? config('order.taobao_trade_status')[optional($item->taobaoTrade)->trade_status] : '',
                'no' => optional($item->order)->no,
                'foreign_order_no' => optional($item->order)->foreign_order_no,
                'game_name' => optional($item->order)->game_name,
                'created_at' => $item->created_at->toDateTimeString(),
                'third_name' => isset($orderDetail['third']) ? config('order.third')[(int)$orderDetail['third']] : '',
                'third_order_no' => isset($orderDetail['third_order_no']) ? $orderDetail['third_order_no'] : '',
            ];
        }

        return [
            'code' => 0,
            'msg' => '',
            'count' => $complaint->total(),
            'data' => $complaintArr,
            'status_count' => $complaintCount
        ];
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
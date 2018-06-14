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
 * Class ComplaintsController
 * @package App\Http\Controllers\Frontend\Workbench\Leveling
 */
class ComplaintsController extends Controller
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
//            'status' => $request->status,
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
     * 创建投诉
     * @param Request $request
     * @return $this
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'order_no'    => 'bail|required|min:22|max:22',
            'amount'  => 'bail|required|numeric',
            'remark'  => 'bail|required|string|max:200',
        ],[],[
            'order_no' => '订单号',
            'amount' => '要求赔偿金额',
            'remark' => '备注',
        ]);
        // 查找订单
        $orderInfo = Order::where('no', $request->order_no)->first();

        $complaintPrimaryUserId = 0; // 投诉 userID
        $beComplaintPrimaryUserId = 0; // 被投诉 userID

        // 如果发单人主ID与当前登录账号主ID一样则投诉人是发单人,被投诉人就是接单人.
        // 如果接单人主ID与当前登录账号主ID一样则投诉人是接单人,被投诉人就是发单人
        if ($orderInfo && $orderInfo->creator_primary_user_id == auth()->user()->getPrimaryUserId()) {
            $complaintPrimaryUserId = $orderInfo->creator_primary_user_id; // 投诉
            $beComplaintPrimaryUserId = $orderInfo->gainer_primary_user_id; // 被投诉
        } else if ($orderInfo && $orderInfo->gainer_primary_user_id == auth()->user()->getPrimaryUserId()) {
            $complaintPrimaryUserId = $orderInfo->gainer_primary_user_id; // 投诉
            $beComplaintPrimaryUserId = $orderInfo->creator_primary_user_id; // 被投诉
        }

        DB::beginTransaction();
        try {
            $complaintArr = [];
            // 存储图片
            if ( !empty($request->pic1)) {
                $complaintArr['pic1'] = base64ToImg($request->pic1, 'complaints');
            } else if(!empty($request->pic2)) {
                $complaintArr['pic2'] = base64ToImg($request->pic2, 'complaints');
            } else if(!empty($request->pic3)) {
                $complaintArr['pic3'] = base64ToImg($request->pic3, 'complaints');
            }
            $complaintArr['amount'] = $request->amount;
            $complaintArr['remark'] = $request->remark;
            $complaintArr['order_no'] = $orderInfo->order_no;
            $complaintArr['game_id'] = $orderInfo->game_id;
            $complaintArr['foreign_order_no'] = $request->foreign_order_no;
            $complaintArr['complaint_primary_user_id'] = $complaintPrimaryUserId;
            $complaintArr['be_complaint_primary_user_id'] = $beComplaintPrimaryUserId;
            // 创建记录
            BusinessmanComplaint::create($complaintArr);
        } catch (AssetException $exception)  {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors($exception->getMessage());
        } catch (Exception $exception)  {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors($exception->getMessage());
        }
        DB::commit();

        return response()->ajax(1, 'success');
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
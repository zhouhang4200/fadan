<?php

namespace App\Http\Controllers\Frontend\Workbench\Leveling;

use App\Models\Order;
use App\Models\OrderDetail;
use Auth, Asset, DB, Config;
use App\Extensions\Asset\Expend;
use App\Extensions\Asset\Income;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BusinessmanComplaint;
use App\Exceptions\AssetException;
use \Exception;
use App\Repositories\Frontend\GameRepository;
use Illuminate\Mail\Mailer;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Mail;

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
        $status = $request->input('status', 1);
        $complaint = BusinessmanComplaint::filter([
            'orderNo' => $request->order_no,
            'gameId' => $request->game_id,
            'startDate' => $request->start_date,
            'endDate' => $request->end_date,
            'status' => $status,
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
                'id' => $item->id,
                'status' => $item->status,
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

        // 查找是否有投诉
        $businessmanComplaint = BusinessmanComplaint::where('order_no', $request->order_no)
            ->where('complaint_primary_user_id', auth()->user()->getPrimaryUserId())
            ->orderBy('id', 'desc')
            ->first();

        if (!$businessmanComplaint || ($businessmanComplaint && in_array($businessmanComplaint->status, [2, 3, 4]))) {
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
                    $complaintArr['img'][] = base64ToImg($request->pic1, 'complaints');
                }
                if(!empty($request->pic2)) {
                    $complaintArr['img'][] = base64ToImg($request->pic2, 'complaints');
                }
                if(!empty($request->pic3)) {
                    $complaintArr['img'][] = base64ToImg($request->pic3, 'complaints');
                }

                $complaintArr['amount'] = $request->amount;
                $complaintArr['remark'] = $request->remark;
                $complaintArr['order_no'] = $orderInfo->no;
                $complaintArr['game_id'] = $orderInfo->game_id;
                $complaintArr['foreign_order_no'] = $request->foreign_order_no;
                $complaintArr['complaint_primary_user_id'] = $complaintPrimaryUserId;
                $complaintArr['be_complaint_primary_user_id'] = $beComplaintPrimaryUserId;
                $complaintArr['images'] = json_encode($complaintArr['img']);
                $complaintArr['status'] = 1;
                // 创建记录
                BusinessmanComplaint::create($complaintArr);
                // 获取接单平台订单号
                $thirdOrder = OrderDetail::where('order_no', $orderInfo->no)->where('field_name', 'third_order_no')->value('field_value');
                $emailTitle = '淘宝发单平台订单投诉（订单号：' . $thirdOrder . '）';
                // 获取接单平台邮件地址

                $to = config('leveling.third_email')[$orderInfo->gainer_primary_user_id];
                // 发送邮件通知接单平台
                sendMail('smtp.mxhichina.com', 'jinjian@fulu.com', '880203Vic')->send('frontend.emails.complaints',[
                    'order' => $thirdOrder,
                    'amount' => $request->amount,
                    'remark' => $request->remark,
                    'image1' => isset($complaintArr['img'][0]) ? asset($complaintArr['img'][0]) : '',
                    'image2' => isset($complaintArr['img'][1]) ? asset($complaintArr['img'][1]) : '',
                    'image3' => isset($complaintArr['img'][2]) ? asset($complaintArr['img'][2]) : '',
                ],function($message) use ($emailTitle, $to) {
                    $message->from('jinjian@fulu.com', '淘宝发单平台');
                    $message->to($to)->cc('yangfan@fulu.com')->subject($emailTitle);
                });

            } catch (AssetException $exception)  {
                DB::rollBack();
                return response()->ajax(0, $exception->getMessage());
            } catch (Exception $exception)  {
                DB::rollBack();
                return response()->ajax(0, $exception->getMessage());
            }
            DB::commit();
            return response()->ajax(1, '我们已收到您的投诉,将尽快处理');
        }
    }

    /**
     * 查询订单
     * @param Request $request
     */
    public function show(Request $request)
    {
        // 查找投诉
        $businessmanComplaint = BusinessmanComplaint::where('order_no', $request->order_no)
            ->where('complaint_primary_user_id', auth()->user()->getPrimaryUserId())
            ->orderBy('id', 'desc')
            ->first();

        if ($businessmanComplaint) {
            return response()->ajax(1, '查到投诉数据', [
                'id' => $businessmanComplaint->id,
                'status' => $businessmanComplaint->status,
                'amount' => $businessmanComplaint->amount,
                'remark' => $businessmanComplaint->remark,
                'result' => $businessmanComplaint->result,
            ]);
        }
        return response()->ajax(0, '没有投诉数据');
    }

    /**
     * 取消投诉
     * @param Request $request
     */
    public function cancel(Request $request)
    {
        // 查找投诉
        $businessmanComplaint = BusinessmanComplaint::where('id', $request->id)
            ->where('complaint_primary_user_id', auth()->user()->getPrimaryUserId())
            ->orderBy('id', 'desc')
            ->first();

        if ($businessmanComplaint) {
            $businessmanComplaint->status = 2;
            $businessmanComplaint->save();
        }
        return response()->ajax(1, '操作成功');
    }

    /**
     * 图片
     * @param Request $request
     */
    public function images(Request $request)
    {
        $complaint = BusinessmanComplaint::where('id', $request->id)->first();

        if ($complaint) {
            return response()->ajax(1, '获取成功', json_decode($complaint->images, true));
        } else {
            return response()->ajax(0, '没有图片');
        }
    }
}
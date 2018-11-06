<?php

namespace App\Http\Controllers\Frontend\V2\Order\GameLeveling;

use DB;
use Auth;
use App\Models\BusinessmanComplaint;
use App\Http\Controllers\Controller;
use App\Models\GameLevelingOrderBusinessmanComplain;

/**
 * 游戏代练订单投诉控制器
 * Class GameLevelingController
 * @package App\Http\Controllers\Frontend\V2\Order
 */
class BusinessmanComplainController extends Controller
{
    /**
     * @return $this
     */
    public function index()
    {
        return view('frontend.v2.order.game-leveling.businessman-complain.index');
    }

    /**
     * @param 
     * @return array
     */
    public function dataList()
    {
        return GameLevelingOrderBusinessmanComplain::filter(request()->all())
            ->with(['gameLevelingOrder' => function($query) {
                $query->select('trade_no', 'game_id', 'platform_trade_no', 'channel_order_trade_no', 'platform_id');
            }])
            ->where('from_user_id', auth()->user()->getPrimaryUserId())
            ->paginate(50);
    }

    /**
     * 订单投诉数量
     * @return mixed
     */
    public function statusQuantity()
    {
        return GameLevelingOrderBusinessmanComplain::filter(request()->all())
            ->selectRaw('status, count(1) as quantity, from_user_id')
            ->where('from_user_id', auth()->user()->getPrimaryUserId())
            ->groupBy('status')
            ->pluck('quantity', 'status')
            ->toArray();
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
     * @param 
     * @return $this
     */
    public function store()
    {
        $this->validate(request(), [
            'order_no'    => 'bail|required|min:22|max:22',
            'amount'  => 'bail|required|numeric',
            'remark'  => 'bail|required|string|max:200',
        ],[],[
            'order_no' => '订单号',
            'amount' => '要求赔偿金额',
            'remark' => '备注',
        ]);

        // 查找是否有投诉
        $businessmanComplaint = BusinessmanComplaint::where('order_no', request()->order_no)
            ->where('complaint_primary_user_id', auth()->user()->getPrimaryUserId())
            ->orderBy('id', 'desc')
            ->first();

        if (!$businessmanComplaint || ($businessmanComplaint && in_array($businessmanComplaint->status, [2, 3, 4]))) {
            // 查找订单
            $orderInfo = Order::where('no', request()->order_no)->first();

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
                if ( !empty(request()->pic1)) {
                    $complaintArr['img'][] = base64ToImg(request()->pic1, 'complaints');
                }
                if(!empty(request()->pic2)) {
                    $complaintArr['img'][] = base64ToImg(request()->pic2, 'complaints');
                }
                if(!empty(request()->pic3)) {
                    $complaintArr['img'][] = base64ToImg(request()->pic3, 'complaints');
                }

                $complaintArr['amount'] = request()->amount;
                $complaintArr['remark'] = request()->remark;
                $complaintArr['order_no'] = $orderInfo->no;
                $complaintArr['game_id'] = $orderInfo->game_id;
                $complaintArr['foreign_order_no'] = $orderInfo->foreign_order_no;
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
                    'amount' => request()->amount,
                    'remark' => request()->remark,
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
     * @return mixed
     */
    public function show()
    {
        // 查找投诉
        $businessmanComplaint = BusinessmanComplaint::where('order_no', request()->order_no)
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
     * @return mixed
     */
    public function cancel()
    {
        // 查找投诉
        $businessmanComplaint = GameLevelingOrderBusinessmanComplain::where('id', request()->id)
            ->where('from_user_id', auth()->user()->getPrimaryUserId())
            ->first();

        if ($businessmanComplaint) {
            $businessmanComplaint->status = 2;
            $businessmanComplaint->save();
            return response()->ajax(1, '操作成功');
        }
        return response()->ajax(0, '操作失败');

    }

    /**
     * 图片
     * @param
     * @return
     */
    public function images()
    {
        $complaint = GameLevelingOrderBusinessmanComplain::where('id', request()->id)->first();

        if ($complaint) {
            return response()->ajax(1, '获取成功', json_decode($complaint->images, true));
        } else {
            return response()->ajax(0, '没有图片');
        }
    }
}
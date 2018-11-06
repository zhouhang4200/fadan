<?php

namespace App\Http\Controllers\Frontend\V2\Order\GameLeveling;

use App\Exceptions\AssetException;
use App\Models\GameLevelingOrder;
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
            'trade_no'    => 'bail|required|min:22|max:22',
            'amount'  => 'bail|required|numeric',
            'reason'  => 'bail|required|string|max:200',
        ],[],[
            'trade_no' => '订单号',
            'amount' => '要求赔偿金额',
            'reason' => '原因',
        ]);

        // 查找是否有投诉
        $businessmanComplaint = GameLevelingOrderBusinessmanComplain::where('game_leveling_order_trade_no', request()->trade_no)
            ->where('from_user_id', auth()->user()->getPrimaryUserId())
            ->orderBy('id', 'desc')
            ->first();

        if (!$businessmanComplaint || ($businessmanComplaint && in_array($businessmanComplaint->status, [2, 3, 4]))) {
            // 查找订单
            $orderInfo = GameLevelingOrder::where('trade_no', request()->trade_no)->first();

            $fromUserId = 0; // 投诉 userID
            $toUserId = 0; // 被投诉 userID

            // 如果发单人主ID与当前登录账号主ID一样则投诉人是发单人,被投诉人就是接单人.
            if ($orderInfo && $orderInfo->parent_user_id == auth()->user()->getPrimaryUserId()) {
                $fromUserId = $orderInfo->parent_user_id; // 投诉
                $toUserId = $orderInfo->take_parent_user_id; // 被投诉
            } else if ($orderInfo && $orderInfo->take_parent_user_id == auth()->user()->getPrimaryUserId()) {
                $fromUserId = $orderInfo->take_parent_user_id; // 投诉
                $toUserId = $orderInfo->parent_user_id; // 被投诉
            }

            DB::beginTransaction();
            try {
                $complaintArr = [];
                // 存储图片
                if (isset(request()->images[0])) {
                    $complaintArr['images'][] = base64ToImg(request()->images[0], 'complaints');
                }
                if(isset(request()->images[1])) {
                    $complaintArr['images'][] = base64ToImg(request()->images[1], 'complaints');
                }
                if(isset(request()->images[2])) {
                    $complaintArr['images'][] = base64ToImg(request()->images[2], 'complaints');
                }

                $complaintArr['amount'] = request()->amount;
                $complaintArr['reason'] = request()->reason;
                $complaintArr['game_leveling_order_trade_no'] = $orderInfo->trade_no;
                $complaintArr['from_user_id'] = $fromUserId;
                $complaintArr['to_user_id'] = $toUserId;
                $complaintArr['images'] = json_encode($complaintArr['images']);
                $complaintArr['status'] = 1;
                // 创建记录
                GameLevelingOrderBusinessmanComplain::create($complaintArr);

                // 拼接发送邮件标题
                $emailTitle = '淘宝发单平台订单投诉（订单号：' . $orderInfo->channel_order_trade_no . '）';

                // 获取接单平台邮件地址
                $to = config('leveling.third_email')[$orderInfo->take_parent_user_id];

                // 发送邮件通知接单平台
                sendMail('smtp.mxhichina.com', 'jinjian@fulu.com', '880203Vic')
                    ->send('frontend.emails.complaints',[
                        'order' => $orderInfo->channel_order_trade_no,
                        'amount' => request()->amount,
                        'remark' => request()->reason,
                        'image1' => isset($complaintArr['images'][0]) ? asset($complaintArr['images'][0]) : '',
                        'image2' => isset($complaintArr['images'][1]) ? asset($complaintArr['images'][1]) : '',
                        'image3' => isset($complaintArr['images'][2]) ? asset($complaintArr['images'][2]) : '',
                ],function($message) use ($emailTitle, $to) {
                    $message->from('jinjian@fulu.com', '淘宝发单平台');
                    $message->to($to)->cc('yangfan@fulu.com')->subject($emailTitle);
                });

            } catch (AssetException $exception)  {
                DB::rollBack();
                return response()->ajax(0, $exception->getMessage() . '-' . $exception->getLine() . '-' . $exception->getFile());
            } catch (\Exception $exception)  {
                DB::rollBack();
                return response()->ajax(0, $exception->getMessage() . '-' . $exception->getLine(). '-' . $exception->getFile());
            }
            DB::commit();
            return response()->ajax(1, '我们已收到您的投诉,将尽快处理');
        } else {
            return response()->ajax(1, '您的投诉正在处理中！');
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
        } else {
            return response()->ajax(0, '操作失败');
        }
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
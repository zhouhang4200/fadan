<?php

namespace App\Http\Controllers\Backend\Punish;

use DB;
use Redis;
use Asset;
use Excel;
use Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Revision;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use App\Models\PunishOrReward;
use App\Extensions\Asset\Consume;
use App\Http\Controllers\Controller;
use App\Models\PunishOrRewardRevision;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PunishController extends Controller
{
    protected static $extensions = ['png', 'jpg', 'jpeg', 'gif'];
    /**
     * 奖惩列表
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // 找出奖惩表里面出现的用户id
        $userIds = PunishOrReward::pluck('user_id')->unique();
        $users = User::whereIn('id', $userIds)->get();

        $startDate = $request->startDate;
        $endDate = $request->endDate;
        $type = $request->type;
        $status = $request->status;
        $userId = $request->user_id;
        $orderNo = $request->order_no;
        $fullUrl = $request->fullUrl();

        $filters = compact('startDate', 'endDate', 'type', 'userId', 'status', 'orderNo');
        // 导出
        if ($request->export) {
            return $this->export($filters);
        }
        // 模型里面有筛选
        $punishes = PunishOrReward::filter($filters)->latest('created_at')->paginate(config('backend.page'));

        return view('backend.punish.index', compact('users', 'startDate', 'endDate', 'type', 'userId', 'punishes', 'status', 'orderNo', 'fullUrl'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function create()
    // {
    //     $users = User::where('parent_id', 0)->get();

    //     $start = Carbon::now()->subDays(2)->startOfDay()->toDateTimeString();
        
    //     $end = Carbon::now()->toDateTimeString();

    //     $orders = Order::whereBetween('created_at', [$start, $end])->pluck('no');

    //     return view('backend.punish.create', compact('users', 'orders'));
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(Request $request)
    // {
    //     $orderNo = static::createOrderId();

    //     $data = $request->all();

    //     $data['type'] = 0;

    //     $data['deadline'] = $request->deadline . ' 23:59:59';

    //     $data['no'] = $orderNo;

    //     $this->validate($request, PunishOrReward::rules(), PunishOrReward::messages());

    //     $res = PunishOrReward::create($data);
        
    //     if (! $res) {

    //         return back()->withInput()->with('createFail', '添加失败！');
    //     }
    //     return redirect(route('punishes.index'))->with('succ', '添加成功!');


    // }

    /**
     * 创建订单号
     * @return string
     */
    // public static function createOrderId()
    // {
    //     // 14位长度当前的时间 20150709105750
    //     $orderdate = date('YmdHis');
    //     // 今日订单数量
    //     $orderquantity = Redis::incr('market:order:punish:' . date('Ymd'));

    //     return $orderdate . str_pad($orderquantity, 9, 0, STR_PAD_LEFT);
    // }

    /**
     * 奖惩详细
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $punish = PunishOrReward::find($id);

        return view('backend.punish.show', compact('punish'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function edit($id)
    // {
    //     $punish = PunishOrReward::find($id);

    //     return view('backend.punish.edit', compact('punish'));
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, $id)
    // {
    //     $this->validate($request, PunishOrReward::rules($id), PunishOrReward::messages());

    //     $data['money'] = $request->money;

    //     $data['remark'] = $request->remark;

    //     $punish = PunishOrReward::find($id);

    //     $int = $punish->update($data);

    //     if ($int > 0) {

    //         return redirect(route('punishes.index'))->with('succ', '更新成功!');
    //     }

    //     return back()->withInput()->with('updateFail', '更新失败!');
    // }

    /**
     * 列表页，直接撤销某个商户还未确认以及未申诉的奖惩记录
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $punish = PunishOrReward::find($id);

        // 如果为加权重
        if (($punish->type == 3 || $punish->type == 5) && $punish->confirm == 1) {
            $punish->status = 11;
            $punish->confirm = 1;
            $punish->save();

            return response()->json(['code' => '1', 'message' => '撤销成功!']);
        } else {  
            $bool = $punish->delete();

            if ($bool) {

                return response()->json(['code' => '1', 'message' => '撤销成功!']);
            }
            return response()->json(['code' => '2', 'message' => '撤销失败!']);
        }

    }

    // public function orders(Request $request)
    // {
    //     $userId = $request->id;

    //     $orders = Order::where('gainer_primary_user_id', $userId)->pluck('no');

    //     return response()->json(['orders' => $orders]);
    // }

    /**
     * 点击图片 ajax 上传
     * @param  Illuminate\Http\Request
     * @return json
     */
    public function uploadImages(Request $request)
    {
        if ($request->hasFile('file')) {

            $file = $request->file('file');

            $path = public_path("/resources/punish/".date('Ymd')."/");

            $imagePath = $this->uploadImage($file, $path);

            return response()->json(['code' => 1, 'path' => $imagePath]);
        }
    }

    /**
     * 图片上传，返回图片路径
     * @param  Symfony\Component\HttpFoundation\File\UploadedFile $file 
     * @param  $path string
     * @return string
     */
    public function uploadImage(UploadedFile $file, $path)
    {   
        $extension = $file->getClientOriginalExtension();

        if ($extension && ! in_array(strtolower($extension), static::$extensions)) {

            return response()->json(['code' => 2, 'path' => $imagePath]);
        }

        if (! $file->isValid()) {

            return response()->json(['code' => 2, 'path' => $imagePath]);
        }

        if (!file_exists($path)) {

            mkdir($path, 0755, true);
        }
        $randNum = rand(1, 100000000) . rand(1, 100000000);

        $fileName = time().substr($randNum, 0, 6).'.'.$extension;

        $path = $file->move($path, $fileName);

        $path = strstr($path, '/resources');

        return str_replace('\\', '/', $path);
    }

    /**
     *撤销操作
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancel($id)
    {
        try {
            $punish = PunishOrReward::find($id);
            //奖励金额撤销
            if ($punish->type == 1) {
                // 撤销奖励时扣款
                $bool = Asset::handle(new Consume($punish->add_money, 3, $punish->no, '奖励撤销扣款', $punish->user_id));

                if ($bool) {
                    // 如果商户没确认，撤销同时，改为撤销状态并软删除该条记录
                    if ($punish->confirm == 0) {

                        PunishOrReward::where('id', $id)->update(['status' => 11, 'confirm' => 1]);

                        $punish = PunishOrReward::find($id);

                        $data = [
                            [
                                'punish_or_reward_id' => $punish->id,
                                'operate_style' => 'status',
                                'no' => $punish->no,
                                'order_no' => $punish->order_no,
                                'before_value' => 2,
                                'after_value' => 11,
                                'admin_user_name' => AdminUser::where('id', Auth::id())->value('name') ?? '系统',
                                'created_at' => new \DateTime(),
                                'updated_at' => new \DateTime(),
                            ],
                            [
                                'punish_or_reward_id' => $punish->id,
                                'operate_style' => 'confirm',
                                'no' => $punish->no,
                                'order_no' => $punish->order_no,
                                'before_value' => 0,
                                'after_value' => 1,
                                'admin_user_name' => AdminUser::where('id', Auth::id())->value('name') ?? '系统',
                                'created_at' => new \DateTime(),
                                'updated_at' => new \DateTime(),
                            ],
                        ];
                        // 奖惩日志
                        DB::table('punish_or_reward_revisions')->insert($data);
                        // 用户没确认，软删除记录
                        $punish->delete();
                    } else {
                        // 如果商户确认了，就不删除，显示撤销状态
                        PunishOrReward::where('id', $id)->update(['status' => 11]);

                        $punish = PunishOrReward::find($id);

                        $data = [
                            [
                                'punish_or_reward_id' => $punish->id,
                                'operate_style' => 'status',
                                'no' => $punish->no,
                                'order_no' => $punish->order_no,
                                'before_value' => 2,
                                'after_value' => 11,
                                'admin_user_name' => AdminUser::where('id', Auth::id())->value('name') ?? '系统',
                                'created_at' => new \DateTime(),
                                'updated_at' => new \DateTime(),
                            ]
                        ];
                        DB::table('punish_or_reward_revisions')->insert($data);
                    }

                } else {
                    return response()->json(['code' => '2', 'message' => '奖励撤销扣款失败!']);
                } 
                // 写多态关联
                if (!$punish->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                    DB::rollback();
                    throw new Exception('操作失败');
                }

                if (!$punish->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                    DB::rollback();
                    throw new Exception('操作失败');
                }
                return response()->json(['code' => '1', 'message' => '撤销奖励成功{$punish->add_money}元!']);

            } elseif ($punish->type == 5) { 
                //撤销禁止接单的处罚
                $punish = PunishOrReward::find($id);
                // 日志
                $bool = $punish->delete();

                if ($bool) {
                    return response()->json(['code' => '1', 'message' => '撤销成功!']);
                }
                return response()->json(['code' => '2', 'message' => '撤销失败!']);
            } else {
                return response()->json(['code' => '2', 'message' => '操作异常!']);
            }   
        } catch (Exception $e) {
            
        }
    }

    /**
     * 奖惩日志
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function record(Request $request)
    {
        $startDate = $request->startDate;
        $endDate = $request->endDate;
        $orderNo = $request->order_no;
        // 生成查询数组
        $filters = compact('startDate', 'endDate', 'orderNo');

        $punishRecords = PunishOrRewardRevision::filter($filters)->paginate(config('backend.page'));

        // $query = \DB::table('revisions')
        //         ->select(\DB::raw("revisions.id, revisions.revisionable_id, revisions.key, revisions.old_value, revisions.new_value, revisions.created_at, punish_or_rewards.order_id, punish_or_rewards.order_no , admin_users.name"))
        //         ->leftjoin('punish_or_rewards', function ($join) {
        //             $join->on('punish_or_rewards.id', '=', 'revisions.revisionable_id');
        //         })
        //         ->leftjoin('admin_users', function ($join) {
        //             $join->on('admin_users.id', '=', 'revisions.user_id');
        //         })
        //         ->where('revisions.revisionable_type', 'App\Models\PunishOrReward');

        // if ($startDate && empty($endDate)) {

        //     $query->where('created_at', '>=', $startDate);
        // }

        // if ($endDate && empty($startDate)) {

        //     $query->where('created_at', '<=', $endDate . " 23:59:59");
        // }

        // if ($endDate && $startDate) {

        //     $query->whereBetween('created_at', [$startDate, $endDate . " 23:59:59"]); 
        // }

        // if ($orderId) {

        //     $punishIds = PunishOrReward::where('order_no', $orderId)->pluck('id');

        //     $query->whereIn('revisionable_id', $punishIds);
        // }
                     
        // $punishRecords = $query->latest('created_at')->paginate(config('backend.page'));
               
        return view('backend.punish.record', compact('punishRecords', 'startDate', 'endDate', 'orderNo'));
    }

    /**
     * 奖惩列表导出.多分页导出
     * @param  [type] $filters [description]
     * @return [type]          [description]
     */
    public function export($filters)
    {
        $punishes = PunishOrReward::filter($filters)->latest('created_at')->withTrashed()->get();
        // 标题
        $title = [
            '序号',
            '订单号',
            '关联订单号',
            '用户id',
            '类型',
            '状态',
            '罚款金额',
            '最后期限',
            '初始权重',
            '奖惩权重',
            '最终权重',
            '生效时间',
            '截止时间',
            '奖励金额',
            '凭证照片',
            '备注',
            '商家确认',
            '创建时间',
            '更新时间',
            '删除时间',
        ];
        // 数组分割,反转
        $chunkPunishes = array_chunk(array_reverse($punishes->toArray()), 1000);

        Excel::create(iconv('UTF-8', 'gbk', '奖惩情况'), function ($excel) use ($chunkPunishes, $title) {

            foreach ($chunkPunishes as $chunkPunish) {
                // 内容
                $datas = [];
                foreach ($chunkPunish as $key => $punish) {
                    $datas[] = [
                        $punish['id'],
                        $punish['no'],
                        $punish['order_no'],
                        $punish['user_id'],
                        $punish['type'],
                        $punish['status'],
                        $punish['sub_money'] ?? '--',
                        $punish['deadline'] ?? '--',
                        $punish['before_weight_value'] ?? '--',
                        $punish['ratio'] ?? '--',
                        $punish['after_weight_value'] ?? '--',
                        $punish['start_time'] ?? '--',
                        $punish['end_time'] ?? '--',
                        $punish['add_money'] ?? '--',
                        json_encode($punish['voucher']) ?? '--',
                        $punish['remark'] ?? '--',
                        $punish['confirm'] ?? '--',
                        $punish['created_at'] ?? '--',
                        $punish['updated_at'] ?? '--',
                        $punish['deleted_at'] ?? '--',
                    ];
                }
                // 将标题加入到数组
                array_unshift($datas, $title);
                // 每页多少数据
                $excel->sheet("页数", function ($sheet) use ($datas) {
                    $sheet->rows($datas);             
                });
            }
        })->export('xls');
    }

    /**
     * 订单详情里面的-》奖惩日志详情
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function recordShow($id)
    {
        try {
            $order = Order::find($id);

            $punishRecords = PunishOrRewardRevision::where('order_no', $order->no)->get();

            return view('backend.punish.detail', compact('punishRecords'));

        } catch (Exception $e) {

        }
    }
}

<?php

namespace App\Http\Controllers\Backend\Punish;

use Redis;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\PunishOrReward;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PunishController extends Controller
{
    protected static $extensions = ['png', 'jpg', 'jpeg', 'gif'];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $userIds = PunishOrReward::pluck('user_id')->unique();

        $users = User::whereIn('id', $userIds)->get();

        $startDate = $request->startDate;

        $endDate = $request->endDate;

        $type = $request->type;

        $status = $request->status;

        $userId = $request->user_id;

        $filters = compact('startDate', 'endDate', 'type', 'userId', 'status');

        $punishes = PunishOrReward::filter($filters)->latest('created_at')->paginate(config('backend.page'));

        return view('backend.punish.index', compact('users', 'startDate', 'endDate', 'type', 'userId', 'punishes', 'status'));
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

    //     $data['order_no'] = $orderNo;

    //     $this->validate($request, PunishOrReward::rules(), PunishOrReward::messages());

    //     $res = PunishOrReward::create($data);
        
    //     if (! $res) {

    //         return back()->withInput()->with('createFail', '添加失败！');
    //     }
    //     return redirect(route('punishes.index'))->with('succ', '添加成功!');


    // }

    /**
     * 获取订单号
     * @return string
     */
    public static function createOrderId()
    {
        // 14位长度当前的时间 20150709105750
        $orderdate = date('YmdHis');
        // 今日订单数量
        $orderquantity = Redis::incr('market:order:punish:' . date('Ymd'));

        return $orderdate . str_pad($orderquantity, 9, 0, STR_PAD_LEFT);
    }

    /**
     * Display the specified resource.
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $bool = PunishOrReward::where('id', $id)->delete();

        if ($bool) {

            return response()->json(['code' => '1', 'message' => '删除成功!']);
        }
        return response()->json(['code' => '2', 'message' => '删除失败!']);
    }

    public function orders(Request $request)
    {
        $userId = $request->id;

        $orders = Order::where('gainer_primary_user_id', $userId)->pluck('no');

        return response()->json(['orders' => $orders]);
    }

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
     * 图片上传
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
}

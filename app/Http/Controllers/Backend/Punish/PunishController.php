<?php

namespace App\Http\Controllers\Backend\Punish;

use Redis;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Punish;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PunishController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $userIds = Punish::where('type', 0)->pluck('user_id');

        $users = User::whereIn('id', $userIds)->get();

        $startDate = $request->startDate;

        $endDate = $request->endDate;

        $type = $request->type;

        $userId = $request->user_id;

        $filters = compact('startDate', 'endDate', 'type', 'userId');

        $punishes = Punish::filter($filters)->latest('created_at')->paginate(config('backend.page'));

        return view('backend.punish.index', compact('users', 'startDate', 'endDate', 'type', 'userId', 'punishes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::where('parent_id', 0)->get();

        $start = Carbon::now()->startOfDay()->toDateTimeString();

        $end = Carbon::now()->toDateTimeString();

        $orders = Order::whereBetween('created_at', [$start, $end])->pluck('no');

        return view('backend.punish.create', compact('users', 'orders'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $orderNo = static::createOrderId();

        $data = $request->all();

        $data['type'] = 0;

        $data['deadline'] = $request->deadline . ' 23:59:59';

        $data['order_no'] = $orderNo;

        $this->validate($request, Punish::rules(), Punish::messages());

        $res = Punish::create($data);
        
        if (! $res) {

            return back()->withInput()->with('createFail', '添加失败！');
        }
        return redirect(route('punishes.index'))->with('succ', '添加成功!');


    }

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
        $punish = Punish::find($id);

        return view('backend.punish.show', compact('punish'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $punish = Punish::find($id);

        return view('backend.punish.edit', compact('punish'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, Punish::rules($id), Punish::messages());

        $data = $request->all();

        $data['deadline'] = $request->deadline . ' 23:59:59';

        $punish = Punish::find($id);

        $int = $punish->update($data);

        if ($int > 0) {

            return redirect(route('punishes.index'))->with('succ', '更新成功!');
        }

        return back()->withInput()->with('updateFail', '更新失败!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $bool = Punish::find($id)->delete();

        if ($bool) {

            return response()->json(['code' => '1', 'message' => '删除成功!']);
        }
        return response()->json(['code' => '2', 'message' => '删除失败!']);
    }
}

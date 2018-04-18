<?php

namespace App\Console\Commands;

use DB;
use Redis;
use Asset;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderDetail;
use App\Services\Show91;
use App\Services\DailianMama;
use Illuminate\Console\Command;
use App\Extensions\Asset\Expend;
use App\Exceptions\DailianException;

class AutoMarkupOrderEveryHour extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'markup-order:one-hour';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '每小时自动加价一次';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            // 取redis的值
            $name = "order:automarkup-every-hour";
            $automarkupOrders = Redis::hGetAll($name);
            // 如果存在值
            if ($automarkupOrders) {
                foreach ($automarkupOrders  as $orderNo => $addAmountAndTime) {
                    // 获取订单数据 
                    $order = $this->getOrder($orderNo);

                    if (! $order) {
                        continue;
                    }
                    // 获取订单详情
                    $orderDetails = $this->getOrderDetails($orderNo);

                    if (! $orderDetails) {
                        continue;
                    }
                    // 解析redis值
                    $datas = $this->parse($addAmountAndTime, $orderNo);

                    if (! $datas) {
                        continue;
                    }
                    // 检查redis里面存的订单当前代练费是否等于数据库的代练费
                    $this->checkRedisAmountEqualOrderAmount($datas, $order);
                    // 检查是否到了加价时间
                    $isBegin = $this->checkTimeToAddPrice($datas, $order, $orderDetails);

                    if (! $isBegin) {
                        continue;
                    }
                    // 写我们的加价流水和日志
                    $bool = $this->addFlowsAndHistory($order, $orderDetails, $datas);

                    if (! $bool) {
                        continue;
                    }
                    // Redis的值增加
                    $this->increase($datas, $order, $orderDetails);
                    // 调外面加价接口
                    $res = $this->addPriceToThirdClient($datas, $order, $orderDetails);

                    if (! $res) {
                        continue;
                    }
                }
            }
        } catch (Exception $e) {
            mylog('order.automarkup.every.hour', ['订单号' => $orderNo, '结果' => '失败', '原因' => $e->getMessage()]);
        }
    }

    /**
     * 获取订单
     * @param  [type] $orderNo [description]
     * @return [type]          [description]
     */
    public function getOrder($orderNo)
    {
        $order = Order::where('no', $orderNo)->first();

        if (! $order) {
            $this->deleteRedisHashKey($orderNo);
            mylog('order.automarkup.every.hour', ['订单号' => $orderNo, '结果' => '失败', '原因' => '订单号不存在']);
            return false;
        }

        return $order;
    }

    /**
     * 获取订单详情
     * @param  [type] $orderNo [description]
     * @return [type]          [description]
     */
    public function getOrderDetails($orderNo)
    {
        $orderDetail = OrderDetail::where('order_no', $orderNo)->first();

        if (! $orderDetail) {
            $this->deleteRedisHashKey($orderNo);
            mylog('order.automarkup.every.hour', ['订单号' => $orderNo, '结果' => '失败', '原因' => '订单号不存在']);
            return false;
        }

        return OrderDetail::where('order_no', $orderNo)
                    ->pluck('field_value', 'field_name')
                    ->toArray();
    }

        /**
         * 解析redis值
         * @param  [type] $datas [description]
         * @return [type]        [description]
         */
    public function parse($datas, $orderNo)
    {
        if (! isset($datas) || ! $datas) {
            $this->deleteRedisHashKey($orderNo);
            mylog('order.automarkup.every.hour', ['订单号' => $orderNo, '结果' => '失败', '原因' => '无相关数据']);
            return false;
        }

        // 开始解析
        $datas = explode('@', $datas);

        if (! is_array($datas) || ! $datas) {
            $this->deleteRedisHashKey($orderNo);
            mylog('order.automarkup.every.hour', ['订单号' => $orderNo, '结果' => '失败', '原因' => '无相关数据']);
            return false;
        }

        $arr = [];
        $arr['add_number'] = $datas[0];
        $arr['add_amount'] = $datas[1];
        $arr['add_time'] = $datas[2];

        return $arr;
    }

    /**
     * 删除hash key
     * @param  [type] $name [description]
     * @param  [type] $key  [description]
     * @return [type]       [description]
     */
    public function deleteRedisHashKey($orderNo)
    {
        $key = $orderNo;
        $name = "order:automarkup-every-hour";
        Redis::hDel($name, $key);
    }

    /**
     * 检查redis的订单代练费是否等于数据库订单代练费，不等于的话改成等于
     * @param  [type] $datas [description]
     * @param  [type] $order [description]
     * @return [type]        [description]
     */
    public function checkRedisAmountEqualOrderAmount($datas, $order)
    {
        if ($datas['add_amount'] != $order->amount) {
            $key = $order->no;
            $name = "order:automarkup-every-hour";
            $value = $datas['add_number'].'@'.$order->amount."@".$datas['add_time'];
            Redis::hSet($name, $key, $value);

            $datas['add_amount'] = $order->amount;
        }
    }

    /**
     * 加价流水和加价订单日志
     * @param [type] $order        [description]
     * @param [type] $orderDetails [description]
     * @param [type] $datas        [description]
     */
    public function addFlowsAndHistory($order, $orderDetails, $datas)
    {
        DB::beginTransaction();
        try {
            // 加价后的订单金额
            $afterAddAmount = bcadd($datas['add_amount'], $orderDetails['markup_range'], 2);
            // 流水
            Asset::handle(new Expend($orderDetails['markup_range'], 7, $order->no, '代练改价支出', $order->creator_primary_user_id));

            $res = Order::where('no', $order->no)->update(['price' => $afterAddAmount, 'amount' => $afterAddAmount]);
            // 订单详情金额更新
            $res1 = OrderDetail::where('order_no', $order->no)
                ->where('field_name', 'game_leveling_amount')
                ->update(['field_value' => $afterAddAmount]);
            // 主账号对象
            $user = User::find($order->creator_primary_user_id);
            
            // 第几次加价
            $number = $datas['add_number']+1;
            // 写订单日志
            $data = [];
            $data['order_no'] = $order->no;
            $data['user_id'] = $order->creator_user_id;
            $data['admin_user_id'] = '';
            $data['type'] = '';
            $data['name'] = '加价';
            $data['description'] = '订单第'.$number.'次自动加价，加价金额为'.$orderDetails['markup_range'].'元，加价后订单金额为'.$afterAddAmount.'元';
            $data['before'] = '';
            $data['after'] = '';
            $data['created_at'] = Carbon::now()->toDateTimeString();
            $data['creator_primary_user_id'] = $order->creator_primary_user_id;

            $res2 = OrderHistory::create($data);
        } catch (Exception $e) {
            DB::rollback();
            mylog('order.automarkup.every.hour', ['订单号' => $order->no, '结果' => '失败', '原因' => $e->getMessage()]);
            return false;
        }
        DB::commit();
        return true;
    }

    public function addPriceToThirdClient($datas, $order, $orderDetails)
    {
        if ($orderDetails['show91_order_no']) {
            try {
                $name = 'addPrice';
                $order->addAmount = $orderDetails['markup_range'];
                call_user_func_array([Show91::class, $name], [$order, false]);
            } catch (DailianException $e) {
                // 91下架接口
                Show91::grounding(['oid' => $orderDetails['show91_order_no']]);
                mylog('order.automarkup.every.hour', ['订单号' => $order->no, '结果' => '失败', '原因' => $e->getMessage()]);
            }
        }

        if ($orderDetails['dailianmama_order_no']) {
            try {
                $name = 'releaseOrder';
                $order->amount = bcadd($datas['add_amount'], $orderDetails['markup_range'], 2);
                call_user_func_array([DailianMama::class, $name], [$order, true]);
            } catch (DailianException $e) {
                // 代练妈妈下架接口
                DailianMama::closeOrder($order);
                myLog('order.automarkup.every.hour', ['订单号' => $order->no, '原因' => $e->getMessage(), '结果' => '自动加价失败!']);
            }
        }

        try {
            // 加价 其他平台通用
            if (config('leveling.third_orders')) {
               // 遍历代练平台
                foreach (config('leveling.third_orders') as $third => $thirdOrderNoName) {
                    // 如果订单详情里面存在某个代练平台的订单号，撤单此平台订单
                    if (isset($orderDetails[$thirdOrderNoName]) && ! empty($orderDetails[$thirdOrderNoName])) {
                        // 控制器-》方法-》参数
                        call_user_func_array([config('leveling.controller')[$third], config('leveling.action')['addMoney']], [$orderDetails]);
                    }
                }
            }
        } catch (Exception $e) {
           if (config('leveling.third_orders')) {
                // 遍历代练平台
                foreach (config('leveling.third_orders') as $third => $thirdOrderNoName) {
                    // 如果订单详情里面存在某个代练平台的订单号，撤单此平台订单
                    if (isset($orderDetails[$thirdOrderNoName]) && ! empty($orderDetails[$thirdOrderNoName])) {
                        // 控制器-》方法-》参数
                        call_user_func_array([config('leveling.controller')[$third], config('leveling.action')['delete']], [$orderDetails]);
                    }
                }
            }
            myLog('order.automarkup.every.hour', ['订单号' => $order->no, '原因' => $e->getMessage(), '结果' => '自动加价失败!']);    
        }
    }

    /**
     * redis 值增加
     * @param  [type] $datas        [description]
     * @param  [type] $orderDetails [description]
     * @return [type]               [description]
     */
    public function increase($datas, $order, $orderDetails)
    {
        $number = $datas['add_number'] + 1;
        $amount = bcadd($datas['add_amount'], $orderDetails['markup_range'], 2);
        $time = Carbon::parse($datas['add_time'])->addMinutes(1)->toDateTimeString();

        $key = $order->no;
        $name = "order:automarkup-every-hour";
        $value = $number.'@'.$amount."@".$time;
        Redis::hSet($name, $key, $value);
    }

    /**
     * 检查是否到了加价时间
     * @param  [type] $datas [description]
     * @return [type]        [description]
     */
    public function checkTimeToAddPrice($datas, $order, $orderDetails)
    {
        // 时间是否到了加价的点
        $now = Carbon::now();
        $addTime = Carbon::parse($datas['add_time'])->addMinutes(1);

        // 加价金额是否到了上限
        $isOverAmount = bcsub($datas['add_amount'], $orderDetails['markup_top_limit']) < 0 ? true : false ;

        if (! $isOverAmount) {
            $this->deleteRedisHashKey($order->no);
            return false;
        }
        return $now->diffInMinutes($addTime, false) < 0 ? true : false;
    }
}

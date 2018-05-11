<?php

namespace App\Console\Commands;

use DB;
use Redis;
use Asset;
use Exception;
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
use App\Exceptions\AssetException;

class NewAutoMarkupOrderEveryHour extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'new-markup-order:one-hour';

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
        // 取redis的值
        $name = "order:automarkup-every-hour";
        $automarkupOrders = Redis::hGetAll($name);
        // 如果存在值
        if ($automarkupOrders) {
            foreach ($automarkupOrders as $orderNo => $addAmountAndTime) {
                try {
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

                    // 掉外面的订单同步价格接口
                    $this->thirdAddPrice($datas, $order);
                    // Redis的值增加
                    $this->increase($datas, $order, $orderDetails);
                } catch (Exception $e) {
                    myLog('order.automarkup.every.hour', ['订单号' => $orderNo, '结果' => '失败', '原因' => $e->getMessage()]);
                }
            }
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
            myLog('order.automarkup.every.hour', ['订单号' => $orderNo, '结果' => '失败', '原因' => '订单号不存在']);
            return false;
        }


        // 如果是已下架，跳出循环
        if (22 == $order->status) {
            return false;
        }

        // 如果此订单不在 未接单并且不是已下架  状态,删除redis
        if ($order->status != 1 && $order->status != 22) {
            $this->deleteRedisHashKey($orderNo);
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
            myLog('order.automarkup.every.hour', ['订单号' => $orderNo, '结果' => '失败', '原因' => '订单号不存在']);
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
            myLog('order.automarkup.every.hour', ['订单号' => $orderNo, '结果' => '失败', '原因' => '无相关数据']);
            return false;
        }

        // 开始解析
        $datas = explode('@', $datas);

        if (! is_array($datas) || ! $datas) {
            $this->deleteRedisHashKey($orderNo);
            myLog('order.automarkup.every.hour', ['订单号' => $orderNo, '结果' => '失败', '原因' => '无相关数据']);
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
    public static  function deleteRedisHashKey($orderNo)
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
        // 始终同步redis 的加价为订单金额
        $key = $order->no;
        $name = "order:automarkup-every-hour";
        $value = $datas['add_number'].'@'.$order->amount."@".$datas['add_time'];
        Redis::hSet($name, $key, $value);
    }

    public function getOrderDatas($order) 
    {
        $orderArr = $order->toArray();

        $orderDetails = OrderDetail::where('order_no', $order->no)->pluck('field_value', 'field_name')->toArray();

        return array_merge($orderDetails, $orderArr);
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
            //如果上限 - 代练金额  小于  加价幅度 但是又大于0
            if (bcsub($orderDetails['markup_top_limit'], $datas['add_amount']) < $orderDetails['markup_range']) {
                // 加价金额
                $rangeMoney = bcsub($orderDetails['markup_top_limit'], $datas['add_amount']);
                // 加价后的订单金额
                $afterAddAmount = $orderDetails['markup_top_limit'];
                // 流水
                Asset::handle(new Expend($rangeMoney, 7, $order->no, '代练改价支出', $order->creator_primary_user_id));
            } else {
                // 加价金额
                $rangeMoney = $orderDetails['markup_range'];
                // 加价后的订单金额
                $afterAddAmount = bcadd($datas['add_amount'], $orderDetails['markup_range'], 2);
                // 流水
                Asset::handle(new Expend($orderDetails['markup_range'], 7, $order->no, '代练改价支出', $order->creator_primary_user_id));
            }

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
            $data['description'] = '订单第'.$number.'次自动加价，加价金额为'.$rangeMoney.'元，加价后订单金额为'.$afterAddAmount.'元';
            $data['before'] = '';
            $data['after'] = '';
            $data['created_at'] = Carbon::now()->toDateTimeString();
            $data['creator_primary_user_id'] = $order->creator_primary_user_id;

            OrderHistory::create($data);
        } catch (DailianException $e) {
            DB::rollback();
            myLog('markup-hour-dailian-errors', ['订单号' => isset($order) ? $order->no : '', '结果' => '失败', '原因' => $e->getMessage()]);
            return false;
        } catch (AssetException $e) {
            DB::rollback();
            myLog('markup-hour-aesset-error', ['订单号' => isset($order) ? $order->no : '', '结果' => '失败', '原因' => $e->getMessage()]);
            return false;
        } catch (Exception $e) {
            DB::rollback();
            myLog('markup-hour-local-error', ['订单号' => isset($order) ? $order->no : '', '结果' => '失败', '原因' => $e->getMessage()]);
            return false;
        }
        DB::commit();
        return true;
    }

    public function addPriceToThirdClient($datas, $order, $orderDetails)
    {     
        // 加价 其他平台通用
        if (config('leveling.third_orders')) {
           // 遍历代练平台
            foreach (config('leveling.third_orders') as $third => $thirdOrderNoName) {
                // 如果订单详情里面存在某个代练平台的订单号，撤单此平台订单
                if (isset($orderDetails[$thirdOrderNoName]) && ! empty($orderDetails[$thirdOrderNoName])) {
                    try {
                        // 控制器-》方法-》参数
                        call_user_func_array([config('leveling.controller')[$third], config('leveling.action')['updateOrder']], [$orderDetails]);
                        myLog('markup-hour-success', ['订单号' => isset($order) ? $order->no : '', '结果' => '平台'.$third.'加价成功', '加价后金额' => isset($order) ? $order->amount : '']);
                    } catch (DailianException $e) {
                        // 控制器-》方法-》参数
                        call_user_func_array([config('leveling.controller')[$third], config('leveling.action')['delete']], [$orderDetails]);
                        myLog('markup-hour-dailian-error', ['订单号' => isset($order) ? $order->no : '', '结果' => '失败,已调用'.$third.'撤单，已删除本地订单', '原因' => $e->getMessage()]);    
                    } catch (Exception $e) {
                        call_user_func_array([config('leveling.controller')[$third], config('leveling.action')['delete']], [$orderDetails]);
                        myLog('markup-hour-dailian-error', ['订单号' => isset($order) ? $order->no : '', '结果' => '失败,已调用'.$third.'撤单，已删除本地订单', '原因' => $e->getMessage()]);     
                    }
                }
            }
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
        $order = Order::where('no', $order->no)->first();
        //如果上限 - 代练金额  小于  加价幅度 但是又大于0
        if (bcsub($orderDetails['markup_top_limit'], $datas['add_amount']) < $orderDetails['markup_range']) {
            // 加价金额
            $rangeMoney = bcsub($orderDetails['markup_top_limit'], $datas['add_amount']);
            // 加价后的订单金额
            $afterAddAmount = $orderDetails['markup_top_limit'];
        } else {
            // 加价金额
            $rangeMoney = $orderDetails['markup_range'];
            // 加价后的订单金额
            $afterAddAmount = bcadd($datas['add_amount'], $orderDetails['markup_range'], 2);
        }

        $number = $datas['add_number'] + 1;
        $amount = bcadd($datas['add_amount'], $rangeMoney, 2);
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
        $isOverAmount = bcsub($datas['add_amount'], $orderDetails['markup_top_limit']) < 0 ? true : false;

        if (! $isOverAmount) {
            $this->deleteRedisHashKey($order->no);
            return false;
        }

        // 如果超过了加价值 
        if (bcsub($orderDetails['markup_top_limit'], $datas['add_amount']) <= 0) {
            return false;
        }
        return $now->diffInMinutes($addTime, false) < 0 ? true : false;
    }

    public function thirdAddPrice($datas, $order)
    {
        // 调外面加价接口
        // 平台加价后的金额
        $order = Order::where('no', $order->no)->first();

        $orderDetails = $this->getOrderDatas($order);

        $this->addPriceToThirdClient($datas, $order, $orderDetails);
    }
}

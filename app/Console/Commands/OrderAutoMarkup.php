<?php

namespace App\Console\Commands;

use DB;
use Redis;
use Asset;
use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Services\Show91;
use App\Models\OrderDetail;
use App\Models\OrderHistory;
use App\Services\DailianMama;
use Illuminate\Console\Command;
use App\Extensions\Asset\Expend;
use App\Exceptions\DailianException;
use App\Models\OrderAutoMarkup as OrderAutoMarkupModel;

class OrderAutoMarkup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:markup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '未接单订单自动加价';

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
        // 获取当前时间
        $now = Carbon::now();
        // 获取订单和发单主账号
        $redisDatas = Redis::hGetAll("order:autoMarkups");
        // 遍历数组
        foreach ($redisDatas as $orderNo => $numberAndTime) {
            // 获取加价次数
            $number = explode('@', $numberAndTime) ? explode('@', $numberAndTime)[0] : null;
            // 订单第一次的下单金额
            $firstAmount = explode('@', $numberAndTime) ? explode('@', $numberAndTime)[1] : null;
            // 订单下单时间
            $addTime = explode('@', $numberAndTime) ? explode('@', $numberAndTime)[2] : null;

            // 如果此次数和时间不存在则换下一条
            if (! isset($number) || ! isset($addTime)) {
                Redis::hDel('order:autoMarkups', $order->no);
                continue;
            }

            // 获取订单对象
            $order = Order::where('no', $orderNo)->first();

            // 获取订单详情
            $orderDetails = OrderDetail::where('order_no', $order->no)
                ->pluck('field_value', 'field_name')
                ->toArray();

            // 如果此订单不在 未接单  状态,删除redis
            if ($order->status != 1) {
                Redis::hDel('order:autoMarkups', $order->no);
                continue;
            }

            // 查找主账号下面设置爱的自动加价模板
            $orderAutoMarkup = OrderAutoMarkupModel::where('user_id', $order->creator_primary_user_id)
                ->where('markup_amount', '>=', $firstAmount)
                ->oldest('markup_amount')
                ->first();


            // 不存在则跳出循环,删除redis
            if (! $orderAutoMarkup) {
                Redis::hDel('order:autoMarkups', $order->no);
                continue;
            }

            // 如果加价到了最大次数，删掉Redis
            if ($orderAutoMarkup->markup_number == $number || $orderAutoMarkup->markup_money == 0) {
                Redis::hDel('order:autoMarkups', $order->no);
                continue;
            }

            // 查看此条自动加价里面加价类型获取加价金额,0绝对自，1是百分比
            $markupType = $orderAutoMarkup->markup_type;

            // 根据此类型算加价金额
            $markupMoney = $markupType == 1 ? bcmul($orderAutoMarkup->markup_money*0.01, $firstAmount, 2) : $orderAutoMarkup->markup_money;

            // 自动加价记录里面最早开始加价时间小于当前时间
            $orderAutoMarkupStartTime = Carbon::parse($order->created_at)->addMinutes($orderAutoMarkup->markup_time);

            // 当前时间大于加价开始时间，开始加价
            $isReady = $now->diffInMinutes($orderAutoMarkupStartTime, false) < 0 ? true : false;

            // 首次加价，redis 存的时间和订单取得下单时间一致，说明是第一次
            if ($isReady && $number == 0) {
                // 加价
                $resShow91 = $this->addShow91Price($order, $markupMoney, $number, $orderDetails);
                $resDailianMama = $this->addDailianMamaPrice($order, $markupMoney, $number, $firstAmount, $orderDetails);

                if (! $resShow91 && ! $resDailianMama) {
                    continue;
                }
                // 支出流水和订单日志
                $result = $this->writeLogAndExpendFlows($order, $markupMoney, $number, $firstAmount, $orderDetails);

                if (! $result) {
                    continue;
                }

                // 加价之后，redis次数+1, 时间换到最新加价的时间
                Redis::hSet('order:autoMarkups', $order->no, bcadd($number, 1, 0).'@'.$firstAmount.'@'.$orderAutoMarkupStartTime->toDateTimeString());
                // 写下日志
                myLog('order.automarkup', [
                    '订单号' => $order->no,
                    '加价金额' => $markupMoney,
                    '时间' => $now->toDateTimeString(),
                    '结果' => '成功!']);
            }

            // 如果加价次数在加价次数之类，并且到了下一次的加价时间，则继续加价
            $nextAddTime = Carbon::parse($addTime)->addMinutes($orderAutoMarkup->markup_frequency);
            // 下一次加价时间
            $nextIsReady = $now->diffInMinutes($nextAddTime, false) < 0 ? true : false;

            if (($number < $orderAutoMarkup->markup_number || $orderAutoMarkup->markup_number == 0) && $nextIsReady && $number > 0) {
                // 加价
                $resShow91 = $this->addShow91Price($order, $markupMoney, $number, $orderDetails);
                $resDailianMama = $this->addDailianMamaPrice($order, $markupMoney, $number, $firstAmount, $orderDetails);

                if (! $resShow91 && ! $resDailianMama) {
                    continue;
                }
                // 支出流水和订单日志
                $result = $this->writeLogAndExpendFlows($order, $markupMoney, $number, $firstAmount, $orderDetails);

                if (! $result) {
                    if ($orderDetails['show91_order_no']) {
                        // 91下架接口
                        Show91::grounding(['oid' => $orderDetails['show91_order_no']]);
                        myLog('order.automarkup', ['订单号' => $order->no, '原因' => $e->getMessage(), '结果' => '自动加价失败!']);
                    }

                    if ($orderDetails['dailianmama_order_no']) {
                         // 代练妈妈下架接口
                        DailianMama::closeOrder($order);
                        myLog('order.automarkup', ['订单号' => $order->no, '原因' => $e->getMessage(), '结果' => '自动加价失败!']);
                    }
                    continue;
                }

                // 加价之后，redis次数+1, 时间换到最新加价的时间
                Redis::hSet('order:autoMarkups', $order->no, bcadd($number, 1, 0).'@'.$firstAmount.'@'.$nextAddTime->toDateTimeString());
                // 写下日志
                myLog('order.automarkup', [
                    '订单号' => $order->no,
                    '加价金额' => $markupMoney,
                    '时间' => $now->toDateTimeString(),
                    '结果' => '成功!']);
            }
        }
    }

    /**
     * 向 91 和 代练妈妈加价
     * 订单， 加价金额
     */
    public function addShow91Price($order, $markupMoney, $number, $orderDetails)
    {
        // 获取订单详情
        // $orderDetails = OrderDetail::where('order_no', $order->no)
        //     ->pluck('field_value', 'field_name')
        //     ->toArray();

         // 如果91下单成功，则91加价
        if ($orderDetails['show91_order_no']) {
            try {
                $name = 'addPrice';
                $order->addAmount = $markupMoney;
                call_user_func_array([Show91::class, $name], [$order, false]);
                return true;
            } catch (DailianException $e) {
                // 91下架接口
                Show91::grounding(['oid' => $orderDetails['show91_order_no']]);
                myLog('order.automarkup', ['订单号' => $order->no, '原因' => $e->getMessage(), '结果' => '自动加价失败!']);
                return false;
            }
        }
        return false;
    }

    /**
     * 代练妈妈加款
     * @param [type] $order       [description]
     * @param [type] $markupMoney [description]
     * @param [type] $number      [description]
     */
    public function addDailianMamaPrice($order, $markupMoney, $number, $firstAmount, $orderDetails)
    {
        // 获取订单详情
        // $orderDetails = OrderDetail::where('order_no', $order->no)
        //     ->pluck('field_value', 'field_name')
        //     ->toArray();

        // 如果代练妈妈下单成功，则代练妈妈加价
        if ($orderDetails['dailianmama_order_no']) {
            try {
                $name = 'releaseOrder';
                $order->amount = bcadd(bcmul($markupMoney, bcadd($number, 1)), $firstAmount);
                call_user_func_array([DailianMama::class, $name], [$order, true]);
                return true;
            } catch (DailianException $e) {
                // 代练妈妈下架接口
                DailianMama::closeOrder($order);
                myLog('order.automarkup', ['订单号' => $order->no, '原因' => $e->getMessage(), '结果' => '自动加价失败!']);
                return false;
            }
        }
        return false;
    }

    /**
     * 支出流水和订单记录
     * @param  [type] $order       [description]
     * @param  [type] $markupMoney [description]
     * @param  [type] $number      [description]
     * @return [type]              [description]
     */
    public function writeLogAndExpendFlows($order, $markupMoney, $number, $firstAmount, $orderDetails)
    {
        DB::beginTransaction();
        try {
            // 获取订单详情
            // $orderDetails = OrderDetail::where('order_no', $order->no)
            //     ->pluck('field_value', 'field_name')
            //     ->toArray();
            // 增加后的金额
            $afterAddAmount = bcadd(bcmul($markupMoney, bcadd($number, 1)), $firstAmount, 2);
            // 流水
            Asset::handle(new Expend($markupMoney, 7, $order->no, '代练改价支出', $order->creator_primary_user_id));
            // 订单金额更新
            // $order->price = $afterAddAmount;
            // $order->amount = $afterAddAmount;
            Order::where('no', $order->no)->update(['price' => $afterAddAmount, 'amount' => $afterAddAmount]);
            // 订单详情金额更新
            OrderDetail::where('order_no', $order->no)
                ->where('field_name', 'game_leveling_amount')
                ->update(['field_value' => $afterAddAmount]);

            // 主账号对象
            $user = User::find($order->creator_primary_user_id);
            
            // 第几次加价
            $number = $number+1;
            // 写订单日志
            $data = [];
            $data['order_no'] = $order->no;
            $data['user_id'] = $order->creator_user_id;
            $data['admin_user_id'] = '';
            $data['type'] = '';
            $data['name'] = '加价';
            $data['description'] = '订单第'.$number.'次自动加价，加价金额为'.$markupMoney.'元，加价后订单金额为'.$afterAddAmount.'元';
            $data['before'] = '';
            $data['after'] = '';
            $data['created_at'] = Carbon::now()->toDateTimeString();
            $data['creator_primary_user_id'] = $order->creator_primary_user_id;

            OrderHistory::create($data);
        } catch (Exception $e) {
            DB::rollback();
            return false;
        }
        DB::commit();
        return true;
    }
}

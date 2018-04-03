<?php

namespace App\Console\Commands;

use Redis;
use Carbon\Carbon;
use App\Models\Order;
use App\Services\Show91;
use App\Models\OrderDetail;
use App\Services\DailianMama;
use Illuminate\Console\Command;
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
            // 订单下单时间
            $addTime = explode('@', $numberAndTime) ? explode('@', $numberAndTime)[1] : null;

            // 如果此次数和时间不存在则换下一条
            if (! isset($number) || ! isset($addTime)) {
                Redis::hDel('order:autoMarkups', $order->no);
                continue;
            }

            // 获取订单对象
            $order = Order::where('no', $orderNo)->first();

            // 如果此订单不在 未接单  状态,删除redis
            if ($order->status != 1) {
                Redis::hDel('order:autoMarkups', $order->no);
                continue;
            }

            // 查找主账号下面设置爱的自动加价模板
            $orderAutoMarkup = OrderAutoMarkupModel::where('user_id', $order->creator_primary_user_id)
                ->where('markup_amount', '>=', $order->amount)
                ->oldest('markup_amount')
                ->first();

            if (! $orderAutoMarkup) {
                Redis::hDel('order:autoMarkups', $order->no);
                continue;
            }

            // 如果次数到了自动加价的最大次数
            if ($orderAutoMarkup->markup_number == $number) {
                Redis::hDel('order:autoMarkups', $order->no);
                continue;
            }

            // 查看此条自动加价里面加价类型获取加价金额,0绝对自，1是百分比
            $markupType = $orderAutoMarkup->markup_type;

            // 根据此类型算加价金额
            $markupMoney = $markupType == 1 ? bcmul($orderAutoMarkup->markup_money*0.01, $order->amount) : $orderAutoMarkup->markup_money;

            // 自动加价记录里面最早开始加价时间小于当前时间
            $orderAutoMarkupStartTime = Carbon::parse($order->created_at)->addMinutes($orderAutoMarkup->markup_time);

            // 当前时间大于加价开始时间，开始加价
            $isReady = $now->diffInMinutes($orderAutoMarkupStartTime, false) < 0 ? true : false;

            // 首次加价，redis 存的时间和订单取得下单时间一致，说明是第一次
            if ($isReady && $number == 0) {
                // 加价
                $this->addPrice($order, $markupMoney, $number);
                // 加价之后，redis次数+1, 时间换到最新加价的时间
                Redis::hSet('order:autoMarkups', $order->no, bcadd($number, 1, 0).'@'.$orderAutoMarkupStartTime->toDateTimeString());
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

            if (($number < $orderAutoMarkup->markup_number || $orderAutoMarkup->markup_number == 0) && $nextIsReady) {
                // 加价
                $this->addPrice($order, $markupMoney, $number);
                // 加价之后，redis次数+1, 时间换到最新加价的时间
                Redis::hSet('order:autoMarkups', $order->no, bcadd($number, 1, 0).'@'.$nextAddTime->toDateTimeString());
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
    public function addPrice($order, $markupMoney, $number)
    {
        try {
            // 获取订单详情
            $orderDetails = OrderDetail::where('order_no', $order->no)
                ->pluck('field_value', 'field_name')
                ->toArray();

             // 如果91下单成功，则91加价
            if ($orderDetails['show91_order_no']) {
                $name = 'addPrice';
                $order->addAmount = $markupMoney;
                call_user_func_array([Show91::class, $name], [$order, false]);
            }

            // 如果代练妈妈下单成功，则代练妈妈加价
            if ($orderDetails['dailianmama_order_no']) {
                $name = 'releaseOrder';
                $order->amount = bcadd($markupMoney*($number+1), $order->amount);
                call_user_func_array([DailianMama::class, $name], [$order, true]);
            }
        } catch (DailianException $e) {
            myLog('order.automarkup', ['订单号' => $order->no, '原因' => $e->getMessage(), '结果' => '自动加价失败!']);
        }
    }
}

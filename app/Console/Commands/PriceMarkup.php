<?php

namespace App\Console\Commands;

use App\Exceptions\GameLevelingOrderOperateException;
use App\Models\GameLevelingOrderLog;
use App\Models\GameLevelingPlatform;
use App\Models\User;
use DB;
use RedisFacade;
use Asset;
use Exception;
use Carbon\Carbon;
use App\Extensions\Asset\Expend;
use App\Exceptions\DailianException;
use App\Exceptions\AssetException;
use App\Models\GameLevelingOrder;
use Illuminate\Console\Command;

class PriceMarkup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'price:markup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '新的订单每小时自动加价';

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
        $priceMarkups = RedisFacade::hGetAll("order:price-markup");

        if ($priceMarkups) {
            foreach ($priceMarkups as $tradeNo => $amountTime) {
                try {
                    $order = GameLevelingOrder::where('trade_no', $tradeNo)->first();

                    // 订单号异常
                    if (! $order) {
                        $this->deleteRedisHashKey($tradeNo);
                        myLog('price-markup-error', ['订单号' => $tradeNo, '原因' => '订单号不存在']);
                        continue;
                    } elseif (22 == $order->status) {
                        continue;
                    } elseif ($order->status != 1 && $order->status != 22) {
                        $this->deleteRedisHashKey($tradeNo); // 如果此订单不在 未接单并且不是已下架  状态,删除redis
                        continue;
                    }

                    // 解析redis值
                    if (! isset($amountTime) || ! $amountTime) {
                        $this->deleteRedisHashKey($tradeNo);
                        myLog('price-markup-error', ['订单号' => $tradeNo,  '原因' => 'redis的值不存在']);
                        continue;
                    }

                    // 开始解析
                    $data = explode('@', $amountTime);

                    if (! is_array($data) || ! $data) {
                        $this->deleteRedisHashKey($tradeNo);
                        myLog('price-markup-error', ['订单号' => $tradeNo,  '原因' => 'redis的值不存在或存储格式异常']);
                        continue;
                    }

                    $info = [];
                    $info['add_number'] = $data[0];
                    $info['add_amount'] = $data[1];
                    $info['add_time'] = $data[2];

                    // 同步redis的value里面amount为订单当前amount
                    $this->formatRedisAmount($info, $order);

                    // 检查是否到了加价时间
                    $isBegin = $this->checkTimeToAddPrice($info, $order);

                    if (! $isBegin) {
                        continue;
                    }

                    // 加价流水和日志
                    $bool = $this->addFlowsAndHistory($order, $info);

                    if (! $bool) {
                        continue;
                    }

                    $this->thirdAddPrice($info, $order); // 开始加价
                    $this->increase($info, $order); // 加价成功，Redis的值相应改变
                } catch (Exception $e) {
                    myLog('price-markup-error', ['订单号' => $tradeNo, '原因' => $e->getMessage()]);
                }
            }
        }
    }

    /**
     * 删除redis
     * @param $tradeNo
     */
    public static  function deleteRedisHashKey($tradeNo)
    {
        RedisFacade::hDel("order:price-markup", $tradeNo);
    }

    /**
     * 同步redis的value里面amount为订单当前amount
     * @param $info
     * @param $order
     */
    public function formatRedisAmount($info, $order)
    {
        RedisFacade::hSet("order:price-markup", $order->trade_no, $info['add_number'].'@'.$order->amount."@".$info['add_time']);
    }

    /**
     * 添加流水和订单日志
     * @param $order
     * @param $info
     * @return bool
     */
    public function addFlowsAndHistory($order, $info)
    {
        DB::beginTransaction();
        try {
            //如果上限 - 代练金额  小于  加价幅度 但是又大于0
            if (bcsub($order->price_ceiling, $info['add_amount']) < $order->price_increase_step) {
                $rangeMoney = bcsub($order->price_ceiling, $info['add_amount']);  // 加价金额
                $afterAddAmount = $order->price_ceiling; // 加价后的订单金额
                // 流水
                if(checkPayment($order->trade_no)) {
                    Asset::handle(new Expend($rangeMoney, 7, $order->trade_no, '代练改价支出', $order->parent_user_id));
                }
            } else {
                $rangeMoney = $order->price_increase_step; // 加价金额
                $afterAddAmount = bcadd($info['add_amount'], $order->price_increase_step, 2); // 加价后的订单金额
                // 流水
                if(checkPayment($order->trade_no)) {
                    Asset::handle(new Expend($order->price_increase_step, 7, $order->trade_no, '代练改价支出', $order->parent_user_id));
                }
            }

            GameLevelingOrder::where('trade_no', $order->trade_no)->update(['amount' => $afterAddAmount]);
            // 第几次加价
            $number = $info['add_number']+1;
            // 写订单日志
            $user = User::find($order->user_id);
            $description = '订单第'.$number.'次自动加价，加价金额为'.$rangeMoney.'元，加价后订单金额为'.$afterAddAmount.'元';
            GameLevelingOrderLog::createOrderHistory($order, $user, 34, $description);
        } catch (DailianException $e) {
            DB::rollback();
            myLog('price-markup-error', ['订单号' => isset($order) ? $order->trade_no : '', '原因' => $e->getMessage()]);
            return false;
        } catch (AssetException $e) {
            DB::rollback();
            myLog('price-markup-error', ['订单号' => isset($order) ? $order->trade_no : '', '原因' => $e->getMessage()]);
            return false;
        } catch (Exception $e) {
            DB::rollback();
            myLog('price-markup-error', ['订单号' => isset($order) ? $order->trade_no : '', '原因' => $e->getMessage()]);
            return false;
        }
        DB::commit();
        return true;
    }

    /**
     * redis 加价次数加1
     * @param $info
     * @param $order
     */
    public function increase($info, $order)
    {
        $order = GameLevelingOrder::where('trade_no', $order->trade_no)->first();
        //如果上限 - 代练金额  小于  加价幅度 但是又大于0
        if (bcsub($order->price_ceiling, $info['add_amount']) < $order->price_increase_step) {
            $rangeMoney = bcsub($order->price_ceiling, $info['add_amount']); // 加价金额
        } else {
            $rangeMoney = $order->price_increase_step; // 加价金额
        }

        $number = $info['add_number'] + 1;
        $amount = bcadd($info['add_amount'], $rangeMoney, 2);
        $time = Carbon::parse($info['add_time'])->addHours(1)->toDateTimeString();

        $key = $order->trade_no;
        $name = "order:price-markup";
        $value = $number.'@'.$amount."@".$time;
        RedisFacade::hSet($name, $key, $value);
    }

    /**
     * 是否到了加价时间
     * @param $info
     * @param $order
     * @return bool
     */
    public function checkTimeToAddPrice($info, $order)
    {
        $addTime = Carbon::parse($info['add_time'])->addHours(1); // 时间是否到了加价的点
        $isOverAmount = bcsub($info['add_amount'], $order->price_ceiling) < 0 ? true : false; // 加价金额是否到了上限

        if (! $isOverAmount) {
            $this->deleteRedisHashKey($order->trade_no);
            return false;
        } elseif (bcsub($order->price_ceiling, $info['add_amount']) <= 0) {  // 如果超过了加价值
            return false;
        }
        return Carbon::now()->diffInMinutes($addTime, false) < 0 ? true : false;
    }

    /**
     * @param $info
     * @param $order
     */
    public function thirdAddPrice($info, $order)
    {
        $order = GameLevelingOrder::where('trade_no', $order->trade_no)->first();

        // 该订单下单成功的接单平台
        $gameLevelingPlatforms = GameLevelingPlatform::where('game_leveling_order_trade_no', $order->trade_no)
            ->get();

        if ($gameLevelingPlatforms->count() > 0) {
            // 删除下单成功的
            foreach ($gameLevelingPlatforms as $gameLevelingPlatform) {
                try {
                    call_user_func_array([config('gameleveling.controller')[$gameLevelingPlatform->platform_id], config('gameleveling.action')['modifyOrder']], [$order]);
                    myLog('price-markup-success', ['订单号' => isset($order) ? $order->trade_no : '', '结果' => '平台'.$gameLevelingPlatform->platform_id.'加价成功', '加价后金额' => isset($order) ? $order->amount : '']);
                } catch (GameLevelingOrderOperateException $e) {
                    call_user_func_array([config('gameleveling.controller')[$gameLevelingPlatform->platform_id], config('gameleveling.action')['delete']], [$order]);
                    myLog('price-markup-error', ['订单号' => isset($order) ? $order->trade_no : '', '结果' => '失败,已调用'.$gameLevelingPlatform->platform_id.'撤单，已删除本地订单', '原因' => $e->getMessage()]);
                } catch (Exception $e) {
                    call_user_func_array([config('gameleveling.controller')[$gameLevelingPlatform->platform_id], config('gameleveling.action')['delete']], [$order]);
                    myLog('price-markup-error', ['订单号' => isset($order) ? $order->trade_no : '', '结果' => '失败,已调用'.$gameLevelingPlatform->platform_id.'撤单，已删除本地订单', '原因' => $e->getMessage()]);
                }
            }
        }
    }
}

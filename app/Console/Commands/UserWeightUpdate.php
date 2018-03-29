<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Extensions\Weight\Algorithm\OrderSix;
use App\Extensions\Weight\Algorithm\OrderSuccess;
use App\Extensions\Weight\Algorithm\OrderUseTime;
use App\Models\User;
use App\Models\UserWeight;
use App\Models\PunishOrReward;
use Illuminate\Console\Command;
use Log, Config, Weight, Order;

/**
 * Class UserWeightCompute
 * @package App\Console\Commands
 */
class UserWeightUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'UserWeightUpdate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更新所有用户权限';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = User::pluck('id');

        $now = Carbon::now()->startOfDay()->toDateTimeString();

        // 初始化用户的权重值
        foreach ($users as $user) {
            $weight = [];
            // 获取小于6元订单，大于50单
            $orderSix = OrderSix::compute([$user]);
            $weight['less_than_six_percent'] = $orderSix[$user] ?? 0;

            // 获成功订单大于平台平均值
            $orderSuccess = OrderSuccess::compute([$user]);
            $weight['success_percent'] = $orderSuccess[$user] ?? 0;

            // 获成功订单用时小于平均值
            $orderUseTime = OrderUseTime::compute([$user]);
            $weight['use_time_percent'] = $orderUseTime[$user] ?? 0;

            // 总体的权重奖惩率
            $weight['ratio'] = 0;
            // 更新用户权重值
            UserWeight::where('user_id', $user)->update($weight);

            // 禁止接单一天惩罚，过期了，则状态改为12,当天的最后时间
            $endDayPunishes = PunishOrReward::where('user_id', $user)
                ->where('type', 5)
                ->where('status', 0)
                ->orderBy('deadline')
                ->get();


            // 禁止接单-》把过期的惩罚状态改为12
            if ($endDayPunishes) {

                foreach ($endDayPunishes as $endDayPunish) {

                    $time = Carbon::parse($endDayPunish->deadline);
                    $int = (new Carbon)->diffInSeconds($time, false);

                    // 禁止接单期限为1天，超过这一天则表示已经处罚了，状态为1
                    if ($int <= 0) {
                        $endDayPunish->status = 12;
                        $endDayPunish->save();
                    }
                }
            }
        }

        // 取出所有减权重的过去时间为现在的
        $sunWeights = PunishOrReward::whereIn('type', ['4'])
                ->whereIn('status', ['1', '9'])
                ->where('deadline', $now)
                ->get();

        // 减权重到点自动确认
        foreach($sunWeights as $sunWeight) {
            $sunWeight->confirm = 1;
            $sunWeight->save();
        }

        // 奖惩表里面当天在加减权重生效和结束时间的所有记录
        $sendRatios = PunishOrReward::whereIn('type', [3, 4])
                ->where('start_time', '<=', $now)
                ->where('end_time', '>=', $now)
                ->where('confirm', 1)
                ->get()
                ->unique();

        // 奖惩表里面当天在加减权重生效和结束时间内的所有用户的 ratio 变更到 user_weight 里面的 ratio
        if ($sendRatios) {

            foreach($sendRatios as $sendRatio) {

                $ratio = $sendRatio->ratio ?? 0;

                UserWeight::where('user_id', $sendRatio->user_id)->update(['ratio' => $ratio]);
            }
        }
    }
}
<?php

namespace App\Console\Commands;

use App\Extensions\Weight\Algorithm\OrderSix;
use App\Extensions\Weight\Algorithm\OrderSuccess;
use App\Extensions\Weight\Algorithm\OrderUseTime;
use App\Models\User;
use App\Models\UserWeight;
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

            // 更新用户权重值
            UserWeight::where('user_id', $user)->update($weight);
        }
    }
}
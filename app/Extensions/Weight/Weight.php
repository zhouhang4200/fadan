<?php
namespace App\Extensions\Weight;

use App\Exceptions\CustomException;
use App\Models\User;
use App\Models\UserWeight;
use Illuminate\Support\Facades\Cache;

/**
 * Class Weight
 * @package App\Extensions\Weight
 */
class Weight
{
    /**
     * 计算后的用户权重
     * @var int
     */
    public $afterCompute;

    /**
     * 将所有权重值相加
     * @var
     */
    public $afterSum = [];

    /**
     * 调用算法计算用户的权重
     * @param $users
     * @param integer $orderNo 订单号
     * @return int|string
     */
    public function run(array $users, $orderNo)
    {
        // 将所有用户主账号ID缓存起来最后弹出的是传入进来的下单用户的id
        $originUsers = [];
        $primaryUsers = [];
        foreach ($users as $user) {
            $primaryUser = Cache::rememberForever(config('redis.user.getPrimaryId') . $user, function () use ($user) {
                return User::find($user)->getPrimaryUserId();
            });
            // 将传入的用户ID与它的主账号ID关联
            $originUsers[$primaryUser] = $user;
            // 将所有传入的ID 存入新的数组，用于计算权重
            $primaryUsers[] = $primaryUser;
        }
        // 初始化用户的权重值
        foreach ($primaryUsers as $v) {
            $this->afterComputeWeight[][$v] = 10;
        }
        // 获取所有算法
        $algorithms = config('weight.algorithm');
        // 调用算法计算用户的权重
        foreach ($algorithms as $algorithm) {
            $this->afterComputeWeight[] =  $algorithm::compute($primaryUsers);
        }
        //
        if (!$this->afterComputeWeight) {
            return 'error';
        }

        // 将相同商户权重值相加
        foreach ($this->afterComputeWeight as $v) {
            foreach ($v as $i => $j) {
                if (isset($this->afterSum[$i])) {
                    $this->afterSum[$i] += $j;
                } else {
                    $this->afterSum[$i] = $j;
                }
            }
        }
        // 记录计算后的值
        \Log::alert(json_encode(['订单号' =>  $orderNo, '调算法后' => $this->afterComputeWeight, '相加后' => $this->afterSum],
            JSON_UNESCAPED_UNICODE));

        $receivingUserId = $this->getUserId($orderNo);
        // 返回最终的商户ID
        if (!isset($originUsers[$receivingUserId])) {
            \Log::alert(json_encode(['下标越界',  $orderNo, $originUsers], JSON_UNESCAPED_UNICODE));
            return array_pop($originUsers);
        } else {
            return $originUsers[$receivingUserId];
        }
    }

    /**
     * 手动调整权重值
     */
    private function manualAdjustment()
    {
        // 获取总的权重
        $weightTotal = array_sum($this->afterSum);

        // 计算出权限的百分比, 遍历找到用户是否需要加权重，是否开始计算 否则跳过
        foreach ($this->afterSum as $user => $weight) {
            $addWeightPercent = UserWeight::where('user_id', $user)->firist();
            if ($addWeightPercent) {
                $currentWeight = $weightTotal - $weight;
                // 当前用户百分比
                $currentUserPercent = bcmul(bcdiv($weight, $weightTotal, 4), 100);
                // 增加后的百分比
                $finalPercent  = bcadd($currentUserPercent, $addWeightPercent->weight_percent);
                // 计算加了百分比后的权重
                $this->afterSum[$user] =  bcdiv(bcmul($currentWeight, $finalPercent), bcsub(100, $finalPercent));
            }
        }
        return $this->afterSum;
    }

    /**
     * 根据权重获取最后商户ID
     * @param $orderNo
     * @return int|string
     */
    protected function getUserId($orderNo)
    {
        // 调用手动调整的值
//        $usersWeight =  $this->manualAdjustment();
        $usersWeight =  $this->afterSum;

        try {
            $userId = 0;
            $randNum = mt_rand(1, array_sum($usersWeight));
            $tmpWeight = 0;
            foreach ($this->afterSum as $user => $weight) {
                if ($randNum <= $weight + $tmpWeight) {
                    $userId = $user;
                    break;
                } else {
                    $tmpWeight += $weight;
                }
            }
            // 删除用户接单记录
            foreach ($usersWeight as $user => $weight) {
                if ($user != $userId) {
                    receivingRecordDelete($user, $orderNo);
                }
            }
            // 删除所有用户接单列队
            receivingUserDel($orderNo);
            \Log::alert($orderNo .' 接单ID ' . $userId);

            return $userId;
        } catch (CustomException $exception) {
            \Log::alert(json_encode(['计算用户权重异常', $orderNo, $usersWeight]));
        }
    }

}
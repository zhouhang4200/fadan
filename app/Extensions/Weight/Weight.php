<?php
namespace App\Extensions\Weight;

use App\Exceptions\CustomException;
use App\Models\User;
use Config;
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
     * @param $orderNo 订单号
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
        $algorithms = Config::get('weight.algorithm');
        // 调用算法计算用户的权重
        foreach ($algorithms as $algorithm) {
            $this->afterComputeWeight[] =  $algorithm::compute($primaryUsers);
        }
        //
        if (!$this->afterComputeWeight) {
            return 'error';
        }
        // 记录计算后的值
        \Log::alert(json_encode(['计算后的值',  $orderNo, $this->afterComputeWeight], JSON_UNESCAPED_UNICODE));

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
        // 返回最终的商户ID
        if (!isset($originUsers[$this->getUserId($orderNo)])) {
            \Log::alert(json_encode(['下标越界',  $orderNo, $originUsers], JSON_UNESCAPED_UNICODE));
            return array_pop($originUsers);
        } else {
            return $originUsers[$this->getUserId($orderNo)];
        }
    }

    /**
     * 手动调整权重值
     */
    private function manualAdjustment()
    {
        // a=>10  b=>10 c=>10
        // (30 - 10)*50 / 100 -50

    }

    /**
     * 根据权重获取最后商户ID
     * @param $orderNo
     * @return int|string
     */
    protected function getUserId($orderNo)
    {
        \Log::alert(json_encode([$orderNo, $this->afterSum]));
        try {
            $userId = 0;
            $randNum = mt_rand(1, array_sum($this->afterSum));
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
            foreach ($this->afterSum as $user => $weight) {
                if ($user != $userId) {
                    receivingRecordDelete($user, $orderNo);
                }
            }
            // 删除所有用户接单列队
            receivingUserDel($orderNo);
            return $userId;
        } catch (CustomException $exception) {
            \Log::alert(json_encode(['error-get-user', $orderNo, $this->afterSum]));
        }
    }

}
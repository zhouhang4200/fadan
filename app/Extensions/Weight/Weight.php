<?php
namespace App\Extensions\Weight;

use Config;

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
    public $afterSum;

    /**
     * 调用算法计算用户的权重
     * @param array $user
     * @return int|string
     */
    public function run(array $user)
    {
        // 获取所有算法
        $algorithms = Config::get('weight.algorithm');
        // 调用算法计算用户的权重
        foreach ($algorithms as $algorithm) {
            $this->afterComputeWeight[] =  $algorithm::compute($user);
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
        // 返回最终的商户ID
        return $this->getUserId();
    }

    /**
     * 根据权重获取最后商户ID
     * @return int|string
     */
    protected function getUserId()
    {
        try {
            $userId = 0;
            $randNum = mt_rand(1, array_sum($this->afterSum));
            $tmpWeight = 0; // 10
            foreach ($this->afterSum as $user => $weight) {
                if ($randNum <= $weight + $tmpWeight) {
                    $userId = $user;
                    break;
                } else {
                    $tmpWeight += $weight;
                }
            }
            return $userId;
        } catch (\Exception $exception) {

        }
    }

}
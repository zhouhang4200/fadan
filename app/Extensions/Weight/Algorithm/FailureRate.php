<?php
namespace App\Extensions\Weight\Algorithm;

/**
 * Class FailureRate
 * @package App\Extensions\Weight\Algorithm
 */
class FailureRate implements AlgorithmInterface
{
    /**
     * @param $userId
     * @return string
     */
    public static function compute($userId)
    {
        return  '计算失败率';
    }
}
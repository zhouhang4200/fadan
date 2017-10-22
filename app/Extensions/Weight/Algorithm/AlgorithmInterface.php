<?php
namespace App\Extensions\Weight\Algorithm;

/**
 * Interface AlgorithmInterface
 * @package App\Extensions\Weight\Algorithm
 */
interface AlgorithmInterface
{
    /**
     * @param $userId
     * @return mixed
     */
    public static function compute($userId);
}
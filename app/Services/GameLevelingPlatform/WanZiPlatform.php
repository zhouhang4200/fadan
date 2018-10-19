<?php

namespace App\Services\GameLevelingPlatform;

class WanZiPlatform implements GameLevelingPlatformInterface
{
    public static function onSale($order){}
    public static function offSale($order){}
    public static function take($order){}
    public static function applyConsult($order){}
    public static function cancelConsult($order){}
    public static function agreeConsult($order){}
    public static function refuseConsult($order){}
    public static function forceDelete($order){}
    public static function applyComplain($order){}
    public static function cancelComplain($order){}
    public static function arbitration($order){}
    public static function applyComplete($order){}
    public static function cancelComplete($order){}
    public static function complete($order){}
    public static function lock($order){}
    public static function cancelLock($order){}
    public static function anomaly($order){}
    public static function cancelAnomaly($order){}
    public static function delete($order){}
    public static function modifyOrder($order){}
    public static function addTime($order){}
    public static function addAmount($order){}
    public static function getOrderDetail($order){}
    public static function getScreenShot($order){}
    public static function getMessage($order){}
    public static function replyMessage($order){}
    public static function modifyGamePassword($order){}
    public static function sendCompleteImage($order){}
    public static function getComplainDetail($order){}
    public static function addComplainDetail($order){}
}

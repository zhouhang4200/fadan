<?php

namespace App\Services\GameLevelingPlatform;

use App\Models\GameLevelingOrder;

class MaYiPlatform implements GameLevelingPlatformInterface
{
    public static function onSale(GameLevelingOrder $order){}
    public static function offSale(GameLevelingOrder $order){}
    public static function take(GameLevelingOrder $order){}
    public static function applyConsult(GameLevelingOrder $order){}
    public static function cancelConsult(GameLevelingOrder $order){}
    public static function agreeConsult(GameLevelingOrder $order){}
    public static function refuseConsult(GameLevelingOrder $order){}
    public static function forceDelete(GameLevelingOrder $order){}
    public static function applyComplain(GameLevelingOrder $order, $pic){}
    public static function cancelComplain(GameLevelingOrder $order){}
    public static function arbitration(GameLevelingOrder $order){}
    public static function applyComplete(GameLevelingOrder $order){}
    public static function cancelComplete(GameLevelingOrder $order){}
    public static function complete(GameLevelingOrder $order){}
    public static function lock(GameLevelingOrder $order){}
    public static function cancelLock(GameLevelingOrder $order){}
    public static function anomaly(GameLevelingOrder $order){}
    public static function cancelAnomaly(GameLevelingOrder $order){}
    public static function delete(GameLevelingOrder $order){}
    public static function modifyOrder(GameLevelingOrder $order){}
    public static function addTime(GameLevelingOrder $order){}
    public static function addAmount(GameLevelingOrder $order){}
    public static function getOrderDetail(GameLevelingOrder $order){}
    public static function getScreenShot(GameLevelingOrder $order){}
    public static function getMessage(GameLevelingOrder $order){}
    public static function replyMessage(GameLevelingOrder $order, $message){}
    public static function modifyGamePassword(GameLevelingOrder $order){}
    public static function sendImage(GameLevelingOrder $order, $pic){}
    public static function getComplainDetail(GameLevelingOrder $order){}
    public static function addComplainDetail(GameLevelingOrder $order, $pic, $content){}
}

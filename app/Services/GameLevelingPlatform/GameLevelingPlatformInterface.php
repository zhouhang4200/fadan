<?php

namespace App\Services\GameLevelingPlatform;

use App\Models\GameLevelingOrder;

/**
 * 第三方平台操作接口
 */
interface GameLevelingPlatformInterface
{
    public static function onSale(GameLevelingOrder $order); // 上架
    public static function offSale(GameLevelingOrder $order); // 下架
    public static function take(GameLevelingOrder $order); // 接单
    public static function applyConsult(GameLevelingOrder $order); // 申请撤销
    public static function cancelConsult(GameLevelingOrder $order); // 取消撤销
    public static function agreeConsult(GameLevelingOrder $order); // 同意撤销
    public static function refuseConsult(GameLevelingOrder $order); // 不同意撤销
    public static function forceDelete(GameLevelingOrder $order); // 强制撤单
    public static function applyComplain(GameLevelingOrder $order, $pic); // 申请仲裁
    public static function cancelComplain(GameLevelingOrder $order); // 取消仲裁
    public static function arbitration(GameLevelingOrder $order); // 强制仲裁(客服仲裁)
    public static function applyComplete(GameLevelingOrder $order); // 申请验收
    public static function cancelComplete(GameLevelingOrder $order); // 取消验收
    public static function complete(GameLevelingOrder $order); // 完成验收
    public static function lock(GameLevelingOrder $order); // 锁定
    public static function cancelLock(GameLevelingOrder $order); // 取消锁定
    public static function anomaly(GameLevelingOrder $order); // 异常
    public static function cancelAnomaly(GameLevelingOrder $order); // 取消异常
    public static function delete(GameLevelingOrder $order); // 删除(撤单)

    public static function modifyOrder(GameLevelingOrder $order); // 修改订单
    public static function addTime(GameLevelingOrder $order); // 订单加时
    public static function addAmount(GameLevelingOrder $order); // 订单加款
    public static function getOrderDetail(GameLevelingOrder $order); // 获取订单详情
    public static function getScreenShot(GameLevelingOrder $order); // 获取订单截图
    public static function getMessage(GameLevelingOrder $order); // 获取留言
    public static function replyMessage(GameLevelingOrder $order, $message); // 回复留言
    public static function modifyGamePassword(GameLevelingOrder $order); // 更改接单后的游戏账号密码
    public static function sendImage(GameLevelingOrder $order, $pic); // 发送完成截图
    public static function getComplainDetail(GameLevelingOrder $order); // 获取仲裁信息（留言，截图)
    public static function addComplainDetail(GameLevelingOrder $order, $pic, $content); // 增加仲裁信息（留言，截图)
}

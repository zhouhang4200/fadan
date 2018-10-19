<?php

namespace App\Services\GameLevelingPlatform;

/**
 * 第三方平台操作接口
 */
interface GameLevelingPlatformInterface
{
    public static function onSale($order); // 上架
    public static function offSale($order); // 下架
    public static function take($order); // 接单
    public static function applyConsult($order); // 申请撤销
    public static function cancelConsult($order); // 取消撤销
    public static function agreeConsult($order); // 同意撤销
    public static function refuseConsult($order); // 不同意撤销
    public static function forceDelete($order); // 强制撤单
    public static function applyComplain($order); // 申请仲裁
    public static function cancelComplain($order); // 取消仲裁
    public static function arbitration($order); // 强制仲裁(客服仲裁)
    public static function applyComplete($order); // 申请验收
    public static function cancelComplete($order); // 取消验收
    public static function complete($order); // 完成验收
    public static function lock($order); // 锁定
    public static function cancelLock($order); // 取消锁定
    public static function anomaly($order); // 异常
    public static function cancelAnomaly($order); // 取消异常
    public static function delete($order); // 删除(撤单)

    public static function modifyOrder($order); // 修改订单
    public static function addTime($order); // 订单加时
    public static function addAmount($order); // 订单加款
    public static function getOrderDetail($order); // 获取订单详情
    public static function getScreenShot($order); // 获取订单截图
    public static function getMessage($order); // 获取留言
    public static function replyMessage($order); // 回复留言
    public static function modifyGamePassword($order); // 更改接单后的游戏账号密码
    public static function sendCompleteImage($order); // 发送完成截图
    public static function getComplainDetail($order); // 获取仲裁信息（留言，截图)
    public static function addComplainDetail($order); // 增加仲裁信息（留言，截图)
}

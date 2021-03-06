<?php

namespace App\Services\GameLevelingPlatform;

use App\Models\GameLevelingOrder;

/**
 * 第三方平台操作接口
 */
interface GameLevelingPlatformServiceInterface
{
    public static function onSale(GameLevelingOrder $order); // 上架
    public static function offSale(GameLevelingOrder $order); // 下架
    public static function applyConsult(GameLevelingOrder $order); // 申请撤销
    public static function cancelConsult(GameLevelingOrder $order); // 取消撤销
    public static function agreeConsult(GameLevelingOrder $order); // 同意撤销
    public static function rejectConsult(GameLevelingOrder $order); // 不同意撤销
    public static function applyComplain(GameLevelingOrder $order, $pic); // 申请仲裁
    public static function cancelComplain(GameLevelingOrder $order); // 取消仲裁
    public static function complete(GameLevelingOrder $order); // 完成验收
    public static function lock(GameLevelingOrder $order); // 锁定
    public static function cancelLock(GameLevelingOrder $order); // 取消锁定
    public static function delete(GameLevelingOrder $order); // 删除(撤单)

    public static function modifyOrder(GameLevelingOrder $order); // 修改订单
    public static function addTime(GameLevelingOrder $order); // 订单加时
    public static function addAmount(GameLevelingOrder $order); // 订单加款
    public static function orderInfo(GameLevelingOrder $order); // 获取订单详情
    public static function getScreenShot(GameLevelingOrder $order); // 获取订单截图
    public static function getMessage(GameLevelingOrder $order); // 获取留言
    public static function replyMessage(GameLevelingOrder $order, $message); // 回复留言
    public static function modifyGamePassword(GameLevelingOrder $order); // 更改接单后的游戏账号密码
    public static function complainInfo(GameLevelingOrder $order); // 获取仲裁信息（留言，截图)
    public static function addComplainDetail(GameLevelingOrder $order, $pic, $content); // 增加仲裁信息（留言，截图)
}

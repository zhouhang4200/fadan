<?php

namespace App\Services\Leveling;

/**
 * 第三方平台操作接口
 */
interface LevelingInterface 
{
    public static function onSale($orderDatas); // 上架 
	public static function offSale($orderDatas); // 下架 
	public static function receive($orderDatas); // 接单
	public static function applyRevoke($orderDatas); // 申请撤销 
	public static function cancelRevoke($orderDatas); // 取消撤销 
	public static function agreeRevoke($orderDatas); // 同意撤销 
	public static function forceRevoke($orderDatas); // 强制撤销 
	public static function refuseRevoke($orderDatas); // 不同意撤销
	public static function applyArbitration($orderDatas); // 申请仲裁 
	public static function cancelArbitration($orderDatas); // 取消仲裁 
	public static function customArbitration($orderDatas); // 强制仲裁(客服仲裁)
	public static function applyComplete($orderDatas); // 申请验收 
	public static function cancelComplete($orderDatas); // 取消验收 
	public static function complete($orderDatas); // 完成验收 
	public static function lock($orderDatas); // 锁定 
	public static function cancelLock($orderDatas); // 取消锁定 
	public static function abnormal($orderDatas); // 异常 
	public static function cancelAbnormal($orderDatas); // 取消异常
	public static function delete($orderDatas); // 删除(撤单)

	public static function updateOrder($orderDatas); // 修改订单
	public static function addTime($orderDatas); // 订单加时
	public static function addMoney($orderDatas); // 订单加款
	public static function orderDetail($orderDatas); // 获取订单详情
	public static function getScreenshot($orderDatas); // 获取订单截图
	public static function getMessage($orderDatas); // 获取留言
	public static function replyMessage($orderDatas); // 回复留言
	public static function updateAccountAndPassword($orderDatas); // 更改接单后的游戏账号密码
}

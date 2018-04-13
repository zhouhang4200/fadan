<?php

namespace App\Services\Leveling;

/**
 * 第三方平台操作接口
 */
interface LevelingInterface 
{
    public function onSale($orderDatas); // 上架 
	public function offSale($orderDatas); // 下架 
	public function receive($orderDatas); // 接单
	public function applyRevoke($orderDatas); // 申请撤销 
	public function cancelRevoke($orderDatas); // 取消撤销 
	public function agreeRevoke($orderDatas); // 同意撤销 
	public function forceRevoke($orderDatas); // 强制撤销 
	public function refuseRevoke($orderDatas); // 不同意撤销
	public function applyArbitration($orderDatas); // 申请仲裁 
	public function cancelArbitration($orderDatas); // 取消仲裁 
	public function customArbitration($orderDatas); // 强制仲裁(客服仲裁)
	public function applyComplete($orderDatas); // 申请验收 
	public function cancelComplete($orderDatas); // 取消验收 
	public function complete($orderDatas); // 完成验收 
	public function lock($orderDatas); // 锁定 
	public function cancelLock($orderDatas); // 取消锁定 
	public function abnormal($orderDatas); // 异常 
	public function cancelAbnormal($orderDatas); // 取消异常
	public function delete($orderDatas); // 删除(撤单)

	public function updateOrder($orderDatas); // 修改订单
	public function addTime($orderDatas); // 订单加时
	public function addMoney($orderDatas); // 订单加款
	public function orderDetail($orderDatas); // 获取订单详情
	public function getScreenshot($orderDatas); // 获取订单截图
	public function getMessage($orderDatas); // 获取留言
	public function replyMessage($orderDatas, $message); // 回复留言
	public function updateAccountAndPassword($orderDatas); // 更改接单后的游戏账号密码
}

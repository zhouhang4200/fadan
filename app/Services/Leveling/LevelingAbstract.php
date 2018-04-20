<?php

namespace App\Services\Leveling;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * 第三方平台操作构造类
 */
class LevelingAbstract extends Controller implements LevelingInterface
{
	/**
	 * 上架
	 * @return [type] [description]
	 */
    public static function onSale($orderDatas) {}
    /**
     * 下架
     * @return [type] [description]
     */
	public static function offSale($orderDatas) {} 

	/**
	 * 接单
	 * @return [type] [description]
	 */
	public static function receive($orderDatas) {}

	/**
	 * 申请撤销
	 * @return [type] [description]
	 */
	public static function applyRevoke($orderDatas) {} 

	/**
	 * 取消撤销
	 * @param  [type] $orderDatas [description]
	 * @return [type]             [description]
	 */
	public static function cancelRevoke($orderDatas) {} 

	/**
	 * 同意撤销
	 * @param  [type] $orderDatas [description]
	 * @return [type]             [description]
	 */
	public static function agreeRevoke($orderDatas) {} 

	/**
	 * 强制撤销
	 * @param  [type] $orderDatas [description]
	 * @return [type]             [description]
	 */
	public static function forceRevoke($orderDatas) {} 

	/**
	 * 不同意撤销
	 * @param  [type] $orderDatas [description]
	 * @return [type]             [description]
	 */
	public static function refuseRevoke($orderDatas) {} 

	/**
	 * 申请仲裁
	 * @param  [type] $orderDatas [description]
	 * @return [type]             [description]
	 */
	public static function applyArbitration($orderDatas) {} 

	/**
	 * 取消仲裁
	 * @param  [type] $orderDatas [description]
	 * @return [type]             [description]
	 */
	public static function cancelArbitration($orderDatas) {} 

	/**
	 * 强制仲裁（客服仲裁
	 * @param  [type] $orderDatas [description]
	 * @return [type]             [description]
	 */
	public static function customArbitration($orderDatas) {} 

	/**
	 * 申请验收
	 * @param  [type] $orderDatas [description]
	 * @return [type]             [description]
	 */
	public static function applyComplete($orderDatas) {} 

	/**
	 * 取消验收
	 * @param  [type] $orderDatas [description]
	 * @return [type]             [description]
	 */
	public static function cancelComplete($orderDatas) {} 

	/**
	 * 完成验收
	 * @param  [type] $orderDatas [description]
	 * @return [type]             [description]
	 */
	public static function complete($orderDatas) {} 

	/**
	 * 锁定
	 * @param  [type] $orderDatas [description]
	 * @return [type]             [description]
	 */
	public static function lock($orderDatas) {} 

	/**
	 * 取消锁定
	 * @param  [type] $orderDatas [description]
	 * @return [type]             [description]
	 */
	public static function cancelLock($orderDatas) {} 

	/**
	 * 异常
	 * @param  [type] $orderDatas [description]
	 * @return [type]             [description]
	 */
	public static function abnormal($orderDatas) {} 

	/**
	 * 取消异常
	 * @param  [type] $orderDatas [description]
	 * @return [type]             [description]
	 */
	public static function cancelAbnormal($orderDatas) {} 

	/**
	 * 撤单（删除)
	 * @param  [type] $orderDatas [description]
	 * @return [type]             [description]
	 */
	public static function delete($orderDatas) {} 

	/** 以下是后面新增的  */

	/**
	 * 修改订单
	 * @return [type] [description]
	 */
	public static function updateOrder($orderDatas) {}

	/**
	 * 订单加时
	 */
	public static function addTime($orderDatas) {}

	/**
	 * 订单加款
	 */
	public static function addMoney($orderDatas) {}

	/**
	 * 获取订单详情
	 * @return [type] [description]
	 */
	public static function orderDetail($orderDatas) {} 

	/**
	 * 获取订单截图
	 * @return [type] [description]
	 */
	public static function getScreenshot($orderDatas) {} 

	/**
	 * 获取留言
	 * @return [type] [description]
	 */
	public static function getMessage($orderDatas) {}

	/**
	 * 回复留言
	 * @return [type] [description]
	 */
	public static function replyMessage($orderDatas, $message) {}

	/**
	 * 更改接单后的游戏账号密码
	 * @param  [type] $orderDatas [description]
	 * @return [type]             [description]
	 */
	public static function updateAccountAndPassword($orderDatas) {}
}

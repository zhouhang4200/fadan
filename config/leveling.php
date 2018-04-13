<?php

use App\Services\Show91;
use App\Services\DailianMama;
use App\Services\Leveling\MayiDailianController;

// 代练平台操作自动匹配第三方平台
return [
	// 第三方平台我们这边的账号ID对应的平台ID
	'third' => [
		// 8596 => 3,
		// 8597 => 4,
	],

	// 外部平台存在订单详情表里面的订单号字段，接单的时候，下架其他平台订单, 平台号 =》 平台订单字段名称
	'third_orders' => [
		// 3 => 'mayi_order_no',
		// 4 => 'dd373_order_no',
	],

	// 调用第三方平台接口的控制器名称
	'controller' => [
		3 => MayiDailianController::class,
		4 => DD373Controller::class,
	],

	// 调用第三方平台接口控制器中的方法名，下面的方法 key 与 value 相同，本来多此一举，便于查看
	'action' => [
		'onSale'                   => 'onSale', // 上架 
		'offSale'                  => 'offSale', // 下架 
		'receive'                  => 'receive', // 接单
		'applyRevoke'              => 'applyRevoke', // 申请撤销 
		'cancelRevoke'             => 'cancelRevoke', // 取消撤销 
		'agreeRevoke'              => 'agreeRevoke', // 同意撤销 
		'forceRevoke'              => 'forceRevoke', // 强制撤销 
		'refuseRevoke'             => 'refuseRevoke', // 不同意撤销
		'applyArbitration'         => 'applyArbitration', // 申请仲裁 
		'cancelArbitration'        => 'cancelArbitration', // 取消仲裁 
		'customArbitration'        => 'customArbitration', // 强制仲裁(客服仲裁)
		'applyComplete'            => 'applyComplete', // 申请验收 
		'cancelComplete'           => 'cancelComplete', // 取消验收 
		'complete'                 => 'complete', // 完成验收 
		'lock'                     => 'lock', // 锁定 
		'cancelLock'               => 'cancelLock', // 取消锁定 
		'abnormal'                 => 'abnormal', // 异常 
		'cancelAbnormal'           => 'cancelAbnormal', // 取消异常
		'delete'                   => 'delete', // 删除(撤单)
		
		'updateOrder'              => 'updateOrder', // 修改订单
		'addTime'                  => 'addTime', // 订单加时
		'addMoney'                 => 'addMoney', // 订单加款
		'orderDetail'              => 'orderDetail', // 获取订单详情
		'getScreenshot'            => 'getScreenshot', // 获取订单截图
		'getMessage'               => 'getMessage', // 获取留言
		'replyMessage'             => 'replyMessage', // 回复留言
		'updateAccountAndPassword' => 'updateAccountAndPassword', // 修改游戏账号密码
	],

	// 蚂蚁代练的信息
	'mayidailian' => [
		'appid'     => '24dca31042361ae3',
		'appsecret' => '889a68ce24dca31042361ae3fa423f56',
		'Ver'       => '1.0',
		'url'       => 'dailian.zuhaowan.com/OpenApi/GateWay.html', // 蚂蚁代练的接口地址
		'password'  => '123456',
	],

	'dd373' => [

	],
];
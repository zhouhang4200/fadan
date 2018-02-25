<?php

return [
	'url' => [
			'releaseOrder'              => 'http://inf.dailianmama.com/releaseOrder.intf', // 发布订单和修改订单接口
			'upOrder'                   => 'http://inf.dailianmama.com/upOrder.intf', // 上架
			'closeOrder'                => 'http://inf.dailianmama.com/closeOrder.intf', // 下架
			'deleteOrder'               => 'http://inf.dailianmama.com/deleteOrder.intf', // 删除订单
			'orderinfo'                 => 'http://inf.dailianmama.com/orderinfo.intf', // 查询订单信息
			'refreshAllOrderTime'       => 'http://inf.dailianmama.com/refreshAllOrderTime.intf', // 刷新未接单订单
			'getReleaseOrderStatusList' => 'http://inf.dailianmama.com/getReleaseOrderStatusList.intf', // 获得发布订单状态列表
			'checkTradePassword'        => 'http://inf.dailianmama.com/checkTradePassword.intf', // 支付密码验证
			'operationOrder'            => 'http://inf.dailianmama.com/operationOrder.intf', // 订单操作
			'chatOldList'               => 'http://inf.dailianmama.com/chatOldList.intf', // 获取订单留言记录
			'getOrderPictureList'       => 'http://inf.dailianmama.com/getOrderPictureList.intf', // 获取订单截图记录
			'addChat'                   => 'http://inf.dailianmama.com/addChat.intf', //发送订单留言
			'savePicture'               => 'http://inf.dailianmama.com/savePicture.intf', //保存订单留言
			'getTempUploadKey'          => 'http://inf.dailianmama.com/getTempUploadKey.intf', 
											//获取临时的上传凭证，获取后一小时内可用
			'releaseOrderManageList'    => 'http://inf.dailianmama.com/releaseOrderManageList.intf', 
											//发单管理接口，查询发布时间在3个月内的订单
			'getOrderPrice'             => 'http://inf.dailianmama.com/getOrderPrice.intf', // 获得订单价格
			'gameInfo' 					=> 'http://static.dailianmama.com/tool/dlmm/gameinfo.html', // 游戏区服详情
	],
];
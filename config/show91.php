<?php

return [
	'account' => '12', // 账号
	'sign' => md5('b'), // 签名
	'url' => [
		'getGames' => 'http://www.show91.com/oauth/getGames', // 获得状态正常的游戏
		'getAreas' => 'http://www.show91.com/oauth/getAreas', // 根据gameid获得游戏区
		'getServer' => 'http://www.show91.com/oauth/getServer', // 根据areaid获得服务器
		'addOrder' => 'http://www.show91.com/oauth/addOrder', // 发布订单
		'editOrderAccPwd' => 'http://www.show91.com/oauth/editOrderAccPwd', // 修改订单的游戏账号密码
		'changeOrderBlock' => 'http://www.show91.com/oauth/changeOrderBlock', // 锁定游戏帐号密码
		'orderStatus' => 'http://www.show91.com/oauth/orderStatus', // 获得订单状态接口
		'orderDetail' => 'http://www.show91.com/oauth/orderDetail', // 获得订单详情接口
		'cancelOrder' => 'http://www.show91.com/oauth/cancelOrder', // 协商结算，退单
		'addappeal' => 'http://www.show91.com/oauth/addappeal', // 提交申诉
		'seeappeal' => 'http://www.show91.com/oauth/seeappeal', // 申诉查看
		'addMess' => 'http://www.show91.com/oauth/addMess', // 协商留言
		'addCancelOrder' => 'http://www.show91.com/oauth/addCancelOrder', // 提交协商请求
		'confirmSc' => 'http://www.show91.com/oauth/confirmSc', // 确认协商接口
		'cancelSc' => 'http://www.show91.com/oauth/cancelSc', // 撤销协商接口
		'cancelAppeal' => 'http://www.show91.com/oauth/cancelAppeal', // 撤销申诉
		'topic' => 'http://www.show91.com/oauth/topic', // 查看订单截图
		'addpic' => 'http://www.show91.com/oauth/addpic', // 上传截图
		'chedan' => 'http://www.show91.com/oauth/chedan', // 对已发布的订单发起主动撤单，返回资金
		'accept' => 'http://www.show91.com/oauth/accept', // 订单确认验收结算
		'messageList' => 'http://www.show91.com/oauth/messageList', // 获取订单留言
		'addevidence' => 'http://www.show91.com/oauth/addevidence', // 添加申诉证据
		'addPrice' => 'http://www.show91.com/oauth/addPrice', // 订单补款
		'addLimitTime' => 'http://www.show91.com/oauth/addLimitTime', // 增加订单代练时间
		'confirmAt' => 'http://www.show91.com/oauth/confirmAt', // 确认增加代练时间
		'grounding' => 'http://www.show91.com/oauth/grounding', // 订单上下架
		'addLimitTime2' => 'http://www.show91.com/oauth/addLimitTime2', // 增加代练时间，商家用
	],
];
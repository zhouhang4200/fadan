<?php

return [
    'uid' => 314027, // 平台的91用户id
    'password' => env('SHOW91_PAY_PASSWORD'), // 91官方账号的交易密码
    'qs_user_id' => 8456, // 91在千手的用户ID
	'account' => env('ACCOUNT'), // 账号
	'sign' => env('SIGN'), // 签名 md5(13FA3E0C456A4368A66D6D2FEAEBAD93 + EFAE2BC69B8D4E16A3649992F031BDDB)
	'url' => [
		'getGames'         => 'http://tm.38sd.com:81/api/oauth/getGames', // 获得状态正常的游戏
		'getAreas'         => 'http://tm.38sd.com:81/api/oauth/getAreas', // 根据gameid获得游戏区
		'getServer'        => 'http://tm.38sd.com:81/api/oauth/getServer', // 根据areaid获得服务器
		'addOrder'         => 'http://tm.38sd.com:81/api/oauth/addOrder', // 发布订单
		'editOrderAccPwd'  => 'http://tm.38sd.com:81/api/oauth/editOrderAccPwd', // 修改订单的游戏账号密码
		'changeOrderBlock' => 'http://tm.38sd.com:81/api/oauth/changeOrderBlock', // 锁定游戏帐号密码
		'orderStatus'      => 'http://tm.38sd.com:81/api/oauth/orderStatus', // 获得订单状态接口
		'orderDetail'      => 'http://tm.38sd.com:81/api/oauth/orderDetail', // 获得订单详情接口
		'cancelOrder'      => 'http://tm.38sd.com:81/api/oauth/cancelOrder', // 协商结算，退单
		'addappeal'        => 'http://tm.38sd.com:81/api/oauth/addappeal', // 提交申诉
		'seeappeal'        => 'http://tm.38sd.com:81/api/oauth/seeappeal', // 申诉查看
		'addMess'          => 'http://tm.38sd.com:81/api/oauth/addMess', // 协商留言
		'addCancelOrder'   => 'http://tm.38sd.com:81/api/oauth/addCancelOrder', // 提交协商请求
		'confirmSc'        => 'http://tm.38sd.com:81/api/oauth/confirmSc', // 确认协商接口
		'cancelSc'         => 'http://tm.38sd.com:81/api/oauth/cancelSc', // 撤销协商接口
		'cancelAppeal'     => 'http://tm.38sd.com:81/api/oauth/cancelAppeal', // 撤销申诉
		'topic'            => 'http://tm.38sd.com:81/api/oauth/topic', // 查看订单截图
		'addpic'           => 'http://tm.38sd.com:81/api/oauth/addpic', // 上传截图
		'chedan'           => 'http://tm.38sd.com:81/api/oauth/chedan', // 对已发布的订单发起主动撤单，返回资金
		'accept'           => 'http://tm.38sd.com:81/api/oauth/accept',  // 订单确认验收结算
		'messageList'      => 'http://tm.38sd.com:81/api/oauth/messageList', // 获取订单留言
		'addevidence'      => 'http://tm.38sd.com:81/api/oauth/addevidence', // 添加申诉证据
		'addPrice'         => 'http://tm.38sd.com:81/api/oauth/addPrice', // 订单补款
		'addLimitTime'     => 'http://tm.38sd.com:81/api/oauth/addLimitTime', // 增加订单代练时间
		'confirmAt'        => 'http://tm.38sd.com:81/api/oauth/confirmAt', // 确认增加代练时间
		'grounding'        => 'http://tm.38sd.com:81/api/oauth/grounding', // 订单上下架
		'addLimitTime2'    => 'http://tm.38sd.com:81/api/oauth/addLimitTime2', // 增加代练时间，商家用
		'getPlays'    	   => 'http://tm.38sd.com:81/api/oauth/getPlays', // 增加代练时间，商家用
	],
];

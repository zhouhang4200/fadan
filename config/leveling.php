<?php

use App\Services\Show91;
use App\Services\DailianMama;
use App\Services\Leveling\DD373Controller;
use App\Services\Leveling\MayiDailianController;
use App\Services\Leveling\WanziController;
use App\Services\Leveling\Show91Controller;

// 代练平台操作自动匹配第三方平台
return [
	// 第三方平台我们这边的账号ID对应的平台ID
	'third' => [
		8737 => 3, // 蚂蚁
		8739 => 4, // dd373
		8456 => 1, // 91
		84568 => 5, // 丸子
	],

	// 外部平台存在订单详情表里面的订单号字段，接单的时候，下架其他平台订单, 平台号 =》 平台订单字段名称
	'third_orders' => [
		3 => 'mayi_order_no',
		4 => 'dd373_order_no',
		1 => 'show91_order_no',
		5 => 'wanzi_order_no',
	],
    // 外部平台价格字段
    'third_orders_price' => [
        3 => [
            'data' => 'data',
            'price' => 'paymoney',
        ],
        4 => [
            'data' => 'data',
            'price' => 'price',
        ],
        1 => [
            'data' => 'data',
            'price' => 'price',
        ],
        5 => [
            'data' => 'data',
            'price' => 'price',
        ],
    ],

	// 调用第三方平台接口的控制器名称
	'controller' => [
		4 => DD373Controller::class,
		3 => MayiDailianController::class,
		1 => Show91Controller::class,
		5 => WanziController::class,
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
		'updateImage'			   => 'updateImage', // 上传截图
		'getArbitrationInfo'       => 'getArbitrationInfo', // 获取仲裁详情
		'addArbitrationInfo'       => 'addArbitrationInfo', // '添加仲裁证据'
	],

	// 蚂蚁代练的信息
	'mayidailian' => [
		'appid'     => '2f666a796ba36a6b',
		'appsecret' => '66a225812f666a796ba36a6b6a151870',
		'Ver'       => '1.0',
		'url'       => env('MAYI_API_URL', 'http://www.mayidailian.com/OpenApi/GateWay/index'), // 蚂蚁代练的接口地址
		'password'  => '123456',
		'aes_key'   => '4l5846ssd8e4f5e8e4e2685',
		'aes_iv'    => '1234567891111152',
	],

	'dd373' => [
		'key'           => 'a8c487d230e0400a8333f976f2b621ef',
		'platform-sign' => '9cd4100e4af146f487284bb18f190c59',
		'appid'         => 'fPHUSGXWN461NRb5VGeFp0xoYYaGOAc0rXIqnMxRwAvCpYcQKR0xhFIJdSTI',
		'appsecret'     => 'ugG8gEvAMb207gbIC21MEh9HHnzdYqYQaio3w622IGyuZkWj3EyG28j92pWc',
		'aes_key'       => '45xd46a5d8e4f5e8e4e268x',
		'aes_iv'        => '1234567891111152',
		'url' => [
			'onSale'                   => env('DD373_API_URL', 'http://sdk.dd373.com/DLSdk.html').'?action=UpOrder', // 上架
			'offSale'                  => env('DD373_API_URL', 'http://sdk.dd373.com/DLSdk.html').'?action=DownOrder', // 下架
			'applyRevoke'              => env('DD373_API_URL', 'http://sdk.dd373.com/DLSdk.html').'?action=applyCancel', // 申请撤销
			'cancelRevoke'             => env('DD373_API_URL', 'http://sdk.dd373.com/DLSdk.html').'?action=CancelAction', // 取消撤销
			'agreeRevoke'              => env('DD373_API_URL', 'http://sdk.dd373.com/DLSdk.html').'?action=CancelAction', // 同意撤销
			'applyArbitration'         => env('DD373_API_URL', 'http://sdk.dd373.com/DLSdk.html').'?action=applyArbitrate', // 申请仲裁
			'cancelArbitration'        => env('DD373_API_URL', 'http://sdk.dd373.com/DLSdk.html').'?action=undoArbitrate', // 取消仲裁
			'complete'                 => env('DD373_API_URL', 'http://sdk.dd373.com/DLSdk.html').'?action=confirmOrder', // 订单完成
			'lock'                     => env('DD373_API_URL', 'http://sdk.dd373.com/DLSdk.html').'?action=lockAccount', // 锁定
			'cancelLock'               => env('DD373_API_URL', 'http://sdk.dd373.com/DLSdk.html').'?action=unlockAccount', // 取消锁定
			'delete'                   => env('DD373_API_URL', 'http://sdk.dd373.com/DLSdk.html').'?action=deleteOrder', // 删除订单
			'updateOrder'              => env('DD373_API_URL', 'http://sdk.dd373.com/DLSdk.html').'?action=modifyOrder', // 修改订单
			'addTime'                  => env('DD373_API_URL', 'http://sdk.dd373.com/DLSdk.html').'?action=addTime', // 加时
			'addMoney'                 => env('DD373_API_URL', 'http://sdk.dd373.com/DLSdk.html').'?action=addPrice', // 加款
			'orderDetail'              => env('DD373_API_URL', 'http://sdk.dd373.com/DLSdk.html').'?action=getDetails', // 订单详情
			'getScreenshot'            => env('DD373_API_URL', 'http://sdk.dd373.com/DLSdk.html').'?action=getImages', // 订单截图
			'getMessage'               => env('DD373_API_URL', 'http://sdk.dd373.com/DLSdk.html').'?action=getMessages', //获取留言
			'replyMessage'             => env('DD373_API_URL', 'http://sdk.dd373.com/DLSdk.html').'?action=sendMessage', // 回复留言
			'updateAccountAndPassword' => env('DD373_API_URL', 'http://sdk.dd373.com/DLSdk.html').'?action=changePwd', // 修改账号密码
			'refuseRevoke'             => env('DD373_API_URL', 'http://sdk.dd373.com/DLSdk.html').'?action=CancelAction', // 不同意撤销
			'updateImage'			   => env('DD373_API_URL', 'http://sdk.dd373.com/DLSdk.html').'?action=uploadImage', //上传截图
		],
		'status' => [
			'1'  => '未接单',
			'4'  => '代练中',
			'5'  => '待验收',
			'6'  => '已完成',
			'9'  => '已撤销',
			'10' => '已结算',
			'11' => '已锁定',
			'12' => '异常',
			'13' => '仲裁中',
			'14' => '已仲裁',
			'15' => '撤销中',
			'16' => '已退款',
			'17' => '已关闭',
		],
	],

	// 91平台
    'show91' => [
		'appid'     => 'RbO1SPEINbjU79DYrTpLxXAmJQxO6TYzVZ6awzyclvXejAQSMM98WBKxSRzm',
		'appsecret' => 'EsPKc2m5NjymkAd95SQFVI5hDHI3IHUOwVmeNTVbKU7xFdkxZODkOwma92BB',
		'aes_key'   => '45584685d8e4f5e8',
		'aes_iv'    => '1234567891111152',
		'account'   => env('SHOW91_ACCOUNT', '558ED3FCAA3E4722A8F8FEFB741AE40D'),
		'sign'      => env('SHOW91_SIGN','f1ba344cc00d3063ba6a8c14e7d0fc4c'),
		'password'  => env('SHOW91_PAY_PASSWORD', 'qqq111'),
    	'status' => [
	        0  => '已发布',
	        1  => '代练中',
	        2  => '待验收',
	        3  => '待结算',
	        4  => '已结算',
	        5  => '已挂起',
	        6  => '已撤单',
	        7  => '已取消',
	        8  => '已锁定',
	        10 => '等待工作室接单',
	        11 => '等待玩家付款',
	        12 => '玩家超时未付款',
    	],
    	'url' => [
			'onSale'                   => env('SHOW91_API_URL', 'http://www.show91.com').'/oauth/grounding', // 上架
			'offSale'                  => env('SHOW91_API_URL', 'http://www.show91.com').'/oauth/grounding', // 下架
			'applyRevoke'              => env('SHOW91_API_URL', 'http://www.show91.com').'/oauth/addCancelOrder', // 申请撤销
			'cancelRevoke'             => env('SHOW91_API_URL', 'http://www.show91.com').'/oauth/cancelSc', // 取消撤销
			'agreeRevoke'              => env('SHOW91_API_URL', 'http://www.show91.com').'/oauth/confirmSc', // 同意撤销
			'applyArbitration'         => env('SHOW91_API_URL', 'http://www.show91.com').'/oauth/addappeal', // 申请仲裁
			'cancelArbitration'        => env('SHOW91_API_URL', 'http://www.show91.com').'/oauth/cancelAppeal', // 取消仲裁
			'complete'                 => env('SHOW91_API_URL', 'http://www.show91.com').'/oauth/accept', // 订单完成
			'lock'                     => '', // 锁定
			'cancelLock'               => '', // 取消锁定
			'delete'                   => env('SHOW91_API_URL', 'http://www.show91.com').'/oauth/chedan', // 删除订单
			'updateOrder'              => env('SHOW91_API_URL', 'http://www.show91.com').'/oauth/qs/updateOrder', // 修改订单
			'addTime'                  => env('SHOW91_API_URL', 'http://www.show91.com').'/oauth/addLimitTime3', // 加时
			'addMoney'                 => env('SHOW91_API_URL', 'http://www.show91.com').'/oauth/addPrice2', // 加款
			'orderDetail'              => env('SHOW91_API_URL', 'http://www.show91.com').'/oauth/orderDetail', // 订单详情
			'getScreenshot'            => env('SHOW91_API_URL', 'http://www.show91.com').'/oauth/topic', // 订单截图
			'getMessage'               => env('SHOW91_API_URL', 'http://www.show91.com').'/oauth/messageList', //获取留言
			'replyMessage'             => env('SHOW91_API_URL', 'http://www.show91.com').'/oauth/addMess', // 回复留言addMess
			'updateAccountAndPassword' => env('SHOW91_API_URL', 'http://www.show91.com').'/oauth/editOrderAccPwd', // 修改账号密码
			'refuseRevoke'             => env('SHOW91_API_URL', 'http://www.show91.com').'/oauth/confirmSc', // 不同意撤销
			'getPlays'				   => env('SHOW91_API_URL', 'http://www.show91.com').'/oauth/getPlays', // 获取代练类型
			'setTop'				   => env('SHOW91_API_URL', 'http://www.show91.com').'/oauth/setTop', // 获取代练类型
    	],
    ],

    // 丸子平台
    'wanzi' => [
    	'appid'     => '8FK2tEkFoHcB1UJu6TkSzLAwkh9e2WIwunbEA2lyFnJhpupzRelEuxjG34YX',
		'appsecret' => '8mCSr0H3EbTIatauflbqzUIkKdZAQiFsAAF3Nu4Jp2nMCGZSBA6mSyF7E5J1',
		'aes_key'   => '4158d685d8e4f5e8',
		'aes_iv'    => '1234567891111152',
		'account'   => env('WANZI_ACCOUNT', '51683C315D36488FB266904B6FD4BDFF'),
		'sign'      => env('WANZI_SIGN','8469245010c0515a5c451bd20cd59cab'),
		'password'  => env('WANZI_PAY_PASSWORD', 'qqq111'),
		'status' => [
	        0  => '已发布',
	        1  => '代练中',
	        2  => '待验收',
	        3  => '待结算',
	        4  => '已结算',
	        5  => '已挂起',
	        6  => '已撤单',
	        7  => '已取消',
	        8  => '已锁定',
	        10 => '等待工作室接单',
	        11 => '等待玩家付款',
	        12 => '玩家超时未付款',
    	],
    	'url' => [
			'onSale'                   => env('WANZI_API_URL', 'http://10.0.1.51:8080').'/oauth/grounding', // 上架
			'offSale'                  => env('WANZI_API_URL', 'http://10.0.1.51:8080').'/oauth/grounding', // 下架
			'applyRevoke'              => env('WANZI_API_URL', 'http://10.0.1.51:8080').'/oauth/addCancelOrder', // 申请撤销
			'cancelRevoke'             => env('WANZI_API_URL', 'http://10.0.1.51:8080').'/oauth/cancelSc', // 取消撤销
			'agreeRevoke'              => env('WANZI_API_URL', 'http://10.0.1.51:8080').'/oauth/confirmSc', // 同意撤销
			'applyArbitration'         => env('WANZI_API_URL', 'http://10.0.1.51:8080').'/oauth/addappeal', // 申请仲裁
			'cancelArbitration'        => env('WANZI_API_URL', 'http://10.0.1.51:8080').'/oauth/cancelAppeal', // 取消仲裁
			'complete'                 => env('WANZI_API_URL', 'http://10.0.1.51:8080').'/oauth/accept', // 订单完成
			'lock'                     => '', // 锁定
			'cancelLock'               => '', // 取消锁定
			'delete'                   => env('WANZI_API_URL', 'http://10.0.1.51:8080').'/oauth/chedan', // 删除订单
			'updateOrder'              => env('WANZI_API_URL', 'http://10.0.1.51:8080').'/oauth/qs/updateOrder', // 修改订单
			'addTime'                  => env('WANZI_API_URL', 'http://10.0.1.51:8080').'/oauth/addLimitTime3', // 加时
			'addMoney'                 => env('WANZI_API_URL', 'http://10.0.1.51:8080').'/oauth/addPrice2', // 加款
			'orderDetail'              => env('WANZI_API_URL', 'http://10.0.1.51:8080').'/oauth/orderDetail', // 订单详情
			'getScreenshot'            => env('WANZI_API_URL', 'http://10.0.1.51:8080').'/oauth/topic', // 订单截图
			'getMessage'               => env('WANZI_API_URL', 'http://10.0.1.51:8080').'/oauth/messageList', //获取留言
			'replyMessage'             => env('WANZI_API_URL', 'http://10.0.1.51:8080').'/oauth/addMess', // 回复留言addMess
			'updateAccountAndPassword' => env('WANZI_API_URL', 'http://10.0.1.51:8080').'/oauth/editOrderAccPwd', // 修改账号密码
			'refuseRevoke'             => env('WANZI_API_URL', 'http://10.0.1.51:8080').'/oauth/confirmSc', // 不同意撤销
			'getPlays'				   => env('WANZI_API_URL', 'http://10.0.1.51:8080').'/oauth/getPlays', // 获取代练类型
			'setTop'				   => env('WANZI_API_URL', 'http://10.0.1.51:8080').'/oauth/setTop', // 获取代练类型
			'appeals'				   => env('WANZI_API_URL', 'http://10.0.1.51:8080').'/oauth/appeals', // 仲裁列表
			'addevidence'              => env('WANZI_API_URL', 'http://10.0.1.51:8080').'/oauth/addevidence', // 添加仲裁证据
			'seeappeal'                => env('WANZI_API_URL', 'http://10.0.1.51:8080').'/oauth/seeappeal', // 查看仲裁详情
    	],
    ],
];
<?php
return [
    // 临时数据保存时间
    'timeout' => '36000',
    // 订单相关
    'order' => [
        // 订单分配开关
        'orderAssignSwitch' => 'order:AssignSwitch',
        // 记录某个旺旺号的订单分配到的了那商户
        'wangWangToUserId' => 'order:wangWangToUserId:',
        // 记录某个旺旺号的订单分配到的了那商户，有效时间 30 分钟
        'wangWangToUserIdRecordTime' => 1800,
        // 订单号自增数量
        'quantity' => 'order:quantity:',
        // 待接单哈希表
        'waitReceiving' => 'order:wait:receiving',
        // 代练留言哈希表
        'levelingMessage' => 'order:leveling:message',
        // 代练留言数量统计
        'levelingMessageCount' => 'order:leveling:message:count:',
        // 接单用户队列  生成的key示例 order:receiving:20170800383838383(队列中存的是点了接单的用户ID)
        'receiving' => 'order:receiving:',
        // 接单记录 生成的key示例：order:receiving:20170800383838383123(123是用户ID：主账号)
        'receivingRecord' => 'order:receiving:record:',
        // 待接单数量
        'waitReceivingQuantity' => 'order:wait:receiving:quantity',
        // 待处理订单数量
        'waitHandleQuantity' => 'order:wait:handle:quantity:',
        // 待确认收货的订单
        'waitConfirm' => 'order:wait:confirm',
        // 数据类型队列，用与检测 是否可以进行下一次订单分配
        'assignStatus' => 'order:assign:status',
        // 自动下架订单
        'autoUnShelve' => 'order:autoUnShelve',
        // 订单角标
        'statusCount' => 'order:statusCount:',
        //  房卡充值队列
        'roomCardRecharge' => 'order:roomCardRecharge',
    ],
    // 用户模型
    'user' => [
        // 获取主账号ID 生成key示例 user:get:primary:id:1
        'getPrimaryId' => 'user:get:primary:id:',
        // users登录账号 sessionid
        'loginSession' => 'users:id:',
        // admin_users 登录账号 sessionid
        'adminLoginSession' => 'admin_users:id:',
        // 获取用户设置 生成key示例 user:setting:id:1
        'setting' => 'user:setting:id:'
    ],
    // 自有天猫订单token
    'tmallStoreToken' => 'tmallStoreToken:',
    // 淘宝授权token 缓存
    'taobaoAccessToken' => 'taobaoAccessToken:'
];
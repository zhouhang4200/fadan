<?php
return [
    // 临时数据保存时间
    'timeout' => '36000',
    // 订单相关
    'order' => [
        // 订单号自增数量
        'quantity' => 'order:quantity:',
        // 待接单哈希表
        'waitReceiving' => 'order:wait:receiving',
        // 接单用户队列  生成的key示例 order:receiving:20170800383838383(队列中存的是点了接单的用户ID)
        'receiving' => 'order:receiving:',
        // 接单记录 生成的key示例：order:receiving:20170800383838383123(123是用户ID：主账号)
        'receivingRecord' => 'order:receiving:record:',
    ],
    // 用户模型
    'user' => [
        // 获取主账号ID 生成key示例 user:get:primary:id:1
        'getPrimaryId' => 'user:get:primary:id:'
    ]
];
<?php
return [
    // 临时数据保存时间
    'timeout' => '36000',
    // 订单相关
    'order' => [
        // 订单号自增数量
        'quantity' => 'order:quantity:',
        // 接单 生成的key示例 order:grab:20170800383838383
        'receiving' => 'order:receiving:',
        // 接单记录 生成的key示例：order:receiving:20170800383838383123(123是商户ID)
        'receivingRecord' => 'order:receivingRecord:'
    ]
];
<?php

use App\Extensions\Order\ForeignOrder\JdForeignOrder;
use App\Extensions\Order\ForeignOrder\TmallForeignOrder;
use App\Extensions\Order\ForeignOrder\KamenForeignOrder;

// 订单
return [
    'status' => [
        1 => '已创建',
        2 => '停止抢单，分配中',
        3 => '已接单',
        4 => '已发货',
        5 => '发货失败',
        6 => '售后中',
        7 => '售后完成',
    ],

    'operation_type' => [
        1 => '创建',
        2 => '关闭抢单',
        3 => '接单',
        4 => '发货',
        5 => '设置失败',
        6 => '申请售后',
        7 => '完成售后',
        8 => '接单后，转回集市'
    ],

    'source' => [
        1 => '手工',
        2 => '淘宝',
        3 => '天猫',
        4 => '京东',
    ],
    
    // 外部订单
    'parsers' => [
        'jd' => JdForeignOrder::class,
        'tmall' => TmallForeignOrder::Class,
        'kamen' => KamenForeignOrder::Class,
    ],
];

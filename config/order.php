<?php

use App\Extensions\Order\ForeignOrder\JdForeignOrder;
use App\Extensions\Order\ForeignOrder\TmallForeignOrder;
use App\Extensions\Order\ForeignOrder\KamenForeignOrder;

// 订单
return [
    'status' => [
        1 => '等待商户接单',
        2 => '系统分配中',
        3 => '商户已接单',
        4 => '已发货',
        5 => '已失败',
        6 => '售后中',
        7 => '售后完成',
        8 => '订单完成',
        9 => '已接单,待分配', // 临时状态不存表
        10 => '已取消',
        11 => '未付款',
    ],

    'operation_type' => [
        1  => '创建',
        2  => '关闭抢单',
        3  => '接单',
        4  => '发货',
        5  => '设置失败',
        6  => '申请售后',
        7  => '完成售后',
        8  => '接单后，转回集市',
        9  => '设置完成',
        10 => '取消订单',
        11 => '支付订单',
    ],

    'source' => [
        1 => '手工',
        2 => '淘宝',
        3 => '天猫',
        4 => '京东',
        5 => '福禄APP',
    ],

    // 外部订单
    'parsers' => [
        'jd' => JdForeignOrder::class,
        'tmall' => TmallForeignOrder::Class,
        'kamen' => KamenForeignOrder::Class,
    ],

    // 订单分配下限 (最少接单人数)
    'assignLowerLimit' => 1,

    // api 自动下单风控值
    'apiRiskRate' => '0.98',

    // 从接单到发货给予的最大时间，单位秒
    'max_use_time' => 2400,
];

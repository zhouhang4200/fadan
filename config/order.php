<?php

use App\Extensions\Order\ForeignOrder\JdForeignOrder;
use App\Extensions\Order\ForeignOrder\TmallForeignOrder;
use App\Extensions\Order\ForeignOrder\KamenForeignOrder;
use App\Extensions\Dailian\Controllers\Complete;
use App\Extensions\Dailian\Controllers\NoReceive;
use App\Extensions\Dailian\Controllers\OffSaled;
use App\Extensions\Dailian\Controllers\Lock;
use App\Extensions\Dailian\Controllers\UnLock;
use App\Extensions\Dailian\Controllers\Revoking;
use App\Extensions\Dailian\Controllers\UnRevoke;
use App\Extensions\Dailian\Controllers\Revoked;
use App\Extensions\Dailian\Controllers\Arbitrationing;
use App\Extensions\Dailian\Controllers\Arbitrationed;
use App\Extensions\Dailian\Controllers\Delete;
use App\Extensions\Dailian\Controllers\ForceRevoke;
use App\Extensions\Dailian\Controllers\CancelArbitration;

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
        // 代练
        12 => '未接单',
        13 => '代练中',
        14 => '待验收',
        15 => '撤销中',
        16 => '仲裁中',
        17 => '异常',
        18 => '锁定',
        19 => '已撤销',
        20 => '已结算',
        21 => '已仲裁',
        22 => '已下架',
        23 => '强制撤销',
        24 => '已删除',
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
        // 代练
        12 => '完成',
        13 => '重发',
        14 => '上架',
        15 => '下架',
        16 => '锁定',
        17 => '取消锁定',
        18 => '撤销',
        19 => '取消撤销',
        20 => '申请仲裁',
        21 => '取消仲裁',
        22 => '编辑',
        23 => '删除',
        24 => '同意撤销',
        25 => '强制撤销',
        26 => '客服仲裁',
    ],

    'source' => [
        1 => '人工',
        2 => '淘宝',
        3 => '天猫',
        4 => '京东',
        5 => '福禄APP',
        6 => '福禄API',
    ],

    // 后台售后申请退款状态
    'after_service' => [
        1 => '待审核',
        2 => '审核通过',
        3 => '审核拒绝',
        4 => '完成退款',
    ],

    // 外部订单
    'parsers' => [
        'jd' => JdForeignOrder::class,
        'tmall' => TmallForeignOrder::Class,
        'kamen' => KamenForeignOrder::Class,
    ],

    // 代练
    'dailians' => [
        'complete' => Complete::class, // 完成 -> 已结算
        'onSale' => NoReceive::class, // 上架 ->未接单
        'offSale' => OffSaled::class, // 下架 -> 已下架
        'lock' => Lock::class, // 锁定 -> 锁定
        'cancelLock' => UnLock::class, // 取消锁定 -> 锁定前状态
        'revoke' => Revoking::class, // 撤销 -> 撤销中
        'cancelRevoke' => UnRevoke::class, // 取消撤销 -> 撤销前的状态
        'agreeRevoke' => Revoked::class, // 同意撤销 -> 已撤销
        'applayArbitration' => Arbitrationing::class, // 申请仲裁 -》 仲裁中
        'cancelArbitration' => CancelArbitration::class, // 取消仲裁 -》 仲裁申请前状态
        'delete' => Delete::class, // 删除 -》 删除
        'forceRevoke' => ForceRevoke::class, // 外面接口传的 强制撤销操作 -》 强制撤销
        'arbitration' => Arbitrationed::class, // 客服仲裁 -》 已仲裁
    ],

    // 订单分配下限 (最少接单人数)
    'assignLowerLimit' => 1,

    // api 自动下单风控值
    'apiRiskRate' => '0.98',

    // 用于计算订单是否超时。 单位秒
    'max_use_time' => 2400,
];

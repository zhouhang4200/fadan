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
use App\Extensions\Dailian\Controllers\Playing;
use App\Extensions\Dailian\Controllers\ApplyComplete;
use App\Extensions\Dailian\Controllers\CancelComplete;
use App\Extensions\Dailian\Controllers\Abnormal;
use App\Extensions\Dailian\Controllers\CancelAbnormal;
use App\Extensions\Dailian\Controllers\RefuseRevoke;

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
    // 代练状态
    'status_leveling' => [
        0  => '全部', // 无用
        1  => '未接单',
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
        // 代练操作
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
        27 => '接单',
        28 => '申请完成',
        29 => '取消验收',
        30 => '异常',
        31 => '取消异常',
    ],

    'source' => [
        1 => '人工',
        2 => '淘宝',
        3 => '天猫',
        4 => '京东',
        5 => '福禄APP',
        6 => '福禄API',
    ],

    // 外部订单
    'parsers' => [
        'jd'    => JdForeignOrder::class,
        'tmall' => TmallForeignOrder::Class,
        'kamen' => KamenForeignOrder::Class,
    ],

    // 代练
    'dailians' => [
        'onSale'            => NoReceive::class, // 上架 
        'offSale'           => OffSaled::class, // 下架 
        'receive'           => Playing::class, // 接单
        'lock'              => Lock::class, // 锁定 
        'cancelLock'        => UnLock::class, // 取消锁定 
        'revoke'            => Revoking::class, // 申请撤销 
        'cancelRevoke'      => UnRevoke::class, // 取消撤销 
        'agreeRevoke'       => Revoked::class, // 同意撤销 
        'forceRevoke'       => ForceRevoke::class, // 强制撤销 
        'refuseRevoke'      => RefuseRevoke::class, // 不同意撤销
        'applyArbitration'  => Arbitrationing::class, // 申请仲裁 
        'cancelArbitration' => CancelArbitration::class, // 取消仲裁 
        'arbitration'       => Arbitrationed::class, // 同意仲裁 
        'delete'            => Delete::class, // 删除
        'applyComplete'     => ApplyComplete::class, // 申请验收 
        'cancelComplete'    => CancelComplete::class, // 取消验收 
        'abnormal'          => Abnormal::class, // 异常 
        'cancelAbnormal'    => CancelAbnormal::class, // 取消异常
        'complete'          => Complete::class, // 完成 
    ],

    // 91平台订单状态
    'show91' => [
        0  => "已发布",
        1  => "代练中",
        2  => "待验收",
        3  => "待结算",
        4  => "已结算",
        5  => "已挂起",
        6  => "已撤单",
        7  => "已取消",
        10 => "等待工作室接单",
        11 => "等待玩家付款",
        12 => "玩家超时未付款",
        13 => '协商中', // 他们没有此状态，自己加的
        14 => '仲裁中', // 他们没有此状态，自己加的
        15 => '协商/仲裁中', // 他们没有此状态，自己加的
    ],

    // 外部平台
    'third' => [
        1 => '91平台',
        2 => '代练妈妈',
        3 => '代练通',
        4 => '易代练',
    ],
    
    // 91平台代练类型
    'show91_plays' => [
        1 => '排位',
        3 => '陪玩',
        5 => '金币',
        7 => '成就',
    ],
    // 订单分配下限 (最少接单人数)
    'assignLowerLimit' => 1,

    // api 自动下单风控值
    'apiRiskRate' => '0.98',

    // 用于计算订单是否超时。 单位秒
    'max_use_time' => 2400,

    // 订单充值状态
    'order_recharge_status' => [
        1 => '充值中',
        2 => '充值完成',
        // 3 => '没想好',
    ],
];

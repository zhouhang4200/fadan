<?php

namespace App\Http\Controllers\Api\Partner;


use App\Http\Controllers\Controller;
use App\Models\GameLevelingOrder;
use App\Models\GameLevelingPlatform;
use App\Repositories\Frontend\OrderRepository;

/**
 * 订单操作分发
 * 兼容旧订单操作
 * Class OperationController
 * @package App\Http\Controllers\Api\Partner
 */
class OperationDistributeController extends Controller
{
    public $type;

    /**
     * OperationController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        if ($order = GameLevelingPlatform::where('platform_trade_no', request('order_no'))->first()) {
            $this->type = 'new';
        } else {
            $this->type = 'old';
        }
    }

    public function query()
    {
        if ($this->type == 'new') {
            return (new GameLevelingOrderOperateController())->query(request());
        } else {
            $orderRepository = new OrderRepository();
            return (new OrderController())->query(request(), $orderRepository);
        }
    }

    // 接单
    public function receive()
    {
        if ($this->type == 'new') {
            return (new GameLevelingOrderOperateController())->take(request());
        } else {
            $orderRepository = new OrderRepository();
            return (new OrderController())->receive(request(), $orderRepository);
        }
    }

    // 申请验收
    public function applyComplete()
    {
        if ($this->type == 'new') {
            return (new GameLevelingOrderOperateController())->applyComplete(request());
        } else {
            return (new OrderController())->applyComplete(request());
        }
    }

    // 取消验收
    public function cancelComplete()
    {
        if ($this->type == 'new') {
            return (new GameLevelingOrderOperateController())->cancelComplete(request());
        } else {
            return (new OrderController())->cancelComplete(request());
        }
    }

    // 申请撤销
    public function revoke()
    {
        if ($this->type == 'new') {
            return (new GameLevelingOrderOperateController())->applyConsult(request());
        } else {
            return (new OrderController())->revoke(request());
        }
    }

    // 取消撤销
    public function cancelRevoke()
    {
        if ($this->type == 'new') {
            return (new GameLevelingOrderOperateController())->cancelConsult(request());
        } else {
            return (new OrderController())->cancelRevoke(request());
        }
    }

    // 不同意撤销
    public function refuseRevoke()
    {
        if ($this->type == 'new') {
            return (new GameLevelingOrderOperateController())->rejectConsult(request());
        } else {
            return (new OrderController())->refuseRevoke(request());
        }
    }

    // 同意撤销
    public function agreeRevoke()
    {
        if ($this->type == 'new') {
            return (new GameLevelingOrderOperateController())->agreeConsult(request());
        } else {
            return (new OrderController())->agreeRevoke(request());
        }
    }

    // 强制撤销
    public function forceRevoke()
    {
        if ($this->type == 'new') {
            return (new GameLevelingOrderOperateController())->forceDelete(request());
        } else {
            return (new OrderController())->forceRevoke(request());
        }
    }

    // 申请仲裁
    public function applyArbitration()
    {
        if ($this->type == 'new') {
            return (new GameLevelingOrderOperateController())->applyComplain(request());
        } else {
            return (new OrderController())->applyArbitration(request());
        }
    }

    // 取消仲裁
    public function cancelArbitration()
    {
        if ($this->type == 'new') {
            return (new GameLevelingOrderOperateController())->cancelComplain(request());
        } else {
            return (new OrderController())->cancelArbitration(request());
        }
    }

    // 强制仲裁
    public function forceArbitration()
    {
        if ($this->type == 'new') {
            return (new GameLevelingOrderOperateController())->arbitration(request());
        } else {
            return (new OrderController())->forceArbitration(request());
        }
    }

    // 异常
    public function abnormal()
    {
        if ($this->type == 'new') {
            return (new GameLevelingOrderOperateController())->anomaly(request());
        } else {
            return (new OrderController())->abnormal(request());
        }
    }

    // 取消异常
    public function cancelAbnormal()
    {
        if ($this->type == 'new') {
            return (new GameLevelingOrderOperateController())->cancelAnomaly(request());
        } else {
            return (new OrderController())->cancelAbnormal(request());
        }
    }

    // 回传
    public function callback()
    {
        if (GameLevelingOrder::where('trade_no', request('no'))->first()) {
            return (new GameLevelingOrderOperateController())->callback(request());
        } else {
            return (new OrderController())->callback(request());
        }
    }

    // 完成
    public function complete()
    {
        if ($this->type == 'new') {
            return (new GameLevelingOrderOperateController())->complete(request());
        } else {
            return (new OrderController())->complete(request());
        }
    }

    // 新留言通知接口
    public function newMessage()
    {
        if ($this->type == 'new') {
            return (new OrderController())->newMessage(request());
        }
    }
}
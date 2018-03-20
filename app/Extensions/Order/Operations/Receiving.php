<?php
namespace App\Extensions\Order\Operations;

use App\Events\NotificationEvent;
use App\Exceptions\CustomException;
use App\Exceptions\OrderException as Exception;
use App\Models\User;
use App\Models\Weight;
use App\Repositories\Frontend\OrderDetailRepository;
use App\Services\FuluAppApi;

// 接单
class Receiving extends \App\Extensions\Order\Operations\Base\Operation
{
    protected $acceptableStatus = [2];
    protected $handledStatus    = 3;
    protected $type             = 3;

    /**
     * @param string $orderNo 订单编号
     * @param int $userId 接单人id
     */
    public function __construct($orderNo, $userId)
    {
        $this->orderNo = $orderNo;
        $this->userId  = $userId;
    }

    public function setAttributes()
    {
        $user = User::find($this->userId);
        if (empty($user)) {
            throw new Exception('不存在的接单人');
        }

        $this->order->gainer_user_id         = $this->userId;
        $this->order->gainer_primary_user_id = $user->getPrimaryUserId();
    }

    public function setDescription()
    {
        $this->description = "订单已分配到用户[{$this->userId}]";
    }

    public function saveWeight()
    {
        $weight = new Weight;
        $weight->order_no                = $this->order->no;
        $weight->order_money             = $this->order->amount;
        $weight->creator_user_id         = $this->order->creator_user_id;
        $weight->creator_primary_user_id = $this->order->creator_primary_user_id;
        $weight->order_time              = $this->order->created_at;
        $weight->gainer_user_id          = $this->order->gainer_user_id;
        $weight->gainer_primary_user_id  = $this->order->gainer_primary_user_id;
        $weight->order_in_time           = $this->order->updated_at;

        if (!$weight->save()) {
            throw new Exception('权重凭证保存失败');
        }
        $this->runAfter = true;
    }

    /**
     * 后置操作
     */
    public function after()
    {
        if ($this->runAfter) {
            waitReceivingDel($this->order->no);
            // 待接单数量减1
            waitReceivingQuantitySub();
            // 待处理订单数加1
            waitHandleQuantityAdd($this->order->gainer_user_id);
            // 向前台发送 待接单数量
            event(new NotificationEvent('MarketOrderQuantity', ['quantity' => marketOrderQuantity()]));
            // 向前台发送 待处理订单数量
            event(new NotificationEvent('waitHandleQuantity', ['quantity' => waitHandleQuantity($this->order->gainer_user_id)]));

            // 删除接单
            try {
                receivingUserDel($this->order->no);
            } catch (CustomException $exception) {
                \Log::alert($exception->getMessage() . ' 删除接单队列');
            }

            // 更新订单详情接单商户ID
            try {
                OrderDetailRepository::updateGainerPrimaryUserIdBy($this->order->no, $this->order->gainer_primary_user_id);
            } catch (CustomException $exception) {
                \Log::alert($exception->getMessage() . ' 更新接单人异常');
            }

            // 如果是王者皮肤订单者并是APP订单则发送QQ号
            if ($this->order->game_id == 21 && $this->order->creator_primary_user_id == 8111) {
                try {
                    FuluAppApi::sendOrderAndQq($this->order->gainer_primary_user_id, $this->order->foreign_order_no);
                } catch(CustomException $exception) {
                    \Log::alert($exception->getMessage() . '给福禄APP发送QQ号异常，单号：' . $this->order->no);
                }
            }

            if ($this->order->game_id == 21 && in_array($this->order->creator_primary_user_id, [8311, 8111])) {
                // 发送短信
                try {
                    $userSet = User::where(['id' => $this->order->gainer_primary_user_id])->first();
                    $userSetArr = $userSet->getUserSetting();

                    $contact = '';

                    $detail = $this->order->detail->pluck('field_value', 'field_name');

                    if (strtolower($detail['version']) == 'qq') {
                        $contact = 'QQ: ' . $userSetArr['skin_trade_qq'];
                    } else {
                        $contact = '微信: ' . $userSetArr['skin_trade_wx'];
                    }

                    $content = '皮肤订单请添加客服 ' .  $contact  .' 添加后按照客服指引进行操作完成交易，不加客服将无法获得皮肤。';

                    $result = sendSms($this->order->creator_primary_user_id,  $this->order->no, $detail['client_qq'], $content, '皮肤交易短信费');

                } catch(CustomException $exception) {
                    myLog('send-message', [$exception->getMessage() . '给用户发送QQ号异常，单号：' . $this->order->no]);
                } catch(\ErrorException $exception) {
                    myLog('send-message', [$exception->getMessage() . '给用户发送QQ号异常，单号：' . $this->order->no]);
                }
            }
        }
    }
}

<?php

namespace App\Extensions\Dailian\Controllers;

use App\Events\OrderFinish;
use App\Events\OrderReceiving;
use DB;
use Asset;
use Carbon\Carbon;
use App\Models\User;
use App\Models\UserAsset;
use App\Models\OrderDetail;
use App\Extensions\Asset\Expend;
use App\Exceptions\DailianException;
use Psy\Exception\ErrorException;

/**
 * 接单操作
 * @package App\Extensions\Dailian\Controllers
 */
class Playing extends DailianAbstract implements DailianInterface
{
    protected $acceptableStatus = [1]; // 状态：未接单
    protected $beforeHandleStatus; // 操作之前的状态:
    protected $handledStatus    = 13; // 状态：代练中
    protected $type             = 27; // 操作：接单

	/**
     * 
     * @param  [type] $orderNo     [订单号]
     * @param  [type] $userId      [操作人]
     * @param  [type] $apiAmount   [回传代练费/安全保证金]
     * @param  [type] $apiDeposit  [回传双金/ 效率保证金]
     * @param  [type] $apiService  [回传代练手续费]
     * @param  [type] $writeAmount [协商代练费]
     * @return [type]              [true or exception]
     */
    public function run($orderNo, $userId, $runAfter = 1)
    {	
    	DB::beginTransaction();
    	try {
    		// 赋值
    		$this->orderNo = $orderNo;
        	$this->userId  = $userId;
            $this->runAfter = $runAfter;
            // 获取订单对象
            $this->getObject();
        	$this->beforeHandleStatus = $this->getOrder()->status;
		    // 创建操作前的订单日志详情
		    $this->createLogObject();
		    // 设置订单属性
		    $this->setAttributes();
		    // 保存更改状态后的订单
		    $this->save();
		    // 更新平台资产
		    $this->updateAsset();
		    // 订单日志描述
		    $this->setDescription();
		    // 保存操作日志
		    $this->saveLog();
            $this->after();
            $this->orderCount();
            // 删除状态不在 申请验收 的redis 订单
            delRedisCompleteOrders($this->orderNo);

    	} catch (DailianException $e) {
    		DB::rollBack();
            throw new DailianException($e->getMessage());
    	}
    	DB::commit();

        return true;
    }

     // 保存更改状态后的订单
    public function save()
    {
        $this->order->status = $this->handledStatus;
        $this->order->gainer_user_id = $this->userId;
        $this->order->gainer_primary_user_id = User::find($this->userId)->getPrimaryUserId();

        if (!$this->order->save()) {
            throw new DailianException('订单操作失败');
        }

        return $this->order;
    }

    /**
     * [接单支出安全和效率保证金]
     * @return [type] [description]
     */
    public function updateAsset()
    {
        DB::beginTransaction();
        try {
            $orderDetails = OrderDetail::where('order_no', $this->order->no)
                    ->pluck('field_value', 'field_name')
                    ->toArray();

            $safePayment = $orderDetails['security_deposit'];
            $effectPayment = $orderDetails['efficiency_deposit'];
            // 检测接单账号余额
            $this->checkGainerMoney($safePayment, $effectPayment);
            if ($safePayment > 0) {                      
                // 接单 安全保证金支出
                Asset::handle(new Expend($safePayment, 4, $this->order->no, '安全保证金支出', $this->order->gainer_primary_user_id));

                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                    throw new DailianException('流水记录写入失败');
                }

                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                    throw new DailianException('流水记录写入失败');
                }
            }

            if ($effectPayment > 0) {                      
                // 接单 效率保证金支出
                Asset::handle(new Expend($effectPayment, 5, $this->order->no, '效率保证金支出', $this->order->gainer_primary_user_id));

                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                    throw new DailianException('流水记录写入失败');
                }

                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                    throw new DailianException('流水记录写入失败');
                }
            }
        } catch (DailianException $e) {
            DB::rollback();
            throw new DailianException($e->getMessage());
        }
        DB::commit();
    }

    /**
     * 检车接单账户余额
     * @return [type] [description]
     */
    public function checkGainerMoney($safePayment, $effectPayment)
    {
        // 接单商户余额
        $leftAmount = UserAsset::where('user_id', $this->order->gainer_primary_user_id)->value('balance');

        $doublePayment = bcadd($safePayment, $effectPayment);

        if (! $leftAmount || $leftAmount < $doublePayment) {
            throw new DailianException('商户余额不足');
        }
    }

    public function after()
    {
        if ($this->runAfter) {
            $now = Carbon::now()->toDateTimeString();

            OrderDetail::where('order_no', $this->order->no)
                ->where('field_name', 'receiving_time')
                ->update(['field_value' => $now]);

            try {
                event(new OrderReceiving($this->order));
            } catch (ErrorException $errorException) {
                myLog('receiving', [$errorException->getMessage()]);
            } catch (\Exception $exception) {
                myLog('receiving', [$exception->getMessage()]);
            }
        }
    }
}

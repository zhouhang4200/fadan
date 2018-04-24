<?php

namespace App\Extensions\Dailian\Controllers;

use App\Exceptions\AssetException;
use App\Exceptions\RequestTimeoutException;
use DB;
use Asset;
use App\Extensions\Asset\Income;
use App\Models\OrderDetail;
use App\Exceptions\DailianException; 
use App\Repositories\Frontend\OrderDetailRepository;

/**
 * 强制撤销操作
 */
class ForceRevoke extends DailianAbstract implements DailianInterface
{
    protected $acceptableStatus = [13, 14, 15, 16, 17, 18];
	protected $beforeHandleStatus; 
    protected $handledStatus    = 23; 
    protected $type             = 25;

	/**
     * [run 回传的 强制撤销操作 -》 强制撤销]
     * @param  [type] $orderNo     [订单号]
     * @param  [type] $userId      [操作人]
     * @param  [type] $apiAmount   [回传代练费]
     * @param  [type] $apiDeposit  [回传双金]
     * @param  [type] $apiService  [回传代练手续费]
     * @param  [type] $writeAmount [协商代练费]
     * @return [type]              [true or exception]
     */
    public function run($orderNo, $userId)
    {	
    	DB::beginTransaction();
    	try {
    		// 赋值
    		$this->orderNo = $orderNo;
        	$this->userId  = $userId;
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
            $this->orderCount();
            delRedisCompleteOrders($this->orderNo);
            // 从留言获取任务中删除
            levelingMessageDel($this->orderNo);
    	} catch (DailianException $e) {
    		DB::rollBack();
            throw new DailianException($e->getMessage());
    	} catch (AssetException $exception) {
            // 资金异常
            throw new DailianException($exception->getMessage());
        } catch (RequestTimeoutException $exception) {
            // 如果出现返回空值则写入报警。并标记为异常
            throw new DailianException($exception->getMessage());
        }
    	DB::commit();
        return true;
    }

    /**
     * [退代练费给发单，退双金给接单]
     * @throws DailianException
     */
    public function updateAsset()
    {

        $orderDetails = OrderDetail::where('order_no', $this->order->no)
                ->pluck('field_value', 'field_name')
                ->toArray();

        // 发单 退回代练费
        Asset::handle(new Income($this->order->amount, 7, $this->order->no, '退回代练费', $this->order->creator_primary_user_id));

        if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
            throw new DailianException('流水记录写入失败');
        }

        if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
            throw new DailianException('流水记录写入失败');
        }

        if ($orderDetails['security_deposit']) {
            // 接单 退回安全保证金
            Asset::handle(new Income($orderDetails['security_deposit'], 8, $this->order->no, '安全保证金退回', $this->order->gainer_primary_user_id));

            if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                throw new DailianException('流水记录写入失败');
            }

            if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                throw new DailianException('流水记录写入失败');
            }
        }

        if ($orderDetails['efficiency_deposit']) {
            // 接单 退效率保证金
            Asset::handle(new Income($orderDetails['efficiency_deposit'], 9, $this->order->no, '效率保证金退回', $this->order->gainer_primary_user_id));

            if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                throw new DailianException('流水记录写入失败');
            }

            if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                throw new DailianException('流水记录写入失败');
            }
        }
        // 写入结算时间
        OrderDetailRepository::updateByOrderNo($this->orderNo, 'checkout_time', date('Y-m-d H:i:s'));
    }
}

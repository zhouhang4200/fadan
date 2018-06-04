<?php

namespace App\Extensions\Dailian\Controllers;

use App\Models\AutomaticallyGrabGoods;
use App\Models\TaobaoTrade;
use DB;
use Redis;
use Exception;
use Carbon\Carbon;
use App\Exceptions\DailianException;
use App\Events\OrderApplyComplete;
use App\Repositories\Frontend\OrderDetailRepository;

/**
 * 申请验收操作
 */
class ApplyComplete extends DailianAbstract implements DailianInterface
{
	protected $acceptableStatus = [13]; // 状态：13代练中
	protected $beforeHandleStatus; // 操作之前的状态:14待验收
    protected $handledStatus    = 14; // 状态：14 待验收
    protected $type             = 28; // 操作：28申请验收
    
	/**
     * [run 完成 -> 已结算]
     * @param  [type] $orderNo     [订单号]
     * @param  [type] $userId      [操作人]
     * @param  [type] $apiAmount   [回传代练费]
     * @param  [type] $apiDeposit  [回传双金]
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
            // redis 存提交验收时间，到期自动完成
            $this->after();
            $this->orderCount();
            // 写入提验时间
            OrderDetailRepository::updateByOrderNo($this->orderNo, 'check_time', date('Y-m-d H:i:s'));
            // 操作成功，删除redis里面以前存在的订单报警
            $this->deleteOperateSuccessOrderFromRedis($this->orderNo);
            $this->runEvent();
        } catch (DailianException $exception) {
            DB::rollBack();
            myLog('opt-ex',  ['操作' => '申请验收',  '订单号' => $this->orderNo, 'user' => $this->userId, $exception->getFile(), $exception->getLine(), $exception->getMessage()]);
            throw new DailianException($exception->getMessage());
    	} catch (Exception $exception) {
            DB::rollBack();
            myLog('opt-ex',  ['操作' => '申请验收', '订单号' => $this->orderNo, 'user' => $this->userId, $exception->getFile(), $exception->getLine(), $exception->getMessage()]);
            throw new DailianException('订单异常');
        }
    	DB::commit();
    	// 返回
        return true;
    }


    public function after()
    {
        if ($this->runAfter) {
            // 申请验收之后redis写入记录
            $now = Carbon::now()->toDateTimeString();
            $key = $this->orderNo;
            Redis::hSet('complete_orders', $key, $now);

            $this->taobaoAutoDelivery();

            // 调用事件
            try {
                event(new OrderApplyComplete($this->order));
            } catch (Exception $errorException) {
                myLog('ex', ['申请验收', $errorException->getMessage()]);
            }
        }
    }

    /**
     * 设置了交易自动发货
     */
    public function taobaoAutoDelivery()
    {
        try {
            // 获取淘宝单号
            $taobaoTrade = TaobaoTrade::where('tid', $this->orderDetail['source_order_no'])->first();

            if ($taobaoTrade) {
                // 获取商品ID
                $goodsConfig = AutomaticallyGrabGoods::where('foreign_goods_id', $taobaoTrade->num_iid)->first();

                // 检测商品ID是否开启了自动发货
                if ($goodsConfig && $goodsConfig->delivery == 1) {
                    taobaoTradeDelivery($this->order->no);
                }
            }
        } catch (\Exception $exception) {
            myLog('apply-complete-error', [$exception->getFile(), $exception->getMessage(), $exception->getLine()]);
        }

    }


}

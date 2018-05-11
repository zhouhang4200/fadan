<?php

namespace App\Extensions\Dailian\Controllers;

use App\Exceptions\RequestTimeoutException;
use DB;
use Redis;
use Exception;
use App\Exceptions\DailianException;

/**
 * 异常操作
 */
class Abnormal extends DailianAbstract implements DailianInterface
{
    protected $acceptableStatus = [13]; // 状态：代练中
	protected $beforeHandleStatus; // 操作之前的状态:
    protected $handledStatus    = 17; // 状态：异常
    protected $type             = 30; // 操作：异常

    /**
     * @param $orderNo [订单号]
     * @param $userId [操作人]
     * @param bool $runAfter
     * @return bool
     * @throws DailianException
     */
    public function run($orderNo, $userId, $runAfter = false)
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
            // 24H自动完成订单
            delRedisCompleteOrders($this->orderNo);
            // 操作成功，删除redis里面以前存在的订单报警
            $this->deleteOperateSuccessOrderFromRedis($this->orderNo);
        } catch (DailianException $exception) {
            DB::rollBack();
            myLog('opt-ex',  ['操作' => '异常', '订单号' => $this->orderNo, 'user' => $this->userId, $exception->getFile(), $exception->getLine(), $exception->getMessage()]);
            throw new DailianException($exception->getMessage());
    	} catch (RequestTimeoutException $exception) {
            DB::rollBack();
            throw new DailianException($exception->getMessage());
        } catch (Exception $exception) {
            DB::rollBack();
            myLog('opt-ex',  ['操作' => '异常', '订单号' => $this->orderNo, 'user' => $this->userId,  $exception->getFile(), $exception->getLine(), $exception->getMessage()]);
            throw new DailianException($exception->getMessage());
        }
    	DB::commit();
        return true;
    }

}

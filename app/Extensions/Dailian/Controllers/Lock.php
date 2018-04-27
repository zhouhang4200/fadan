<?php

namespace App\Extensions\Dailian\Controllers;

use App\Exceptions\RequestTimeoutException;
use DB;
use Exception;
use App\Services\DailianMama;
use App\Exceptions\AssetException;
use App\Exceptions\DailianException;

/**
 * 锁定操作
 */
class Lock extends DailianAbstract implements DailianInterface
{
    protected $acceptableStatus = [13, 14, 17]; // 状态：13,代练中，14待验收，17异常
    protected $beforeHandleStatus; // 操作之前的状态:13,代练中
    protected $handledStatus    = 18; // 状态：18锁定
    protected $type             = 16; // 操作：16锁定

    /**
     * [run 锁定 -> 锁定]
     * @internal param $ [type] $orderNo     [订单号]
     * @internal param $ [type] $userId      [操作人]
     * @internal param $ [type] $apiAmount   [回传代练费]
     * @internal param $ [type] $apiDeposit  [回传双金]
     * @internal param $ [type] $apiService  [回传代练手续费]
     * @internal param $ [type] $writeAmount [协商代练费]
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
            // 申请验收过期自动删除
            delRedisCompleteOrders($this->orderNo);
            // 操作成功，删除redis里面以前存在的订单报警
            $this->deleteOperateSuccessOrderFromRedis($this->orderNo);
    	} catch (DailianException $e) {
    		DB::rollBack();
            throw new DailianException($e->getMessage());
    	} catch (AssetException $exception) {
            throw new DailianException($exception->getMessage());
        } catch (RequestTimeoutException $exception) {
            //  redis报警
            $this->addOperateFailOrderToRedis($this->order, $this->type);
            DB::rollBack();
            throw new DailianException($exception->getMessage());
        } catch (Exception $exception) {
            DB::rollBack();
            myLog('opt-ex',  ['操作' => '锁定', $exception->getFile(), $exception->getLine(), $exception->getMessage()]);
            throw new DailianException('订单异常');
        }
        DB::commit();

        return true;
    }

    /**
     * 调用外部锁定发接口
     * @return bool|void [type] [description]
     * @throws DailianException
     */
    public function after()
    {
        if ($this->runAfter) {

            if (config('leveling.third_orders') && $this->userId != 8456) {
                // 获取订单和订单详情以及仲裁协商信息
                $orderDatas = $this->getOrderAndOrderDetailAndLevelingConsult($this->orderNo);
                // 遍历代练平台
                foreach (config('leveling.third_orders') as $third => $thirdOrderNoName) {
                    // 如果订单详情里面存在某个代练平台的订单号
                    if ($third == $orderDatas['third'] && isset($orderDatas['third_order_no']) && !empty($orderDatas['third_order_no'])) {
                        // 控制器-》方法-》参数
                        call_user_func_array([config('leveling.controller')[$third], config('leveling.action')['lock']], [$orderDatas]);
                    }
                }
            }

            /**
             * 以下只适用于  91  和 代练妈妈
             */
            $orderDetails = $this->checkThirdClientOrder($this->order);

            switch ($orderDetails['third']) {
                case 1:
                    throw new DailianException('该订单被91平台接单，91平台无此操作!');
                    break;
                case 2:
                    // 代练妈妈锁定接口
                    DailianMama::operationOrder($this->order, 20002);
                    break;
            }
            return true;

        }
    }
}

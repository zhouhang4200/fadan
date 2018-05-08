<?php

namespace App\Extensions\Dailian\Controllers;

use App\Exceptions\RequestTimeoutException;
use DB;
use Exception;
use App\Services\Show91;
use App\Models\OrderDetail;
use App\Models\LevelingConsult;
use App\Exceptions\DailianException; 

/**
 * 不同意撤销操作
 */
class RefuseRevoke extends DailianAbstract implements DailianInterface
{
    protected $acceptableStatus = [15]; // 状态：15撤销中
	protected $beforeHandleStatus = 15; // 操作之前的状态:15撤销中
    protected $handledStatus; // 状态：操作之后的状态
    protected $type             = 19; // 操作：19取消撤销

	/**
     * [run 取消撤销 -> 撤销前的状态]
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
            $this->checkIfNeedDo($orderNo);
            // 获取上一个操作状态
            $this->getBeforeStatus($orderNo);
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
            $this->changeConsultStatus();
            $this->after();
            $this->orderCount();
            delRedisCompleteOrders($this->orderNo);
            
            $this->checkIfNeedLock($orderNo, $userId);
        } catch (DailianException $exception) {
            DB::rollBack();
            myLog('opt-ex',  ['操作' => '不同意撤销', '订单号' => $this->orderNo, 'user' => $this->userId, $exception->getFile(), $exception->getLine(), $exception->getMessage()]);
            throw new DailianException($exception->getMessage());
    	} catch (RequestTimeoutException $exception) {
            //  写入redis报警
            $this->addOperateFailOrderToRedis($this->order, $this->type);
            DB::rollBack();
            throw new DailianException($exception->getMessage());
        } catch (Exception $exception) {
            DB::rollBack();
            myLog('opt-ex',  ['操作' => '不同意撤销', '订单号' => $this->orderNo, 'user' => $this->userId, $exception->getFile(), $exception->getLine(), $exception->getMessage()]);
            throw new DailianException('订单异常');
        }
    	DB::commit();

        return true;
    }

    public function checkIfNeedDo($orderNo)
    {
        $datas = $this->getOrderAndOrderDetailAndLevelingConsult($orderNo);

        if (isset($datas) && isset($datas['third']) && in_array($datas['third'], [3])) {
            throw new DailianException('该接单平台没有此操作');
        }
    }
    /**
     * 检查是否需要进行锁定操作
     * @param  [type] $orderNo [description]
     * @param  [type] $userId  [description]
     * @return [type]          [description]
     */
    public function checkIfNeedLock($orderNo, $userId)
    {
        $third = $this->order->detail()->where('field_name', 'third')->value('field_value');

        $orderDetail = OrderDetail::where('order_no', $orderNo)
            ->where('field_name', 'order_previous_status')
            ->first();

        if (! $orderDetail) {
            throw new DailianException('订单前一个状态不存在');
        }

        $previousArr = explode('|', $orderDetail->field_value);

        if (! is_array($previousArr)) {
            throw new DailianException('订单前一个状态数据异常');
        }

        if ($third == 2 && ! in_array(18, $previousArr)) {
            (new Lock)->run($orderNo, $userId);
        }
    }

    /**
     * 获取前一个状态
     * @param $orderNo
     * @throws DailianException
     */
    public function getBeforeStatus($orderNo)
    {
        $orderDetail = OrderDetail::where('order_no', $orderNo)
            ->where('field_name', 'order_previous_status')
            ->first();

        if (! $orderDetail) {
            throw new DailianException('订单前一个状态不存在');
        }

        $previousArr = explode('|', $orderDetail->field_value);

        if (! is_array($previousArr)) {
            throw new DailianException('订单前一个状态数据异常');
        }

        $this->handledStatus = $previousArr[0];
    }

    public function changeConsultStatus()
    {
        LevelingConsult::where('order_no', $this->orderNo)->update(['consult' => 0]);
    }

    /**
     * 调用外部提交协商发接口
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
                    if ($third == $orderDatas['third'] && isset($orderDatas['third_order_no']) && ! empty($orderDatas['third_order_no'])) {
                        // 控制器-》方法-》参数
                        call_user_func_array([config('leveling.controller')[$third], config('leveling.action')['refuseRevoke']], [$orderDatas]);
                    }
                }
            }

            /**
             * 以下只适用于  91  和 代练妈妈
             */
            $orderDetails = $this->checkThirdClientOrder($this->order);

            switch ($orderDetails['third']) {
                case 1:
                    // 91 同意撤销 接口
                    $options = [
                        'oid' => $orderDetails['show91_order_no'],
                        'v' => 2,
                        'p' => config('show91.password'),
                    ];

                    Show91::confirmSc($options);
                    break;
                case 2:
                    throw new DailianException('该订单被代练妈妈平台接单，该平台没有【不同意撤销】操作!');
                    break;
            }
            return true;
        }
    }
}

<?php

namespace App\Extensions\Dailian\Controllers;

use App\Exceptions\RequestTimeoutException;
use DB;
use Redis;
use Image;
use Exception;
use App\Services\Show91;
use App\Services\DailianMama;
use App\Models\LevelingConsult;
use App\Events\OrderArbitrationing;
use App\Exceptions\DailianException;

/**
 * 申请仲裁操作
 */
class Arbitrationing extends DailianAbstract implements DailianInterface
{
    protected $acceptableStatus = [13, 14, 15]; // 状态：15撤销中
	protected $beforeHandleStatus; // 操作之前的状态:15撤销中
    protected $handledStatus = 16; // 操作之后状态：16仲裁中
    protected $type          = 20; // 操作：20申请仲裁

    protected $pic = [];

    /**
     * [仲裁中：写日志，写流水]
     * @param string $orderNo  订单号
     * @param integer $userId  操作用户ID
     * @param int $runAfter 是否调用外部接口
     * @param array $pic 申请仲裁图片
     * @return bool
     * @throws DailianException
     */
    public function run($orderNo, $userId, $runAfter = 1, $pic = [])
    {	
    	DB::beginTransaction();
    	try {
    		// 赋值
    		$this->orderNo = $orderNo;
        	$this->userId  = $userId;
            $this->runAfter = $runAfter;
            $this->pic = $pic;
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
            // 删除状态不是 申请验收 的 redis 订单
            delRedisCompleteOrders($this->orderNo);
            // 操作成功，删除redis里面以前存在的订单报警
            $this->deleteOperateSuccessOrderFromRedis($this->orderNo);
        } catch (DailianException $exception) {
            DB::rollBack();
            myLog('opt-ex',  ['操作' => '申请仲裁', '订单号' => $this->orderNo, 'user' => $this->userId, $exception->getFile(), $exception->getLine(), $exception->getMessage()]);
            throw new DailianException($exception->getMessage());
    	}  catch (RequestTimeoutException $exception) {
            DB::rollBack();
            // 写入redis报警
            $this->addOperateFailOrderToRedis($this->order, $this->type);
            throw new DailianException($exception->getMessage());
        } catch (Exception $exception) {
            DB::rollBack();
            myLog('opt-ex',  ['操作' => '申请仲裁', '订单号' => $this->orderNo, 'user' => $this->userId, $exception->getFile(), $exception->getLine(), $exception->getMessage()]);
            throw new DailianException('订单异常');
        }
    	DB::commit();
    	// 返回
        return true;
    }

    /**
     * 调用外部提交申诉接口
     * @throws DailianException
     */
    public function after()
    {
        // 调用事件
        try {
            event(new OrderArbitrationing($this->order));
        }  catch (Exception $exception) {
            myLog('ex', ['OrderArbitrationing 事件',$exception->getMessage()]);
        }

        if ($this->runAfter) {
            if (config('leveling.third_orders')) {
                 // 获取订单和订单详情以及仲裁协商信息
                $orderDatas = $this->getOrderAndOrderDetailAndLevelingConsult($this->orderNo);
                // 如果没有撤销信息，抛出错误
                if (! $orderDatas['consult_order_no']) {
                    throw new DailianException('撤销记录不存在');
                }
                $orderDatas['pic1'] = !empty($this->pic['pic1']) ? base64ToBlob($this->pic['pic1']) : '';
                $orderDatas['pic2'] = !empty($this->pic['pic2']) ? base64ToBlob($this->pic['pic2']) : '';
                $orderDatas['pic3'] = !empty($this->pic['pic3']) ? base64ToBlob($this->pic['pic3']) : '';

                // 遍历代练平台
                foreach (config('leveling.third_orders') as $third => $thirdOrderNoName) {
                    // 如果订单详情里面存在某个代练平台的订单号
                    if ($third == $orderDatas['third'] && isset($orderDatas['third_order_no']) && ! empty($orderDatas['third_order_no'])) {
                        // 控制器-》方法-》参数
                        call_user_func_array([config('leveling.controller')[$third], config('leveling.action')['applyArbitration']], [$orderDatas]);
                    }
                }
            }
        }
    }
}

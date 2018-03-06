<?php

namespace App\Extensions\Dailian\Controllers;

use DB;
use Redis;
use Image;
use App\Services\Show91;
use App\Services\DailianMama;
use App\Models\LevelingConsult;
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
    
	/**
     * [仲裁中：写日志，写流水]
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

            $this->after();
            $this->orderCount();
            // 删除状态不是 申请验收 的 redis 订单
            delRedisCompleteOrders($this->orderNo);
    	} catch (DailianException $e) {
    		DB::rollBack();
            throw new DailianException($e->getMessage());
    	}
    	DB::commit();
    	// 返回
        return true;
    }

    /**
     * 调用外部提交申诉接口
     * @return [type] [description]
     */
    public function after()
    {
        if ($this->runAfter) {
            try {
                $orderDetails = $this->checkThirdClientOrder($this->order);
                $consult = LevelingConsult::where('order_no', $this->order->no)->first();

                if (! $consult) {
                    throw new DailianException('订单申诉或协商记录不存在!');
                }

                switch ($orderDetails['third']) {
                    case 1:
                        // 91申请仲裁接口
                        $options = [
                            'oid' => $orderDetails['show91_order_no'],
                            'appeal.title' => '申请仲裁',
                            'appeal.content' => $consult->complain_message,
                            'pic1' => new \CURLFile(public_path('frontend/images/123.png'), 'image/png'),
                            'pic2' => new \CURLFile(public_path('frontend/images/123.png'), 'image/png'),
                            'pic3' => new \CURLFile(public_path('frontend/images/123.png'), 'image/png'),
                        ];
                        Show91::addappeal($options);
                        break;
                    case 2:
                        // 代练妈妈申请仲裁接口
                        DailianMama::operationOrder($this->order, 20007);
                        break;
                    default:
                        throw new DailianException('不存在第三方接单平台!');
                        break;
                }
                // if ($this->order->detail()->where('field_name', 'third')->value('field_value') == 1) { //91代练
                //     $consult = LevelingConsult::where('order_no', $this->order->no)->first();

                //     if (! $consult) {
                //         throw new DailianException('订单申诉和协商记录不存在');
                //     }

                //     $thirdOrderNo = $this->order->detail()->where('field_name', 'third_order_no')->value('field_value');

                //     if (! $thirdOrderNo) {
                //         throw new DailianException('第三方订单号不存在');
                //     }

                //     $options = [
                //         'oid' => $this->order->detail()->where('field_name', 'third_order_no')->value('field_value'),
                //         'appeal.title' => '申请仲裁',
                //         'appeal.content' => $consult->complain_message,
                //         'pic1' => new \CURLFile(public_path('frontend/images/123.png'), 'image/png'),
                //         'pic2' => new \CURLFile(public_path('frontend/images/123.png'), 'image/png'),
                //         'pic3' => new \CURLFile(public_path('frontend/images/123.png'), 'image/png'),
                //     ];
                //     // 结果
                //     Show91::addappeal($options);
                // }
            } catch (DailianException $e) {
                throw new DailianException($e->getMessage());
            }
        }
    }
}

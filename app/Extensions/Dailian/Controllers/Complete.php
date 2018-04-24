<?php

namespace App\Extensions\Dailian\Controllers;

use App\Events\OrderFinish;
use App\Exceptions\AssetException;
use App\Exceptions\CustomException;
use App\Exceptions\RequestTimeoutException;
use App\Models\TaobaoTrade;
use DB;
use Asset;
use ErrorException;
use App\Services\Show91;
use App\Models\OrderDetail;
use App\Services\DailianMama;
use App\Extensions\Asset\Income;
use App\Exceptions\DailianException;
use App\Repositories\Frontend\OrderDetailRepository;
use LogisticsDummySendRequest;
use TopClient;

/**
 * 订单完成操作
 */
class Complete extends DailianAbstract implements DailianInterface
{
    protected $acceptableStatus   = [14]; // 状态：14待验收
    protected $beforeHandleStatus = 14; // 操作之前的状态:14待验收
    protected $handledStatus      = 20; // 状态：20已结算
    protected $type               = 12; // 操作：12完成
    protected $delivery           = 0; // 淘宝订单发货

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
    public function run($orderNo, $userId, $runAfter = 1, $delivery = 0)
    {
    	DB::beginTransaction();
        try {
    		// 赋值
    		$this->orderNo = $orderNo;
        	$this->userId  = $userId;
            $this->runAfter = $runAfter;
            $this->delivery = $delivery;
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
            // 发短信
            $this->sendMessage();
            $this->orderCount();
            // 删除状态不阻碍申请验收redis 订单
            delRedisCompleteOrders($this->orderNo);
            // 从留言获取任务中删除
            levelingMessageDel($this->orderNo);
            // 操作成功，删除redis里面以前存在的订单报警
            $this->deleteOperateSuccessOrderFromRedis($this->orderNo);
    	} catch (DailianException $e) {
            // 我们平台操作失败，写入redis报警
            $this->addOperateFailOrderToRedis($this->order, 12);
    		DB::rollBack();
            throw new DailianException($e->getMessage());
    	} catch (AssetException $exception) {
            throw new DailianException($exception->getMessage());
        } catch (RequestTimeoutException $exception) {
            throw new DailianException($exception->getMessage());
        } catch (CustomException $exception) {
            throw new DailianException($exception->getMessage());
        }
        DB::commit();
    	// 返回
        return true;
    }

    /**
     * 流水，代练完成，接单商户完成代练收入
     * @throws DailianException
     */
    public function updateAsset()
    {
        // 接单 代练收入
        Asset::handle(new Income($this->order->amount, 12, $this->order->no, '代练订单完成收入', $this->order->gainer_primary_user_id));

        if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
            throw new DailianException('流水记录写入失败');
        }

        if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
            throw new DailianException('流水记录写入失败');
        }

        if ($this->order->detail()->where('field_name', 'security_deposit')->value('field_value')) {
            // 接单 退回安全保证金
            Asset::handle(new Income($this->order->detail()->where('field_name', 'security_deposit')->value('field_value'), 8, $this->order->no, '退回安全保证金', $this->order->gainer_primary_user_id));

            if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                throw new DailianException('流水记录写入失败');
            }

            if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                throw new DailianException('流水记录写入失败');
            }
        }

        if ($this->order->detail()->where('field_name', 'efficiency_deposit')->value('field_value')) {
            // 接单 退效率保证金
            Asset::handle(new Income($this->order->detail()->where('field_name', 'efficiency_deposit')->value('field_value'), 9, $this->order->no, '退回效率保证金', $this->order->gainer_primary_user_id));

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

    /**
     * 订单验收结算
     * @return [type] [description]
     */
    public function after()
    {
        if ($this->runAfter) {

            if (config('leveling.third_orders')) {
                // 获取订单和订单详情以及仲裁协商信息
                $orderDatas = $this->getOrderAndOrderDetailAndLevelingConsult($this->orderNo);
                // 遍历代练平台
                foreach (config('leveling.third_orders') as $third => $thirdOrderNoName) {
                    // 如果订单详情里面存在某个代练平台的订单号
                    if ($third == $orderDatas['third'] && isset($orderDatas['third_order_no']) && ! empty($orderDatas['third_order_no'])) {
                        // 控制器-》方法-》参数
                        call_user_func_array([config('leveling.controller')[$third], config('leveling.action')['complete']], [$orderDatas]);
                    }
                }
            }

            /**
             * 以下只适用于  91  和 代练妈妈
             */
            $orderDetails = $this->checkThirdClientOrder($this->order);

            switch ($orderDetails['third']) {
                case 1:
                    // 91 完成接口
                    $options = [
                        'oid' => $orderDetails['show91_order_no'],
                        'p' => config('show91.password'),
                    ];
                    Show91::accept($options);
                    break;
                case 2:
                    // 代练妈妈完成接口
                    DailianMama::operationOrder($this->order, 20013);
                    break;
            }

            // 将相关的淘宝订单发货''
            if ($this->delivery == 1) {
                $sourceOrderNo = OrderDetail::select()->where('order_no', $this->order->no)
                    ->whereIn('field_name_alias', ['source_order_no'])
                    ->pluck('field_value')
                    ->toArray();

                // 去重
                $uniqueArray = array_unique($sourceOrderNo);

                if (count($uniqueArray)) {
                    // 将订单号淘宝订单状态改为交易成功
                    OrderDetail::where('order_no', $this->order->no)
                        ->where('field_name', 'taobao_status')
                        ->update(['field_value' => 2]);

                    $taobaoTrade = TaobaoTrade::select('tid', 'seller_nick')->whereIn('tid', $uniqueArray)->get();
                    // 获取备注并更新
                    $client = new TopClient;
                    $client->format = 'json';
                    $client->appkey = '12141884';
                    $client->secretKey = 'fd6d9b9f6ff6f4050a2d4457d578fa09';
                    foreach ($taobaoTrade as $item) {
                       try {
                           $req = new LogisticsDummySendRequest;
                           $req->setTid($item->tid);
                           $resp = $client->execute($req, taobaoAccessToken($item->seller_nick));
                       } catch (\ErrorException $exception) {
                           myLog('ex', [$exception->getMessage()]);
                       }
                    }
                }
            }

            return true;
        }
    }

    /**
     * 发短信
     * @return [type] [description]
     */
    public function sendMessage()
    {
        try {
            event(new OrderFinish($this->order));
        } catch (ErrorException $errorException) {
            myLog('ex', ['订单完成 异常', $errorException->getMessage()]);
        } catch (\Exception $exception) {
            myLog('ex', ['订单完成 异常', $exception->getMessage()]);
        }
    }
}

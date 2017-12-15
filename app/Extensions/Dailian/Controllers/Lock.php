<?php

namespace App\Extensions\Dailian\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Lock extends Controller
{
     //锁定
    protected $acceptableStatus = [13, 14, 17]; // 状态：13,代练中，14待验收，17异常
	protected $beforeHandleStatus = 13; // 操作之前的状态:12未接单
    protected $handledStatus    = 18; // 状态：18锁定
    protected $type             = 16; // 操作：16锁定
	// 运行, 第一个参数为订单号，第二个参数为操作用户id
    public function run($no, $userId)
    {	
    	DB::beginTransaction();
    	try {
    		// 赋值
    		$this->orderNo = $no;
        	$this->userId  = $userId;
        	$this->$beforeHandleStatus = $this->getObject()->status ?? 13;
    		// 获取订单对象
		    $this->getObject();
		    // 创建操作前的订单日志详情
		    $this->createLogObject();
		    // 设置订单属性
		    $this->setAttributes();
		    // 保存更改状态后的订单
		    $this->save();
		    // 更新平台资产
		    $this->updateAsset();
		    // 订单日志描述
		    $this->logDescription();
		    // 保存操作日志
		    $this->saveLog();
		   	// 保存权重
		   	$this->saveWeight();
		    // 后续操作
		    $this->after();
    	} catch (Exception $e) {
    		DB::rollBack();
    		throw new Exception($e->getMessage());
    	}
    	DB::commit();
    	// 返回
        return true;
    }
}

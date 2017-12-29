<?php

namespace App\Extensions\Dailian\Controllers;

interface DailianInterface
{
	// 获取订单对象
    public function getObject();
    // 创建操作前的订单日志详情
    public function createLogObject();
    // 设置订单属性
    public function setAttributes();
    // 保存更改状态后的订单
    public function save();
    // 更新平台资产
    public function updateAsset();
    // 订单日志描述
    public function setDescription();
    // 保存操作日志
    public function saveLog();
    //
    public function after();
}

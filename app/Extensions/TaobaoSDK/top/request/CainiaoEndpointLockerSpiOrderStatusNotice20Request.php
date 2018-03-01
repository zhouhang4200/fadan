<?php
/**
 * TOP API: cainiao.endpoint.locker.spi.order.status.notice_2.0 request
 * 
 * @author auto create
 * @since 1.0, 2017.08.17
 */
class CainiaoEndpointLockerSpiOrderStatusNotice20Request
{
	/** 
	 * 附属信息
	 **/
	private $extra;
	
	/** 
	 * 订单号
	 **/
	private $orderCode;
	
	/** 
	 * 订单类型(0-取件业务)
	 **/
	private $orderType;
	
	/** 
	 * 站点ID
	 **/
	private $stationId;
	
	/** 
	 * 状态 IS_NOTICED:已经发送消息通知
	 **/
	private $status;
	
	private $apiParas = array();
	
	public function setExtra($extra)
	{
		$this->extra = $extra;
		$this->apiParas["extra"] = $extra;
	}

	public function getExtra()
	{
		return $this->extra;
	}

	public function setOrderCode($orderCode)
	{
		$this->orderCode = $orderCode;
		$this->apiParas["order_code"] = $orderCode;
	}

	public function getOrderCode()
	{
		return $this->orderCode;
	}

	public function setOrderType($orderType)
	{
		$this->orderType = $orderType;
		$this->apiParas["order_type"] = $orderType;
	}

	public function getOrderType()
	{
		return $this->orderType;
	}

	public function setStationId($stationId)
	{
		$this->stationId = $stationId;
		$this->apiParas["station_id"] = $stationId;
	}

	public function getStationId()
	{
		return $this->stationId;
	}

	public function setStatus($status)
	{
		$this->status = $status;
		$this->apiParas["status"] = $status;
	}

	public function getStatus()
	{
		return $this->status;
	}

	public function getApiMethodName()
	{
		return "cainiao.endpoint.locker.spi.order.status.notice_2.0";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->orderCode,"orderCode");
		RequestCheckUtil::checkNotNull($this->orderType,"orderType");
		RequestCheckUtil::checkNotNull($this->stationId,"stationId");
		RequestCheckUtil::checkNotNull($this->status,"status");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}

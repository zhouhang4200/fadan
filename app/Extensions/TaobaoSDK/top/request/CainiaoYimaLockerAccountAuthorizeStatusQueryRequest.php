<?php
/**
 * TOP API: cainiao.yima.locker.account.authorize.status.query request
 * 
 * @author auto create
 * @since 1.0, 2017.07.12
 */
class CainiaoYimaLockerAccountAuthorizeStatusQueryRequest
{
	/** 
	 * 柜子url
	 **/
	private $guiUrl;
	
	/** 
	 * 柜子stationId,url和id必须有一个
	 **/
	private $stationId;
	
	/** 
	 * 小件员id
	 **/
	private $userId;
	
	private $apiParas = array();
	
	public function setGuiUrl($guiUrl)
	{
		$this->guiUrl = $guiUrl;
		$this->apiParas["gui_url"] = $guiUrl;
	}

	public function getGuiUrl()
	{
		return $this->guiUrl;
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

	public function setUserId($userId)
	{
		$this->userId = $userId;
		$this->apiParas["user_id"] = $userId;
	}

	public function getUserId()
	{
		return $this->userId;
	}

	public function getApiMethodName()
	{
		return "cainiao.yima.locker.account.authorize.status.query";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->userId,"userId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}

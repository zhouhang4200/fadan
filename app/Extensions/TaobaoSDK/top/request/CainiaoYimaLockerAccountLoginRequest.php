<?php
/**
 * TOP API: cainiao.yima.locker.account.login request
 * 
 * @author auto create
 * @since 1.0, 2017.07.12
 */
class CainiaoYimaLockerAccountLoginRequest
{
	/** 
	 * 柜子url
	 **/
	private $guiUrl;
	
	/** 
	 * 当前纬度
	 **/
	private $lat;
	
	/** 
	 * 当前经度
	 **/
	private $lng;
	
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

	public function setLat($lat)
	{
		$this->lat = $lat;
		$this->apiParas["lat"] = $lat;
	}

	public function getLat()
	{
		return $this->lat;
	}

	public function setLng($lng)
	{
		$this->lng = $lng;
		$this->apiParas["lng"] = $lng;
	}

	public function getLng()
	{
		return $this->lng;
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
		return "cainiao.yima.locker.account.login";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->guiUrl,"guiUrl");
		RequestCheckUtil::checkNotNull($this->userId,"userId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}

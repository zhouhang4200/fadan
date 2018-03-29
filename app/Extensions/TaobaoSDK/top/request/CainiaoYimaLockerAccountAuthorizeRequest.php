<?php
/**
 * TOP API: cainiao.yima.locker.account.authorize request
 * 
 * @author auto create
 * @since 1.0, 2017.07.12
 */
class CainiaoYimaLockerAccountAuthorizeRequest
{
	/** 
	 * 一次性授权码
	 **/
	private $accessCode;
	
	/** 
	 * 授权自提柜的linkappkey
	 **/
	private $linkAppkey;
	
	/** 
	 * 小件员id
	 **/
	private $userId;
	
	private $apiParas = array();
	
	public function setAccessCode($accessCode)
	{
		$this->accessCode = $accessCode;
		$this->apiParas["access_code"] = $accessCode;
	}

	public function getAccessCode()
	{
		return $this->accessCode;
	}

	public function setLinkAppkey($linkAppkey)
	{
		$this->linkAppkey = $linkAppkey;
		$this->apiParas["link_appkey"] = $linkAppkey;
	}

	public function getLinkAppkey()
	{
		return $this->linkAppkey;
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
		return "cainiao.yima.locker.account.authorize";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->accessCode,"accessCode");
		RequestCheckUtil::checkNotNull($this->linkAppkey,"linkAppkey");
		RequestCheckUtil::checkNotNull($this->userId,"userId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}

<?php
/**
 * TOP API: cainiao.endpoint.locker.spi.account.authorize request
 * 
 * @author auto create
 * @since 1.0, 2017.07.14
 */
class CainiaoEndpointLockerSpiAccountAuthorizeRequest
{
	/** 
	 * 一次性授权码
	 **/
	private $authCode;
	
	/** 
	 * 柜子公司编码
	 **/
	private $companyCode;
	
	/** 
	 * 开放用户id
	 **/
	private $openUserId;
	
	private $apiParas = array();
	
	public function setAuthCode($authCode)
	{
		$this->authCode = $authCode;
		$this->apiParas["auth_code"] = $authCode;
	}

	public function getAuthCode()
	{
		return $this->authCode;
	}

	public function setCompanyCode($companyCode)
	{
		$this->companyCode = $companyCode;
		$this->apiParas["company_code"] = $companyCode;
	}

	public function getCompanyCode()
	{
		return $this->companyCode;
	}

	public function setOpenUserId($openUserId)
	{
		$this->openUserId = $openUserId;
		$this->apiParas["open_user_id"] = $openUserId;
	}

	public function getOpenUserId()
	{
		return $this->openUserId;
	}

	public function getApiMethodName()
	{
		return "cainiao.endpoint.locker.spi.account.authorize";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->authCode,"authCode");
		RequestCheckUtil::checkNotNull($this->companyCode,"companyCode");
		RequestCheckUtil::checkNotNull($this->openUserId,"openUserId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}

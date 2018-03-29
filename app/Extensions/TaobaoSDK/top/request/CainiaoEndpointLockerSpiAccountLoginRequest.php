<?php
/**
 * TOP API: cainiao.endpoint.locker.spi.account.login request
 * 
 * @author auto create
 * @since 1.0, 2017.06.16
 */
class CainiaoEndpointLockerSpiAccountLoginRequest
{
	/** 
	 * 柜子公司编码
	 **/
	private $companyCode;
	
	/** 
	 * 柜子Id，gui_id和gui_url必须存在一个
	 **/
	private $guiId;
	
	/** 
	 * 柜子url
	 **/
	private $guiUrl;
	
	/** 
	 * 开放用户id
	 **/
	private $openUserId;
	
	private $apiParas = array();
	
	public function setCompanyCode($companyCode)
	{
		$this->companyCode = $companyCode;
		$this->apiParas["company_code"] = $companyCode;
	}

	public function getCompanyCode()
	{
		return $this->companyCode;
	}

	public function setGuiId($guiId)
	{
		$this->guiId = $guiId;
		$this->apiParas["gui_id"] = $guiId;
	}

	public function getGuiId()
	{
		return $this->guiId;
	}

	public function setGuiUrl($guiUrl)
	{
		$this->guiUrl = $guiUrl;
		$this->apiParas["gui_url"] = $guiUrl;
	}

	public function getGuiUrl()
	{
		return $this->guiUrl;
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
		return "cainiao.endpoint.locker.spi.account.login";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->companyCode,"companyCode");
		RequestCheckUtil::checkNotNull($this->openUserId,"openUserId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}

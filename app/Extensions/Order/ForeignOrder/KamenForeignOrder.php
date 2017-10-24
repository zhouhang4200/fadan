<?php

namespace App\Extensions\Order\ForeignOrder;

use App\Models\Goods;
use App\Models\GoodsTemplateWidget;
use App\Models\ForeignOrder as ForeignOrderModel;

class KamenForeignOrder extends ForeignOrder
{
    public function outputOrder($data)
    {
    	$array = $this->xmlToArray($data);

    	$decodeArray =  $this->urldecodeData($array['Order']);
    	
    	$model = $this->createForeignOrder($decodeArray);

    	if ($model) {

    		return $this->output($model);
    	}

    }

    public function xmlToArray($xml)
    {    
        libxml_disable_entity_loader(true);

        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);     
    }

    public function urldecodeData($array)
    {
    	foreach ($array as $k => &$v) {

    		if (! $v) {

    			$v = '';
    		}

    		if ($v && ! is_array($v)) {

    			$v = urldecode($v);
    		}
    	}
    	return $array;
    }

    protected function createForeignOrder($decodeArray)
    {
		$data['channel']          = 3;
		$data['channel_name']     = '卡门';
		$data['foreign_order_id'] = $decodeArray['CustomerOrderNo'];
		$data['order_time']       = $decodeArray['BuyTime'];
		$data['foreign_goods_id'] = $decodeArray['ProductId'];
		$data['single_price']     = $decodeArray['ProductPrice'];
		$data['total_price']      = bcdiv($decodeArray['ProductPrice'], $decodeArray['BuyNum'], 4);
		$data['tel']              = $decodeArray['ContactType'] ?: '';
		$data['qq']               = $decodeArray['ContactQQ'] ?: '';
		$data['details']          = $this->saveDetails($decodeArray);

		$has = ForeignOrderModel::where('foreign_order_id', $decodeArray['CustomerOrderNo'])->first();

		if (! $has) {

			return ForeignOrderModel::create($data);
		}

		return false;
    }

    protected function output(ForeignOrderModel $model)
    {
    	$goodsTemplateId = Goods::where('foreign_goods_id', $model->foreign_goods_id)->value('goods_template_id');

    	if ($goodsTemplateId) {

    		$fieldNames = GoodsTemplateWidget::where('goods_template_id', $goodsTemplateId)->pluck('field_name');

    		if ($fieldNames->count() > 0) {

    			foreach ($fieldNames as $key => $fieldName) {

    				if ($fieldName == 'version') {

    					$data['version'] = $this->version($model->details->region);

    				} else {

    					$data[$fieldName] = $model->details->$fieldName ?: '';
    				}
    			}
    			return $data;
    		}
    	}

    	return '无';
    }

    protected function saveDetails($decodeArray)
    {
    	return [
			"OrderNo" => $decodeArray['OrderNo'],
			"OrderStatus" => $decodeArray['OrderStatus'],
			"BuyTime" => $decodeArray['OrderStatus'],
			"number" => $decodeArray['BuyTime'],
			"ProductId" => $decodeArray['ProductId'],
			"ProductPrice" => $decodeArray['ProductPrice'],
			"ProductName" => $decodeArray['ProductName'],
			"ProductType" => $decodeArray['ProductType'],
			"TemplateId" => $decodeArray['TemplateId'],
			"account" => $decodeArray['ChargeAccount'],
			"password" => $decodeArray['ChargePassword'],
			"ChargeGame" => $decodeArray['ChargeGame'],
			"region" => $decodeArray['ChargeRegion'],
			"serve" => $decodeArray['ChargeServer'],
			"ChargeType" => $decodeArray['ChargeType'],
			"JSitid" => $decodeArray['JSitid'],
			"GSitid" => $decodeArray['GSitid'],
			"BuyerIp" => $decodeArray['BuyerIp'],
			"OrderFrom" => $decodeArray['OrderFrom'],
			"role" => $decodeArray['RoleName'],
			"RemainingNumber" => $decodeArray['RemainingNumber'],
			"ContactType" => $decodeArray['ContactType'],
			"ContactQQ" => $decodeArray['ContactQQ'],
			"UseAccount" => $decodeArray['UseAccount'],
			"CustomerOrderNo" => $decodeArray['CustomerOrderNo'],
		];
    }

    protected function version($version)
    {
    	if (preg_match('/微信/', $version)) {

    		return '微信';

    	} else if (preg_match('/QQ/', $version)) {

    		return 'QQ';

    	} else {

    		return '';
    	}
    }
}

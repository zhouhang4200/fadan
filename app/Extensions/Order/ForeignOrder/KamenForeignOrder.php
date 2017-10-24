<?php

namespace App\Extensions\Order\ForeignOrder;

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
		$data['details']          = $decodeArray;

		return ForeignOrderModel::create($data);
    }

    protected function output(ForeignOrderModel $model)
    {
    	$goodsTemplateId = Goods::where('foreign_goods_id', $model->foreign_goods_id)->firest();

    	if ($goodsTemplateId) {

    		$fieldNames = GoodsTemplateWidget::where('goods_template_id', $goodsTemplateId)->pluck('field_name');

    		if ($fieldNames->count() > 0) {

    			
    		}
    	}

    	return '无';
    }
}

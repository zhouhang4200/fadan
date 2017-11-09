<?php

namespace App\Extensions\Order\ForeignOrder;

use App\Services\TmallOrderApi;
use App\Models\SiteInfo;
use Log;
use Exception;
use App\Models\Goods;
use App\Models\GoodsTemplateWidget;
use App\Models\ForeignOrder as ForeignOrderModel;

class KamenForeignOrder extends ForeignOrder
{
    public function outputOrder($data)
    {
        try {

        	$decodeArray =  $this->urldecodeData($data);

        	$model = $this->createForeignOrder($decodeArray);

        	if ($model) {
                $outputData = $this->output($model);

        		return $outputData;
        	}
            
        } catch (Exception $e) {
            Log::info('参数格式传入错误!', [ $e->getMessage(), 'data' => $data]);
        }
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
        // 如果进货站点为天猫店，则去取订单的天猫订单信息
        $siteInfo  = SiteInfo::where('kamen_site_id', $decodeArray['JSitid'])->first();
        $price = 0; $totalPrice = 0; $wangWang = '';

        if ($siteInfo && $siteInfo->channel == 3) {
            $tmallOrderInfo = TmallOrderApi::getOrder($siteInfo->kamen_site_id,  $decodeArray['CustomerOrderNo']);
            $price = $tmallOrderInfo['price'];
            $totalPrice = $tmallOrderInfo['payment'];
            $wangWang = $tmallOrderInfo['wang_wang'];
            $decodeArray['ProductPrice'] = $price;
            $decodeArray['total_price'] = $totalPrice;
        } else {
            $price = $decodeArray['ProductPrice'];
            $totalPrice = bcmul($price, $decodeArray['BuyNum'], 4);;
        }

		$data['channel']          =  $siteInfo->channel;
		$data['channel_name']     =  $siteInfo->name;
		$data['kamen_order_no']   =  $decodeArray['OrderNo'];
		$data['foreign_order_no'] = $decodeArray['CustomerOrderNo'];
		$data['order_time']       = $decodeArray['BuyTime'];
		$data['foreign_goods_id'] = $decodeArray['ProductId'];
		$data['single_price']     = $price;
		$data['total_price']      = $totalPrice;
		$data['wang_wang']        = $wangWang;
		$data['tel']              = $decodeArray['ContactType'] ?: '';
		$data['qq']               = $decodeArray['ContactQQ'] ?: '';
		$data['details']          = $this->saveDetails($decodeArray);

		$has = ForeignOrderModel::where('foreign_order_no', $decodeArray['CustomerOrderNo'])->first();

		if (! $has) {
			return ForeignOrderModel::create($data);
		}

		return false;
    }

    protected function output(ForeignOrderModel $model)
    {
        // 优先用数量与卡门商品ID切匹配，如果没有则直接用卡门商品ID查询
        $goods = Goods::where([
            'foreign_goods_id' => $model->foreign_goods_id,
            'quantity' =>$model->details->quantity,
        ])->first();

        if (!$goods) {
            $goods = Goods::where('foreign_goods_id', $model->foreign_goods_id)->first();
        }
    	if ($goods) {
            $data = [];
            // 商品ID
            $data['goods_id'] = $goods->id;
    		$fieldNames = GoodsTemplateWidget::where('goods_template_id', $goods->goods_template_id)->pluck('field_name');

    		if ($fieldNames->count() > 0) {

    			foreach ($fieldNames as $key => $fieldName) {

    				if ($fieldName == 'version') {
    					$data['version'] = $this->version($model->details->region);
    				}  else {
    					$data[$fieldName] = $model->details->$fieldName ?: '';
    				}
    			}
    			// 如果与数量匹配则将下单数量改为1
    			if ($goods->quantity != 0) {
                    $data['quantity'] = 1;
                    $data['price'] = $model->details->total_price;
                } else {
                    $data['price'] = $model->details->ProductPrice;
                }
                $data['kamen_site_id'] = $model->details->JSitid;
    			return $data;
    		}
    	}
    	return '无';
    }

    protected function saveDetails($decodeArray)
    {
    	return [
			"OrderNo" => $decodeArray['OrderNo'] ?? '',
			"OrderStatus" => $decodeArray['OrderStatus'] ?? '',
			"BuyTime" => $decodeArray['OrderStatus'] ?? '',
			"quantity" => $decodeArray['BuyNum'] ?? '',
			"ProductId" => $decodeArray['ProductId'] ?? '',
			"ProductPrice" => $decodeArray['ProductPrice'] ?? '',
			"ProductName" => $decodeArray['ProductName'] ?? '',
			"ProductType" => $decodeArray['ProductType'] ?? '',
			"TemplateId" => $decodeArray['TemplateId'] ?? '',
			"account" => $decodeArray['ChargeAccount'] ?? '',
			"password" => $decodeArray['ChargePassword'] ?? '',
			"ChargeGame" => $decodeArray['ChargeGame'] ?? '',
			"region" => $decodeArray['ChargeRegion'] ?? '',
			"serve" => $decodeArray['ChargeServer'] ?? '',
			"ChargeType" => $decodeArray['ChargeType'] ?? '',
			"JSitid" => $decodeArray['JSitid'] ?? '',
			"GSitid" => $decodeArray['GSitid'] ?? '',
			"BuyerIp" => $decodeArray['BuyerIp'] ?? '',
			"OrderFrom" => $decodeArray['OrderFrom'] ?? '',
			"role" => $decodeArray['OrderFrom'] ?? '',
			"RemainingNumber" => $decodeArray['RemainingNumber'] ?? '',
			"ContactType" => $decodeArray['ContactType'] ?? '',
			"ContactQQ" => $decodeArray['ContactQQ'] ?? '',
			"UseAccount" => $decodeArray['UseAccount'] ?? '',
			"foreign_order_no" => $decodeArray['CustomerOrderNo'] ?? '',
			"total_price" => $decodeArray['total_price'] ?? '',
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

    /**
     * 如果是自营天猫店则获取天猫订单售价
     * @param $kamenSite
     * @param $orderId
     * @return array
     */
    protected function tmallOrderInfo($kamenSite, $orderId)
    {
        return TmallOrderApi::getOrder($kamenSite, $orderId);
    }
}

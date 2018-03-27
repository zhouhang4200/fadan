<?php

namespace App\Extensions\Order\ForeignOrder;

use App\Models\GoodsContractorConfig;
use App\Models\WangWangBlacklist;
use App\Services\KamenOrderApi;
use App\Services\TmallOrderApi;
use App\Models\SiteInfo;
use Log;
use Exception;
use App\Models\Goods;
use App\Models\GoodsTemplateWidget;
use App\Models\ForeignOrder as ForeignOrderModel;

class KamenForeignOrder extends ForeignOrder
{
    // 商户ID
    protected $userId;

    // 卡门进货站点
    protected $jSiteId = 0;

    // 集市站点信息
    protected $siteInfo = null;

    // 渠道Id
    protected $channelId;

    // 渠道名
    protected $channelName;


    public function outputOrder($data)
    {
        try {
        	$decodeArray =  $this->urldecodeData($data);

            $this->getSiteInfo($decodeArray);

        	$model = $this->createForeignOrder($decodeArray);

        	if ($model) {
                $outputData = $this->output($model);

        		return $outputData;
        	}

        } catch (Exception $e) {
            Log::info('参数格式传入错误!', [ $e->getMessage(), $e->getLine(), $e->getFile(), 'data' => $data]);
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
        $price = 0; $totalPrice = 0; $wangWang = ''; $remark = '';

        if ($this->channelId == 3 && isset($decodeArray['CustomerOrderNo']) && $decodeArray['CustomerOrderNo']) {
            $tmallOrderInfo = TmallOrderApi::getOrder($this->jSiteId,  $decodeArray['CustomerOrderNo']);
            $price = $tmallOrderInfo['price'];
            $remark = $tmallOrderInfo['remark'];
            $totalPrice = $tmallOrderInfo['payment'];
            $wangWang = $tmallOrderInfo['wang_wang'];

            $decodeArray['ProductPrice'] = $price;
            $decodeArray['total_price'] = $totalPrice;
            $decodeArray['remark'] = $remark;
            $decodeArray['province'] = loginDetail($tmallOrderInfo['ip'])['province'];
        } else {
            $price = $decodeArray['ProductPrice'];
            $totalPrice = bcmul($price, $decodeArray['BuyNum'], 4);

            $decodeArray['ProductPrice'] = $price;
            $decodeArray['total_price'] = $totalPrice;
            $decodeArray['remark'] = $remark;
            $decodeArray['province'] = $this->jSiteId == 0 ? $decodeArray['ChargeServer'] : loginDetail($decodeArray['BuyerIp'])['province'];
        }

        // 旺旺黑名单检测,如果在黑名单中则直接失败订单
        if ($this->blacklist($wangWang)) {
            // 将订单改为处理中
            (KamenOrderApi::share()->ing($decodeArray['OrderNo']));
            // 将订单改为失败
            (KamenOrderApi::share()->fail($decodeArray['OrderNo']));
            return false;
        }

		$data['channel']          =  $this->channelId;
		$data['channel_name']     =  $this->channelName;
		$data['kamen_order_no']   =  $decodeArray['OrderNo'] ?? '';
		$data['foreign_order_no'] = isset($decodeArray['CustomerOrderNo']) ? $decodeArray['CustomerOrderNo'] : $decodeArray['OrderNo'];
		$data['order_time']       = $decodeArray['BuyTime'] ?? '';
		$data['foreign_goods_id'] = $decodeArray['ProductId'] ?? '';
		$data['single_price']     = $price;
		$data['total_price']      = $totalPrice;
		$data['wang_wang']        = $wangWang;
		$data['tel']              = $decodeArray['ContactType'] ?? '';
		$data['qq']               = $decodeArray['ContactQQ'] ?? '';
		$data['details']          = $this->saveDetails($decodeArray);

        if (isset($decodeArray['CustomerOrderNo']) && $decodeArray['CustomerOrderNo']) {
            $has = ForeignOrderModel::where('foreign_order_no', $decodeArray['CustomerOrderNo'])->first();
        } else {
            $has = ForeignOrderModel::where('kamen_order_no', $decodeArray['OrderNo'])->first();
        }

		if (! $has) {
			return ForeignOrderModel::create($data);
		}

		return false;
    }

    protected function output(ForeignOrderModel $model)
    {
        // 优先用数量与卡门商品ID切匹配，如果没有则直接用卡门商品ID查询
//        $siteId = !empty($model->details->JSitid) ? $model->details->JSitid : 0;
//        $userId = SiteInfo::where('kamen_site_id', $siteId)->value('user_id');

        $goods = Goods::where([
            'user_id' => $this->userId,
            'foreign_goods_id' => $model->foreign_goods_id,
            'quantity' =>$model->details->quantity,
        ])->first();

        if (!$goods) {
            $goods = Goods::where([
                'user_id' => $this->userId,
                'foreign_goods_id' =>  $model->foreign_goods_id,
            ])->first();
        }
    	if ($goods) {
            $data = [];
            // 商品ID
            $data['goods_id'] = $goods->id;
    		$fieldNames = GoodsTemplateWidget::where('goods_template_id', $goods->goods_template_id)->pluck('field_name');

    		if ($fieldNames->count() > 0) {

    			foreach ($fieldNames as $key => $fieldName) {
                    switch ($fieldName) {
                        case 'version':
                            $data['version'] = $this->version($model->details->region);
                            break;
                        case 'game_gold':
                        case 'game_gold_unit':
                            $data[$fieldName] = $goods->$fieldName ?? 0;
                            break;
                        default:
                            $data[$fieldName] = $model->details->$fieldName ?: '';
                            break;
                    }
    			}
    			// 如果与数量匹配则将下单数量改为1
    			if ($goods->quantity != 0) {
                    $data['quantity'] = 1;
                    $data['price'] = $model->details->total_price;
                } else {
                    $data['price'] = $model->details->ProductPrice;
                }
                $data['total'] = $model->details->total_price;
                $data['kamen_site_id'] = $this->jSiteId;
                $data['province'] = $model->details->province;
                $data['remark'] = $model->details->remark;
                $data['wang_wang'] = $model->wang_wang;
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
			"role" => !isset($decodeArray['JSitid']) ? $decodeArray['ChargeGame']  : $decodeArray['RoleName'] ?? '',
			"RemainingNumber" => $decodeArray['RemainingNumber'] ?? '',
			"ContactType" => $decodeArray['ContactType'] ?? '',
			"client_qq" => $decodeArray['ContactQQ'] ?? '',
			"UseAccount" => $decodeArray['UseAccount'] ?? '',
			"foreign_order_no" => $decodeArray['CustomerOrderNo'] ?? $decodeArray['OrderNo'] ,
			"total_price" => $decodeArray['total_price'] ?? '',
			"province" => $decodeArray['province'] ?? '',
			"remark" => $decodeArray['remark'] ?? '',
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

    /**
     * 旺旺黑名单
     * @param $wangWang
     * @return bool
     */
    protected function blacklist($wangWang)
    {
        $exist = WangWangBlacklist::where('wang_wang', $wangWang)->first();
        if ($exist) {
            return true;
        }
        return false;
    }

    /**
     * 获取自动下单对应的集市站点信息
     * @param $decodeArray
     */
    protected function getSiteInfo($decodeArray)
    {
        // 优先获取外包的卡门商品配置表
        $goodsContractorConfig = GoodsContractorConfig::where('km_goods_id', $decodeArray['ProductId'])->first();

        // 如果商品存在承包商获取站点信息
        if ($goodsContractorConfig) {
            $this->jSiteId = $goodsContractorConfig->user_id;
            $this->siteInfo = SiteInfo::where('user_id', $goodsContractorConfig->user_id)->first();
        } else {
            $this->jSiteId = isset($decodeArray['JSitid']) ? $decodeArray['JSitid'] : 0;
        }

    }
}

<?php

namespace App\Console\Commands;

use App\Events\OrderApplyComplete;
use App\Events\OrderArbitrationing;
use App\Events\OrderRevoking;
use App\Extensions\Dailian\Controllers\Complete;
use App\Extensions\Dailian\Controllers\DailianFactory;
use App\Extensions\Dailian\Controllers\Delete;
use App\Extensions\Dailian\Controllers\ForceRevoke;
use App\Extensions\Dailian\Controllers\Revoked;
use App\Models\OrderDetail;
use App\Models\TaobaoTrade;
use App\Repositories\Frontend\OrderAttachmentRepository;
use App\Repositories\Frontend\OrderDetailRepository;
use App\Services\DailianMama;
use App\Services\KamenOrderApi;
use App\Services\Leveling\DD373Controller;
use App\Services\Leveling\MayiDailianController;
use App\Services\Show91;
use App\Services\SmSApi;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Log, Config, Weight, Order;
use LogisticsDummySendRequest;
use OSS\Core\OssException;
use OSS\OssClient;
use TopClient;
use TradeFullinfoGetRequest;
use TraderatesGetRequest;

/**
 * Class OrderTestData
 * @package App\Console\Commands
 */
class Temp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Temp {no}{user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '测试';

    protected $message = [];

    protected $show91Status = [
        0 => "已发布",
        1 => "代练中",
        2 => "待验收",
        3 => "待结算",
        4 => "已结算",
        5 => "已挂起",
        6 => "已撤单",
        7 => "已取消",
        10 => "等待工作室接单",
        11 => "等待玩家付款",
        12 => "玩家超时未付款",
    ];

    protected $messageBeginId = 0;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $no = $this->argument('no');
        $user = $this->argument('user');

        echo taobaoAesEncrypt('{"AcookieId":null,"AdjustFee":null,"AlipayId":0,"AlipayNo":null,"AlipayPoint":0,"AlipayUrl":null,"AlipayWarnMsg":null,"AreaId":null,"ArriveCutTime":null,"ArriveInterval":0,"Assembly":null,"AsyncModified":null,"AvailableConfirmFee":null,"BuyerAlipayNo":"13660664440","BuyerArea":"未知","BuyerCodFee":null,"BuyerEmail":"13660664440@qq.com","BuyerFlag":0,"BuyerIp":"MzkuMTgxLjIzNy4xNzk=","BuyerMemo":null,"BuyerMessage":null,"BuyerNick":"孩子气4440","BuyerObtainPointFee":0,"BuyerRate":false,"CanRate":false,"CodFee":null,"CodStatus":null,"CommissionFee":null,"ConsignInterval":0,"ConsignTime":null,"CouponFee":0,"Created":"2018-06-04 11:30:44","CreditCardFee":null,"CrossBondedDeclare":false,"DelayCreateDelivery":0,"DiscountFee":null,"EncryptAlipayId":null,"EndTime":null,"EsDate":null,"EsRange":null,"EstConTime":null,"EtPlateNumber":null,"EtSerTime":null,"EtShopId":0,"EtShopName":null,"EtType":null,"EtVerifiedShopName":null,"EticketExt":null,"EticketServiceAddr":null,"ExpressAgencyFee":null,"ForbidConsign":0,"HasBuyerMessage":false,"HasPostFee":false,"HasYfx":false,"HkBirthday":null,"HkCardCode":null,"HkCardType":null,"HkChinaName":null,"HkEnName":null,"HkFlightDate":null,"HkFlightNo":null,"HkGender":null,"HkPickup":null,"HkPickupId":null,"Identity":null,"Iid":null,"InvoiceKind":null,"InvoiceName":null,"InvoiceType":null,"Is3D":false,"IsBrandSale":false,"IsDaixiao":false,"IsForceWlb":false,"IsLgtype":false,"IsPartConsign":false,"IsShShip":false,"IsWt":false,"LgAging":null,"LgAgingType":null,"MarkDesc":null,"Market":null,"Modified":null,"Num":1,"NumIid":559143959395,"NutFeature":null,"O2o":null,"O2oDelivery":null,"O2oEtOrderId":null,"O2oGuideId":null,"O2oGuideName":null,"O2oOutTradeId":null,"O2oShopId":null,"O2oShopName":null,"O2oSnatchStatus":null,"O2oStepOrderId":null,"O2oStepTradeDetail":null,"O2oVoucherPrice":null,"Obs":null,"OfpHold":0,"OmniAttr":null,"OmniParam":null,"OmnichannelParam":null,"OrderTaxFee":null,"OrderTaxPromotionFee":null,"Orders":[{"AdjustFee":null,"AssemblyItem":null,"AssemblyPrice":null,"AssemblyRela":null,"BindOid":0,"BindOids":null,"BuyerNick":null,"BuyerRate":false,"CalPenalty":null,"CarStoreCode":null,"CarStoreName":null,"CarTaker":null,"CarTakerId":null,"CarTakerIdNum":null,"CarTakerPhone":null,"Cid":0,"ClCarTaker":null,"ClCarTakerIDNum":null,"ClCarTakerIdNum":null,"ClCarTakerPhone":null,"ClDownPayment":null,"ClDownPaymentRatio":null,"ClInstallmentNum":null,"ClMonthPayment":null,"ClServiceFee":null,"ClTailPayment":null,"ComboId":null,"ConsignTime":null,"Customization":null,"DiscountFee":null,"DivideOrderFee":null,"DownPayment":null,"DownPaymentRatio":null,"EndTime":null,"EstimateConTime":null,"EtPlateNumber":null,"EtSerTime":null,"EtShopName":null,"EtVerifiedShopName":null,"FStatus":null,"FTerm":null,"FType":null,"Iid":null,"InstallmentNum":null,"InvType":null,"InvoiceNo":null,"IsDaixiao":false,"IsOversold":false,"IsServiceOrder":false,"IsShShip":false,"IsWww":false,"ItemMealId":0,"ItemMealName":null,"ItemMemo":null,"LogisticsCompany":null,"MdFee":null,"MdQualification":null,"Modified":null,"MonthPayment":null,"Num":0,"NumIid":0,"O2oEtOrderId":null,"Oid":0,"OidStr":null,"OrderAttr":null,"OrderFrom":null,"OutUniqueId":null,"OuterIid":null,"OuterSkuId":null,"PartMjzDiscount":null,"Payment":null,"Penalty":null,"PicPath":null,"Price":null,"RefundId":0,"RefundStatus":null,"SellerNick":null,"SellerRate":false,"SellerType":null,"ServiceFee":null,"Shipper":null,"ShippingType":null,"SkuId":null,"SkuPropertiesName":null,"Snapshot":null,"SnapshotUrl":null,"Status":null,"StoreCode":null,"SubOrderTaxFee":null,"SubOrderTaxPromotionFee":null,"SubOrderTaxRate":null,"TailPayment":null,"TicketExpdateKey":null,"TicketOuterId":null,"TimeoutActionTime":null,"Title":"同城游银子50万+4W 同城游戏银子54万两 同城游50元点卡 自动充值","TmserSpuCode":null,"TotalFee":null,"Type":null,"WsBankApplyNo":null,"Xxx":null,"ZhengjiStatus":null}],"OsDate":null,"OsRange":null,"PaidCouponFee":null,"PayTime":"2018-06-04 11:36:23","Payment":"44.90","PccAf":0,"PicPath":null,"PointFee":0,"PostFee":null,"PostGateDeclare":false,"Price":"44.90","Promotion":null,"PromotionDetails":[],"RealPointFee":0,"ReceivedPayment":null,"ReceiverAddress":"所在区/服:玉环\r\n游>戏账号:你是我的平\r\n备注:","ReceiverCity":null,"ReceiverCountry":null,"ReceiverDistrict":null,"ReceiverMobile":null,"ReceiverName":null,"ReceiverPhone":null,"ReceiverState":null,"ReceiverTown":null,"ReceiverZip":null,"RxAuditStatus":null,"SellerAlipayNo":null,"SellerCanRate":false,"SellerCodFee":null,"SellerEmail":null,"SellerFlag":0,"SellerMemo":null,"SellerMobile":null,"SellerName":null,"SellerNick":"李丽华199009","SellerPhone":null,"SellerRate":false,"SendTime":null,"ServiceOrders":[],"ServiceTags":[],"ShareGroupHold":0,"ShippingType":null,"ShopCode":null,"ShopPick":null,"Sid":null,"Snapshot":null,"SnapshotUrl":null,"Status":"WAIT_SELLER_SEND_GOODS","StepPaidFee":null,"StepTradeStatus":null,"TeamBuyHold":0,"Tid":169089154989950874,"TidStr":"169089154989950874","TimeoutActionTime":null,"Title":null,"TopHold":0,"Toptype":0,"TotalFee":null,"TradeAttr":null,"TradeExt":null,"TradeFrom":"WAP,WAP","TradeMemo":null,"TradeSource":null,"Type":"fixed","YfxFee":null,"YfxId":null,"YfxType":null,"ZeroPurchase":false}');



        die;
        // 我们是待接单
        if ($status == 1) {
            // 获取所有没有接单的单
            $allOrder = \App\Models\Order::where('service_id', 4)->where('status', 1)->get();

            foreach ($allOrder as $item) {

                $show91OrderNO = OrderDetail::where('order_no', $item->no)->where('field_name', 'show91_order_no')->first();

                if ($show91OrderNO->field_value) {
                    // 如果91订单状态是接单，调我们自己接单接口，如果不是记录一下他们状态
                    $orderDetail = Show91::orderDetail(['oid' => $show91OrderNO->field_value]);

                    // 代练中
                    if ($orderDetail['data']['order_status'] == 1) {

                        // 调用自己接单接口
                        $client = new Client();
                        $response = $client->request('POST', 'http://js.qsios.com/api/receive/order', [
                            'form_params' => [
                                'sign' => 'a46ae5de453bfaadc8548a3e48c151db',
                                'orderNo' => $show91OrderNO->field_value,
                            ],
                        ]);
                        $result = json_decode($response->getBody()->getContents());

                        myLog('temp-log', [
                            '类型' => '要修改',
                            '我们订单号' => $item->no,
                            '91订单号' => $show91OrderNO->field_value,
                            '91状态' => $this->show91Status[$orderDetail['data']['order_status']],
                            '我们状态' => config('order.status_leveling')[$item->status],
                            '修改结果' => $result
                        ]);
                    } else if ($orderDetail['data']['order_status'] == 2) {
                        // 调用自己接单接口
                        $client = new Client();
                        $response = $client->request('POST', 'http://js.qsios.com/api/receive/order', [
                            'form_params' => [
                                'sign' => 'a46ae5de453bfaadc8548a3e48c151db',
                                'orderNo' => $show91OrderNO->field_value,
                            ],
                        ]);
                        $result = json_decode($response->getBody()->getContents());

                        myLog('temp-log', [
                            '类型' => '待验收改接单',
                            '我们订单号' => $item->no,
                            '91订单号' => $show91OrderNO->field_value,
                            '91状态' => $this->show91Status[$orderDetail['data']['order_status']],
                            '我们状态' => config('order.status_leveling')[$item->status],
                            '修改结果' => $result
                        ]);

                        // 调用自己接单接口
                        $client = new Client();
                        $response = $client->request('POST', 'http://js.qsios.com/api/apply/complete', [
                            'form_params' => [
                                'sign' => 'a46ae5de453bfaadc8548a3e48c151db',
                                'orderNo' => $show91OrderNO->field_value,
                            ],
                        ]);
                        $result = json_decode($response->getBody()->getContents());

                        myLog('temp-log', [
                            '类型' => '改成待验收',
                            '我们订单号' => $item->no,
                            '91订单号' => $show91OrderNO->field_value,
                            '91状态' => $this->show91Status[$orderDetail['data']['order_status']],
                            '我们状态' => config('order.status_leveling')[$item->status],
                            '修改结果' => $result
                        ]);

                    } else {
                        myLog('temp-log', [
                            '类型' => '不用修改',
                            '我们订单号' => $item->no,
                            '91订单号' => $show91OrderNO->field_value,
                            '状态' => $this->show91Status[$orderDetail['data']['order_status']],
                            '我们状态' => config('order.status_leveling')[$item->status],
                            '状态码' => $orderDetail['data']['order_status']
                        ]);
                    }
                } else {
                    myLog('temp-log', [
                        '类型' => '没有91单号',
                        '我们订单号' => $item->no,
//                    '91订单号' => $show91OrderNO->field_value,
//                    '状态' => $this->show91Status[$orderDetail['data']['order_status']],
                        '我们状态' => config('order.status_leveling')[$item->status],
//                    '状态码' => $orderDetail['data']['order_status']
                    ]);
                }
            }

        } else if($status == 13) {
            // 获取所有没有接单的单
            $allOrder = \App\Models\Order::where('service_id', 4)->where('status', 13)->get();

            foreach ($allOrder as $item) {

                $show91OrderNO = OrderDetail::where('order_no', $item->no)->where('field_name', 'show91_order_no')->first();

                if (isset($show91OrderNO->field_value)) {
                    // 如果91订单状态是接单，调我们自己接单接口，如果不是记录一下他们状态
                    $orderDetail = Show91::orderDetail(['oid' => $show91OrderNO->field_value]);

                    // 91 是待验收
                    if ($orderDetail['data']['order_status'] == 2 || $orderDetail['data']['order_status'] == 3) {

                        // 调用自己接单接口
                        $client = new Client();
                        $response = $client->request('POST', 'http://js.qsios.com/api/apply/complete', [
                            'form_params' => [
                                'sign' => 'a46ae5de453bfaadc8548a3e48c151db',
                                'orderNo' => $show91OrderNO->field_value,
                            ],
                        ]);
                        $result = json_decode($response->getBody()->getContents());

                        myLog('temp-log', [
                            '类型' => '要修改',
                            '我们订单号' => $item->no,
                            '91订单号' => $show91OrderNO->field_value,
                            '91状态' => $this->show91Status[$orderDetail['data']['order_status']],
                            '我们状态' => config('order.status_leveling')[$item->status],
                            '修改结果' => $result
                        ]);

                    } else {
                        myLog('temp-log', [
                            '类型' => '不用修改',
                            '我们订单号' => $item->no,
                            '91订单号' => $show91OrderNO->field_value,
                            '状态' => $this->show91Status[$orderDetail['data']['order_status']],
                            '我们状态' => config('order.status_leveling')[$item->status],
                            '状态码' => $orderDetail['data']['order_status']
                        ]);
                    }
                } else {
                    myLog('temp-log', [
                        '类型' => '没有91单号',
                        '我们订单号' => $item->no,
//                    '91订单号' => $show91OrderNO->field_value,
//                    '状态' => $this->show91Status[$orderDetail['data']['order_status']],
                        '我们状态' => config('order.status_leveling')[$item->status],
//                    '状态码' => $orderDetail['data']['order_status']
                    ]);
                }
            }
        } else  {
//            (new Revoked())->run('2018042109054600000281', 8711, 0);
//            $this->addPrice();
            dd($this->queryShow91Order($status));
        }
    }

    public function get($orderNO, $beginId = 0)
    {
        $message = DailianMama::chatOldList($orderNO, $beginId);

        if (count($message['list'])) {
            $this->message = array_merge($this->message, $message['list']);
            $this->get($orderNO, $message['beginid']);
        }
    }

    /**
     * 对比91订单状态;
     */
    public function show91OrderStatus()
    {
        // 获取所有没有接单的单
        $allOrder = \App\Models\Order::where('service_id', 4)->get();

        foreach ($allOrder as $item) {

            $show91OrderNO = OrderDetail::where('order_no', $item->no)->where('field_name', 'show91_order_no')->first();

            if (isset($show91OrderNO->field_value) && $show91OrderNO->field_value) {
                // 如果91订单状态是接单，调我们自己接单接口，如果不是记录一下他们状态
                $orderDetail = Show91::orderDetail(['oid' => $show91OrderNO->field_value]);

                // 91 是待验收
                if (isset($orderDetail['data'])) {

                    myLog('status-log', [
                        '类型' => '双方存在订单',
                        '我们订单号' => $item->no,
                        '91订单号' => $show91OrderNO->field_value,
                        '91状态' => $this->show91Status[$orderDetail['data']['order_status']],
                        '我们状态' => config('order.status_leveling')[$item->status],
                        '我们价格' => $item->amount,
                        '91价格' => $orderDetail['data']['price'],
                        '价格相等' => $item->amount == $orderDetail['data']['price'] ? '是' : '否'
                    ]);

                } else {
                    myLog('status-log', [
                        '类型' => '没有91单信息',
                        '我们订单号' => $item->no,
                        '我们状态' => config('order.status_leveling')[$item->status],
                    ]);
                }
            } else {
                myLog('status-log', [
                    '类型' => '没有91单号',
                    '我们订单号' => $item->no,
                    '我们状态' => config('order.status_leveling')[$item->status],
                ]);
            }
        }
    }

    /**
     * 查询show91订单
     * @param $orderNO
     */
    public function queryShow91Order($orderNO)
    {
        return Show91::orderDetail(['oid' => $orderNO]);
    }

    /**
     * 完成订单
     * @param $no
     * @param $user
     */
    public function complete($no)
    {

        $order  = \App\Models\Order::where('no', $no)->first();
        if ($order) {
            dump((new Complete())->run($order->no, $order->creator_primary_user_id, 0));
        }

    }

    /**
     * 完成订单
     * @param $no
     * @param $user
     */
    public function revoked($no)
    {

        $order  = \App\Models\Order::where('no', $no)->first();
        if ($order) {
            dump((new Revoked())->run($order->no, $order->creator_primary_user_id, 0));
        }

    }

    /**
     * 删除
     * @param $no
     * @param $user
     */
    public function delete($no, $user)
    {
        (new Delete())->run($no, $user, 0);
    }

    public function forceRevoke($no, $user)
    {
        (new ForceRevoke())->run($no, $user);
    }

    public function addPrice()
    {
        $params = [
            'account' => config('show91.account'),
            'sign' => config('show91.sign'),
        ];

        $options = [
            'oid' => 'ORD180419220853663712',
            'appwd' => config('show91.password'),
            'cash' => 6,
        ];

        $options = array_merge($params, $options);

        $client = new Client;
        $response = $client->request('POST', config('show91.url.addPrice'), [
            'query' => $options,
        ]);
       dd($response->getBody()->getContents());
    }

    /**
     * 同步91己验收但集市没有验收的单
     */
    public function e()
    {
       $allOrder =  \App\Models\Order::where('service_id', 4)
           ->where('status', '14')
           ->get();

        foreach ($allOrder as $item) {
            $detail = OrderDetail::where('order_no', $item->no)->where('field_name', 'show91_order_no')->first();

            if ($detail) {

                if ($detail->field_value) {
                    $show91 = $this->queryShow91Order($detail->field_value);
                    if ($show91['data']['order_status'] == 4) {
                        myLog('show-91-14-1', [
                            'no'=> $item->no,
                            '91no' => $detail->field_value,
                            '91状态' => $this->show91Status[$show91['data']['order_status']],
                            '我们状态' => config('order.status_leveling')[$item->status],
                            '我们价格' => $item->amount,
                            '91' => $show91['data']['price'],
                            '是否相关' => $show91['data']['price'] == $item->amount ? '是' : '否',
                        ]);
                        try {
                            $this->complete($item->no, $item->creator_primary_user_id);
                        } catch (\Exception $exception) {
                            myLog('show-91-14-3', ['no'=> $item->no]);
                        }
                    } else {
                        myLog('show-91-14-2', [
                            'no'=> $item->no,
                            '91no' => $detail->field_value,
                            '91状态' => $this->show91Status[$show91['data']['order_status']],
                            '我们状态' => config('order.status_leveling')[$item->status],
                            '我们价格' => $item->amount,
                            '91' => $show91['data']['price'],
                            '是否相关' => $show91['data']['price'] == $item->amount ? '是' : '否',
                        ]);
                    }
                }
            }
        }
    }

}
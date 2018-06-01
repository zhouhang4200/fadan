<?php

namespace App\Console\Commands;

use App\Extensions\Dailian\Controllers\Arbitrationed;
use App\Extensions\Dailian\Controllers\Complete;
use App\Models\OrderDetail;
use App\Repositories\Frontend\OrderRepository;
use App\Services\Leveling\DD373Controller;
use App\Services\Leveling\MayiDailianController;
use App\Services\Leveling\Show91Controller;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Console\Command;

use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Test  {type}';

    protected $orderRepository;
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
        parent::__construct();
    }

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

    protected $dd373 = [
        1 => "未接单",
        4 => "代练中",
        5 => "待验收",
        6 => "已完成",
        9 => "已撤消",
        10 => "已结算",
        11 => "已锁定",
        12 => "异常",
        13 => "仲裁中",
        14 => "已仲裁",

    ];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $client = new Client();

        for ($i = 0; $i < 1000; $i++) {

            $b= '{"AcookieId":null,"AdjustFee":null,"AlipayId":0,"AlipayNo":null,"AlipayPoint":0,"AlipayUrl":null,"AlipayWarnMsg":null,"AreaId":null,"ArriveCutTime":null,"ArriveInterval":0,"Assembly":null,"AsyncModified":null,"AvailableConfirmFee":null,"BuyerAlipayNo":"1-6265030676","BuyerArea":"密苏里州圣路易斯市Charter通信公司","BuyerCodFee":null,"BuyerEmail":"","BuyerFlag":0,"BuyerIp":"NzEuODQuNTAuNDY=","BuyerMemo":null,"BuyerMessage":null,"BuyerNick":"joseph8047","BuyerObtainPointFee":0,"BuyerRate":false,"CanRate":false,"CodFee":null,"CodStatus":null,"CommissionFee":null,"ConsignInterval":0,"ConsignTime":null,"CouponFee":0,"Created":"2018-05-31 17:00:36","CreditCardFee":null,"CrossBondedDeclare":false,"DelayCreateDelivery":0,"DiscountFee":null,"EncryptAlipayId":null,"EndTime":null,"EsDate":null,"EsRange":null,"EstConTime":null,"EtPlateNumber":null,"EtSerTime":null,"EtShopId":0,"EtShopName":null,"EtType":null,"EtVerifiedShopName":null,"EticketExt":null,"EticketServiceAddr":null,"ExpressAgencyFee":null,"ForbidConsign":0,"HasBuyerMessage":false,"HasPostFee":false,"HasYfx":false,"HkBirthday":null,"HkCardCode":null,"HkCardType":null,"HkChinaName":null,"HkEnName":null,"HkFlightDate":null,"HkFlightNo":null,"HkGender":null,"HkPickup":null,"HkPickupId":null,"Identity":null,"Iid":null,"InvoiceKind":null,"InvoiceName":null,"InvoiceType":null,"Is3D":false,"IsBrandSale":false,"IsDaixiao":false,"IsForceWlb":false,"IsLgtype":false,"IsPartConsign":false,"IsShShip":false,"IsWt":false,"LgAging":null,"LgAgingType":null,"MarkDesc":null,"Market":null,"Modified":null,"Num":2,"NumIid":789789,"NutFeature":null,"O2o":null,"O2oDelivery":null,"O2oEtOrderId":null,"O2oGuideId":null,"O2oGuideName":null,"O2oOutTradeId":null,"O2oShopId":null,"O2oShopName":null,"O2oSnatchStatus":null,"O2oStepOrderId":null,"O2oStepTradeDetail":null,"O2oVoucherPrice":null,"Obs":null,"OfpHold":0,"OmniAttr":null,"OmniParam":null,"OmnichannelParam":null,"OrderTaxFee":null,"OrderTaxPromotionFee":null,"Orders":[{"AdjustFee":null,"AssemblyItem":null,"AssemblyPrice":null,"AssemblyRela":null,"BindOid":0,"BindOids":null,"BuyerNick":null,"BuyerRate":false,"CalPenalty":null,"CarStoreCode":null,"CarStoreName":null,"CarTaker":null,"CarTakerId":null,"CarTakerIdNum":null,"CarTakerPhone":null,"Cid":0,"ClCarTaker":null,"ClCarTakerIDNum":null,"ClCarTakerIdNum":null,"ClCarTakerPhone":null,"ClDownPayment":null,"ClDownPaymentRatio":null,"ClInstallmentNum":null,"ClMonthPayment":null,"ClServiceFee":null,"ClTailPayment":null,"ComboId":null,"ConsignTime":null,"Customization":null,"DiscountFee":null,"DivideOrderFee":null,"DownPayment":null,"DownPaymentRatio":null,"EndTime":null,"EstimateConTime":null,"EtPlateNumber":null,"EtSerTime":null,"EtShopName":null,"EtVerifiedShopName":null,"FStatus":null,"FTerm":null,"FType":null,"Iid":null,"InstallmentNum":null,"InvType":null,"InvoiceNo":null,"IsDaixiao":false,"IsOversold":false,"IsServiceOrder":false,"IsShShip":false,"IsWww":false,"ItemMealId":0,"ItemMealName":null,"ItemMemo":null,"LogisticsCompany":null,"MdFee":null,"MdQualification":null,"Modified":null,"MonthPayment":null,"Num":0,"NumIid":0,"O2oEtOrderId":null,"Oid":0,"OidStr":null,"OrderAttr":null,"OrderFrom":null,"OutUniqueId":null,"OuterIid":null,"OuterSkuId":null,"PartMjzDiscount":null,"Payment":null,"Penalty":null,"PicPath":null,"Price":null,"RefundId":0,"RefundStatus":null,"SellerNick":null,"SellerRate":false,"SellerType":null,"ServiceFee":null,"Shipper":null,"ShippingType":null,"SkuId":null,"SkuPropertiesName":null,"Snapshot":null,"SnapshotUrl":null,"Status":null,"StoreCode":null,"SubOrderTaxFee":null,"SubOrderTaxPromotionFee":null,"SubOrderTaxRate":null,"TailPayment":null,"TicketExpdateKey":null,"TicketOuterId":null,"TimeoutActionTime":null,"Title":"刺激战场代练绝地求生手游代打单双四排位上分吃鸡评分金币信誉分","TmserSpuCode":null,"TotalFee":null,"Type":null,"WsBankApplyNo":null,"Xxx":null,"ZhengjiStatus":null}],"OsDate":null,"OsRange":null,"PaidCouponFee":null,"PayTime":"2018-05-31 17:02:50","Payment":"20.00","PccAf":0,"PicPath":null,"PointFee":0,"PostFee":null,"PostGateDeclare":false,"Price":"10.00","Promotion":null,"PromotionDetails":[],"RealPointFee":0,"ReceivedPayment":null,"ReceiverAddress":"所在区/服:1\r\n角色名:1\r\n备注:","ReceiverCity":null,"ReceiverCountry":null,"ReceiverDistrict":null,"ReceiverMobile":null,"ReceiverName":null,"ReceiverPhone":null,"ReceiverState":null,"ReceiverTown":null,"ReceiverZip":null,"RxAuditStatus":null,"SellerAlipayNo":null,"SellerCanRate":false,"SellerCodFee":null,"SellerEmail":null,"SellerFlag":0,"SellerMemo":null,"SellerMobile":null,"SellerName":null,"SellerNick":"斗奇网游专营店","SellerPhone":null,"SellerRate":false,"SendTime":null,"ServiceOrders":[],"ServiceTags":[],"ShareGroupHold":0,"ShippingType":null,"ShopCode":null,"ShopPick":null,"Sid":null,"Snapshot":null,"SnapshotUrl":null,"Status":"WAIT_SELLER_SEND_GOODS","StepPaidFee":null,"StepTradeStatus":null,"TeamBuyHold":0,"Tid":167801012253294476,"TidStr":"167801012253294476","TimeoutActionTime":null,"Title":null,"TopHold":0,"Toptype":0,"TotalFee":null,"TradeAttr":null,"TradeExt":null,"TradeFrom":"WAP,WAP","TradeMemo":null,"TradeSource":null,"Type":"fixed","YfxFee":null,"YfxId":null,"YfxType":null,"ZeroPurchase":false}';
            $arr = json_decode($b, true);
            $itd = generateOrderNo();
            $arr['Tid'] = $itd;
            $arr['TidStr'] = $itd;

            $data = [
                'query' => [
                    'data' => taobaoAesEncrypt(json_encode($arr))
                ],
            ];
            // 发送 post 请求
            $promise = $client->requestAsync('POST', 'http://js.qsios.com/api/taobao/store', $data);
            $promise->then(
                function (ResponseInterface $res) {
                    myLog('test-send-success', [$res->getBody()->getContents()]);
                },
                function (RequestException $e) {
                    myLog('test-send-fail', [$e->getMessage()]);
                }
            );
            $promise->wait();
        }



die;
        $client = new Client();
        $requests = function () {
            $uri = 'http://js.qsios.com/api/taobao/store';
            for ($i = 0; $i < 1; $i++) {

                $b= '{"AcookieId":null,"AdjustFee":null,"AlipayId":0,"AlipayNo":null,"AlipayPoint":0,"AlipayUrl":null,"AlipayWarnMsg":null,"AreaId":null,"ArriveCutTime":null,"ArriveInterval":0,"Assembly":null,"AsyncModified":null,"AvailableConfirmFee":null,"BuyerAlipayNo":"1-6265030676","BuyerArea":"密苏里州圣路易斯市Charter通信公司","BuyerCodFee":null,"BuyerEmail":"","BuyerFlag":0,"BuyerIp":"NzEuODQuNTAuNDY=","BuyerMemo":null,"BuyerMessage":null,"BuyerNick":"joseph8047","BuyerObtainPointFee":0,"BuyerRate":false,"CanRate":false,"CodFee":null,"CodStatus":null,"CommissionFee":null,"ConsignInterval":0,"ConsignTime":null,"CouponFee":0,"Created":"2018-05-31 17:00:36","CreditCardFee":null,"CrossBondedDeclare":false,"DelayCreateDelivery":0,"DiscountFee":null,"EncryptAlipayId":null,"EndTime":null,"EsDate":null,"EsRange":null,"EstConTime":null,"EtPlateNumber":null,"EtSerTime":null,"EtShopId":0,"EtShopName":null,"EtType":null,"EtVerifiedShopName":null,"EticketExt":null,"EticketServiceAddr":null,"ExpressAgencyFee":null,"ForbidConsign":0,"HasBuyerMessage":false,"HasPostFee":false,"HasYfx":false,"HkBirthday":null,"HkCardCode":null,"HkCardType":null,"HkChinaName":null,"HkEnName":null,"HkFlightDate":null,"HkFlightNo":null,"HkGender":null,"HkPickup":null,"HkPickupId":null,"Identity":null,"Iid":null,"InvoiceKind":null,"InvoiceName":null,"InvoiceType":null,"Is3D":false,"IsBrandSale":false,"IsDaixiao":false,"IsForceWlb":false,"IsLgtype":false,"IsPartConsign":false,"IsShShip":false,"IsWt":false,"LgAging":null,"LgAgingType":null,"MarkDesc":null,"Market":null,"Modified":null,"Num":2,"NumIid":789789,"NutFeature":null,"O2o":null,"O2oDelivery":null,"O2oEtOrderId":null,"O2oGuideId":null,"O2oGuideName":null,"O2oOutTradeId":null,"O2oShopId":null,"O2oShopName":null,"O2oSnatchStatus":null,"O2oStepOrderId":null,"O2oStepTradeDetail":null,"O2oVoucherPrice":null,"Obs":null,"OfpHold":0,"OmniAttr":null,"OmniParam":null,"OmnichannelParam":null,"OrderTaxFee":null,"OrderTaxPromotionFee":null,"Orders":[{"AdjustFee":null,"AssemblyItem":null,"AssemblyPrice":null,"AssemblyRela":null,"BindOid":0,"BindOids":null,"BuyerNick":null,"BuyerRate":false,"CalPenalty":null,"CarStoreCode":null,"CarStoreName":null,"CarTaker":null,"CarTakerId":null,"CarTakerIdNum":null,"CarTakerPhone":null,"Cid":0,"ClCarTaker":null,"ClCarTakerIDNum":null,"ClCarTakerIdNum":null,"ClCarTakerPhone":null,"ClDownPayment":null,"ClDownPaymentRatio":null,"ClInstallmentNum":null,"ClMonthPayment":null,"ClServiceFee":null,"ClTailPayment":null,"ComboId":null,"ConsignTime":null,"Customization":null,"DiscountFee":null,"DivideOrderFee":null,"DownPayment":null,"DownPaymentRatio":null,"EndTime":null,"EstimateConTime":null,"EtPlateNumber":null,"EtSerTime":null,"EtShopName":null,"EtVerifiedShopName":null,"FStatus":null,"FTerm":null,"FType":null,"Iid":null,"InstallmentNum":null,"InvType":null,"InvoiceNo":null,"IsDaixiao":false,"IsOversold":false,"IsServiceOrder":false,"IsShShip":false,"IsWww":false,"ItemMealId":0,"ItemMealName":null,"ItemMemo":null,"LogisticsCompany":null,"MdFee":null,"MdQualification":null,"Modified":null,"MonthPayment":null,"Num":0,"NumIid":0,"O2oEtOrderId":null,"Oid":0,"OidStr":null,"OrderAttr":null,"OrderFrom":null,"OutUniqueId":null,"OuterIid":null,"OuterSkuId":null,"PartMjzDiscount":null,"Payment":null,"Penalty":null,"PicPath":null,"Price":null,"RefundId":0,"RefundStatus":null,"SellerNick":null,"SellerRate":false,"SellerType":null,"ServiceFee":null,"Shipper":null,"ShippingType":null,"SkuId":null,"SkuPropertiesName":null,"Snapshot":null,"SnapshotUrl":null,"Status":null,"StoreCode":null,"SubOrderTaxFee":null,"SubOrderTaxPromotionFee":null,"SubOrderTaxRate":null,"TailPayment":null,"TicketExpdateKey":null,"TicketOuterId":null,"TimeoutActionTime":null,"Title":"刺激战场代练绝地求生手游代打单双四排位上分吃鸡评分金币信誉分","TmserSpuCode":null,"TotalFee":null,"Type":null,"WsBankApplyNo":null,"Xxx":null,"ZhengjiStatus":null}],"OsDate":null,"OsRange":null,"PaidCouponFee":null,"PayTime":"2018-05-31 17:02:50","Payment":"20.00","PccAf":0,"PicPath":null,"PointFee":0,"PostFee":null,"PostGateDeclare":false,"Price":"10.00","Promotion":null,"PromotionDetails":[],"RealPointFee":0,"ReceivedPayment":null,"ReceiverAddress":"所在区/服:1\r\n角色名:1\r\n备注:","ReceiverCity":null,"ReceiverCountry":null,"ReceiverDistrict":null,"ReceiverMobile":null,"ReceiverName":null,"ReceiverPhone":null,"ReceiverState":null,"ReceiverTown":null,"ReceiverZip":null,"RxAuditStatus":null,"SellerAlipayNo":null,"SellerCanRate":false,"SellerCodFee":null,"SellerEmail":null,"SellerFlag":0,"SellerMemo":null,"SellerMobile":null,"SellerName":null,"SellerNick":"斗奇网游专营店","SellerPhone":null,"SellerRate":false,"SendTime":null,"ServiceOrders":[],"ServiceTags":[],"ShareGroupHold":0,"ShippingType":null,"ShopCode":null,"ShopPick":null,"Sid":null,"Snapshot":null,"SnapshotUrl":null,"Status":"WAIT_SELLER_SEND_GOODS","StepPaidFee":null,"StepTradeStatus":null,"TeamBuyHold":0,"Tid":167801012253294476,"TidStr":"167801012253294476","TimeoutActionTime":null,"Title":null,"TopHold":0,"Toptype":0,"TotalFee":null,"TradeAttr":null,"TradeExt":null,"TradeFrom":"WAP,WAP","TradeMemo":null,"TradeSource":null,"Type":"fixed","YfxFee":null,"YfxId":null,"YfxType":null,"ZeroPurchase":false}';
                $arr = json_decode($b, true);
                $itd = generateOrderNo();
                $arr['Tid'] = $itd;
                $arr['Tid'] = $itd;

                yield new Request('POST', $uri, [], [
                    'form_params' => [
                        'data' => taobaoAesEncrypt(json_encode($arr))
                    ]
                ]);
            }
        };

        $pool = new Pool($client, $requests(1), [
            'concurrency' => 5,
            'fulfilled' => function ($response, $index) {
                // 每个请求成功时执行
                myLog('test-send-success', [$response->getBody()]);
            },
            'rejected' => function ($reason, $index) {
                // 每个请求失败时执行
                myLog('test-send-fail', [$reason->getBody()]);
            },
        ]);
        // 开始传输并创建一个 promise
        $promise = $pool->promise();
        // 等待请求池完成
        $promise->wait();
        die;



        $type = $this->argument('type');
      dd(  Show91Controller::getMessage([
          'show91_order_no' => 'ORD180521105449004776'
        ]));
        if ($type == 1) {
            $this->shos91();
        } elseif ($type == 2) {
            $this->dd373();
        } elseif ($type == 3) {
            $this->my();
        } else {
            $this->syncDD373();
        }

    }

    /**
     * dd373订单查询
     */
    public function dd373()
    {
        $allOrder = \App\Models\Order::where('service_id', 4)->where('gainer_primary_user_id', 8739)->get();

        foreach ($allOrder as $item) {
            $show91OrderNO = OrderDetail::where('order_no', $item->no)->where('field_name', 'dd373_order_no')->first();
            if (isset($show91OrderNO->field_value) && !empty($show91OrderNO->field_value)) {
                // 按接单方取订单详情
                try {
                    $orderDetail = DD373Controller::orderDetail(['dd373_order_no' => $show91OrderNO->field_value]);
                    if ($orderDetail['data']) {
                        myLog('dd373-show-order-query', [
                            '第三方' => $orderDetail['data']['platformOrderNo'],
                            '我们订单号' => $item->no,
                            '第三方订单号' => $show91OrderNO->field_value,
                            '第三方状态' => isset($this->dd373[$orderDetail['data']['orderStatus']]) ? $this->dd373[$orderDetail['data']['orderStatus']] : '',
                            '我们状态' => config('order.status_leveling')[$item->status],
                            '第三方价格' => $orderDetail['data']['price'],
                            '我们价格' => $item->amount,
                            '价格' => $item->amount == $orderDetail['data']['price'] ? '是' : '否'
                        ]);
                    }
                } catch (\Exception $exception) {
                    myLog('dd373-show-order-query-err', [$item->no, '平台' => $show91OrderNO->field_value, '状态' => config('order.status_leveling')[$item->status]]);
                }

            }
        }
    }

    public function shos91()
    {
        $allOrder = \App\Models\Order::where('service_id', 4)->where('gainer_primary_user_id', 8456)->get();

        foreach ($allOrder as $item) {
            $show91OrderNO = OrderDetail::where('order_no', $item->no)->where('field_name', 'show91_order_no')->first();
            if (isset($show91OrderNO->field_value) && !empty($show91OrderNO->field_value)) {
                // 如果91订单状态是接单，调我们自己接单接口，如果不是记录一下他们状态
                $orderDetail = Show91::orderDetail(['oid' => $show91OrderNO->field_value]);
                // 91 是待验收
                if ($orderDetail['data']) {
                    myLog('91-show-order-query', [
                        '我们订单号' => $item->no,
                        '91订单号' => $show91OrderNO->field_value,
                        '91状态' => isset($this->show91Status[$orderDetail['data']['order_status']]) ? $this->show91Status[$orderDetail['data']['order_status']] : '',
                        '我们状态' => config('order.status_leveling')[$item->status],
                        '91从格' => $orderDetail['data']['price'],
                        '我们价格' => $item->amount,
                        '价格' => $item->amount == $orderDetail['data']['price'] ? '是' : '否'
                    ]);
                }
            }
        }
    }

    public function my()
    {
        $allOrder = \App\Models\Order::where('service_id', 4)->where('gainer_primary_user_id', 8737)->get();

        foreach ($allOrder as $item) {
            $show91OrderNO = OrderDetail::where('order_no', $item->no)->where('field_name', 'mayi_order_no')->first();
            if (isset($show91OrderNO->field_value) && !empty($show91OrderNO->field_value)) {
                // 按接单方取订单详情
                try {
                    $orderDetail = MayiDailianController::orderDetail(['mayi_order_no' => $show91OrderNO->field_value]);
                    if ($orderDetail['data']) {
                        myLog('my-show-order-query', [
                            '第三方' => $item->no,
                            '我们订单号' => $item->no,
                            '第三方订单号' => $show91OrderNO->field_value,
                            '第三方状态' => $orderDetail['data']['status_type'],
                            '我们状态' => config('order.status_leveling')[$item->status],
                            '第三方价格' => $orderDetail['data']['paymoney'],
                            '我们价格' => $item->amount,
                            '价格' => $item->amount == $orderDetail['data']['paymoney'] ? '是' : '否'
                        ]);
                    }
                } catch (\Exception $exception) {
                    myLog('my-show-order-query-err', [$item->no]);
                }

            }
        }
    }

    public function syncDD373()
    {
        $allOrder = \App\Models\Order::where('service_id', 4)->where('status', 1)->get();
        foreach ($allOrder as $item) {
            $detail = $this->orderRepository->levelingDetail($item->no);
            if(isset($detail['dd373_order_no'])) {
                DD373Controller::updateOrder($detail);
            } else {
                myLog('sync', [$item->no]);
            }
        }
    }
}

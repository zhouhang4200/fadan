<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

class TestApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'TestApi';

    protected $orderRepository;
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $totalCount = 10000;
        $client = new Client();
        myLog('time', [date('Y-m-d H:i:s')]);
        $requests = function ($total) use ($client, $totalCount) {
            for($i = 0; $i < $totalCount; $i++) {
                $uri = 'http://js.qsios.com/api/taobao/store';
                yield function() use ($client, $uri) {

                    $b= '{"AcookieId":null,"AdjustFee":null,"AlipayId":0,"AlipayNo":null,"AlipayPoint":0,"AlipayUrl":null,"AlipayWarnMsg":null,"AreaId":null,"ArriveCutTime":null,"ArriveInterval":0,"Assembly":null,"AsyncModified":null,"AvailableConfirmFee":null,"BuyerAlipayNo":"1-6265030676","BuyerArea":"密苏里州圣路易斯市Charter通信公司","BuyerCodFee":null,"BuyerEmail":"","BuyerFlag":0,"BuyerIp":"NzEuODQuNTAuNDY=","BuyerMemo":null,"BuyerMessage":null,"BuyerNick":"joseph8047","BuyerObtainPointFee":0,"BuyerRate":false,"CanRate":false,"CodFee":null,"CodStatus":null,"CommissionFee":null,"ConsignInterval":0,"ConsignTime":null,"CouponFee":0,"Created":"2018-05-31 17:00:36","CreditCardFee":null,"CrossBondedDeclare":false,"DelayCreateDelivery":0,"DiscountFee":null,"EncryptAlipayId":null,"EndTime":null,"EsDate":null,"EsRange":null,"EstConTime":null,"EtPlateNumber":null,"EtSerTime":null,"EtShopId":0,"EtShopName":null,"EtType":null,"EtVerifiedShopName":null,"EticketExt":null,"EticketServiceAddr":null,"ExpressAgencyFee":null,"ForbidConsign":0,"HasBuyerMessage":false,"HasPostFee":false,"HasYfx":false,"HkBirthday":null,"HkCardCode":null,"HkCardType":null,"HkChinaName":null,"HkEnName":null,"HkFlightDate":null,"HkFlightNo":null,"HkGender":null,"HkPickup":null,"HkPickupId":null,"Identity":null,"Iid":null,"InvoiceKind":null,"InvoiceName":null,"InvoiceType":null,"Is3D":false,"IsBrandSale":false,"IsDaixiao":false,"IsForceWlb":false,"IsLgtype":false,"IsPartConsign":false,"IsShShip":false,"IsWt":false,"LgAging":null,"LgAgingType":null,"MarkDesc":null,"Market":null,"Modified":null,"Num":2,"NumIid":789789,"NutFeature":null,"O2o":null,"O2oDelivery":null,"O2oEtOrderId":null,"O2oGuideId":null,"O2oGuideName":null,"O2oOutTradeId":null,"O2oShopId":null,"O2oShopName":null,"O2oSnatchStatus":null,"O2oStepOrderId":null,"O2oStepTradeDetail":null,"O2oVoucherPrice":null,"Obs":null,"OfpHold":0,"OmniAttr":null,"OmniParam":null,"OmnichannelParam":null,"OrderTaxFee":null,"OrderTaxPromotionFee":null,"Orders":[{"AdjustFee":null,"AssemblyItem":null,"AssemblyPrice":null,"AssemblyRela":null,"BindOid":0,"BindOids":null,"BuyerNick":null,"BuyerRate":false,"CalPenalty":null,"CarStoreCode":null,"CarStoreName":null,"CarTaker":null,"CarTakerId":null,"CarTakerIdNum":null,"CarTakerPhone":null,"Cid":0,"ClCarTaker":null,"ClCarTakerIDNum":null,"ClCarTakerIdNum":null,"ClCarTakerPhone":null,"ClDownPayment":null,"ClDownPaymentRatio":null,"ClInstallmentNum":null,"ClMonthPayment":null,"ClServiceFee":null,"ClTailPayment":null,"ComboId":null,"ConsignTime":null,"Customization":null,"DiscountFee":null,"DivideOrderFee":null,"DownPayment":null,"DownPaymentRatio":null,"EndTime":null,"EstimateConTime":null,"EtPlateNumber":null,"EtSerTime":null,"EtShopName":null,"EtVerifiedShopName":null,"FStatus":null,"FTerm":null,"FType":null,"Iid":null,"InstallmentNum":null,"InvType":null,"InvoiceNo":null,"IsDaixiao":false,"IsOversold":false,"IsServiceOrder":false,"IsShShip":false,"IsWww":false,"ItemMealId":0,"ItemMealName":null,"ItemMemo":null,"LogisticsCompany":null,"MdFee":null,"MdQualification":null,"Modified":null,"MonthPayment":null,"Num":0,"NumIid":0,"O2oEtOrderId":null,"Oid":0,"OidStr":null,"OrderAttr":null,"OrderFrom":null,"OutUniqueId":null,"OuterIid":null,"OuterSkuId":null,"PartMjzDiscount":null,"Payment":null,"Penalty":null,"PicPath":null,"Price":null,"RefundId":0,"RefundStatus":null,"SellerNick":null,"SellerRate":false,"SellerType":null,"ServiceFee":null,"Shipper":null,"ShippingType":null,"SkuId":null,"SkuPropertiesName":null,"Snapshot":null,"SnapshotUrl":null,"Status":null,"StoreCode":null,"SubOrderTaxFee":null,"SubOrderTaxPromotionFee":null,"SubOrderTaxRate":null,"TailPayment":null,"TicketExpdateKey":null,"TicketOuterId":null,"TimeoutActionTime":null,"Title":"刺激战场代练绝地求生手游代打单双四排位上分吃鸡评分金币信誉分","TmserSpuCode":null,"TotalFee":null,"Type":null,"WsBankApplyNo":null,"Xxx":null,"ZhengjiStatus":null}],"OsDate":null,"OsRange":null,"PaidCouponFee":null,"PayTime":"2018-05-31 17:02:50","Payment":"20.00","PccAf":0,"PicPath":null,"PointFee":0,"PostFee":null,"PostGateDeclare":false,"Price":"10.00","Promotion":null,"PromotionDetails":[],"RealPointFee":0,"ReceivedPayment":null,"ReceiverAddress":"所在区/服:1\r\n角色名:1\r\n备注:","ReceiverCity":null,"ReceiverCountry":null,"ReceiverDistrict":null,"ReceiverMobile":null,"ReceiverName":null,"ReceiverPhone":null,"ReceiverState":null,"ReceiverTown":null,"ReceiverZip":null,"RxAuditStatus":null,"SellerAlipayNo":null,"SellerCanRate":false,"SellerCodFee":null,"SellerEmail":null,"SellerFlag":0,"SellerMemo":null,"SellerMobile":null,"SellerName":null,"SellerNick":"斗奇网游专营店","SellerPhone":null,"SellerRate":false,"SendTime":null,"ServiceOrders":[],"ServiceTags":[],"ShareGroupHold":0,"ShippingType":null,"ShopCode":null,"ShopPick":null,"Sid":null,"Snapshot":null,"SnapshotUrl":null,"Status":"WAIT_SELLER_SEND_GOODS","StepPaidFee":null,"StepTradeStatus":null,"TeamBuyHold":0,"Tid":167801012253294476,"TidStr":"167801012253294476","TimeoutActionTime":null,"Title":null,"TopHold":0,"Toptype":0,"TotalFee":null,"TradeAttr":null,"TradeExt":null,"TradeFrom":"WAP,WAP","TradeMemo":null,"TradeSource":null,"Type":"fixed","YfxFee":null,"YfxId":null,"YfxType":null,"ZeroPurchase":false}';
                    $arr = json_decode($b, true);
                    $itd = generateOrderNo();
                    $arr['Tid'] = $itd;
                    $arr['TidStr'] = $itd;

                    return $client->postAsync($uri, [
                        'query' => [
                            'data' => taobaoAesEncrypt(json_encode($arr))
                        ],
                    ]);
                };
            }
        };

        $pool = new Pool($client, $requests($totalCount), [
            'concurrency' => 50, // 同时并发的数量
            'fulfilled'   => function ($response, $index){
                $res = json_decode($response->getBody()->getContents());
                $this->info("请求第 $index 个请求，用户  ID 为：" .$res->message);
            },
            'rejected' => function ($reason, $index){
                $this->error("rejected" );
                $this->error("rejected reason: " . $reason );
            },
        ]);

        // 开始发送请求
        $promise = $pool->promise();
        $promise->wait();
        myLog('time', [date('Y-m-d H:i:s')]);
    }

}

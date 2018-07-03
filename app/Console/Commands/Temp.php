<?php

namespace App\Console\Commands;

use App\Events\NotificationEvent;
use App\Events\OrderApplyComplete;
use App\Events\OrderArbitrationing;
use App\Events\OrderRevoking;
use App\Exceptions\DailianException;
use App\Extensions\Asset\Facades\Asset;
use App\Extensions\Asset\Income;
use App\Extensions\Dailian\Controllers\Complete;
use App\Extensions\Dailian\Controllers\DailianFactory;
use App\Extensions\Dailian\Controllers\Delete;
use App\Extensions\Dailian\Controllers\ForceRevoke;
use App\Extensions\Dailian\Controllers\Revoked;
use App\Extensions\Dailian\Controllers\Revoking;
use App\Models\OrderAttachment;
use App\Models\OrderDetail;
use App\Models\TaobaoTrade;
use App\Repositories\Frontend\OrderAttachmentRepository;
use App\Repositories\Frontend\OrderDetailRepository;
use App\Services\DailianMama;
use App\Services\KamenOrderApi;
use App\Services\Leveling\DD373Controller;
use App\Services\Leveling\MayiDailianController;
use App\Services\Leveling\Show91Controller;
use App\Services\Show91;
use App\Services\SmSApi;
use App\Services\TmallOrderApi;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Log, Config, Weight, Order;
use LogisticsDummySendRequest;
use OSS\Core\OssException;
use OSS\OssClient;
use TopClient;
use TradeFullinfoGetRequest;
use TraderatesGetRequest;
use Illuminate\Support\Facades\Mail;

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
     * 获取订单，订单详情，协商仲裁的所有信息
     * @param $orderNo
     * @return array
     * @throws DailianException
     */
    public function getOrderAndOrderDetailAndLevelingConsult($orderNo)
    {
        $collectionArr =  \DB::select("
            SELECT a.order_no, 
                MAX(CASE WHEN a.field_name='region' THEN a.field_value ELSE '' END) AS region,
                MAX(CASE WHEN a.field_name='serve' THEN a.field_value ELSE '' END) AS serve,
                MAX(CASE WHEN a.field_name='account' THEN a.field_value ELSE '' END) AS account,
                MAX(CASE WHEN a.field_name='password' THEN a.field_value ELSE '' END) AS password,
                MAX(CASE WHEN a.field_name='source_order_no' THEN a.field_value ELSE '' END) AS source_order_no,
                MAX(CASE WHEN a.field_name='role' THEN a.field_value ELSE '' END) AS role,
                MAX(CASE WHEN a.field_name='game_leveling_type' THEN a.field_value ELSE '' END) AS game_leveling_type,
                MAX(CASE WHEN a.field_name='game_leveling_title' THEN a.field_value ELSE '' END) AS game_leveling_title,
                MAX(CASE WHEN a.field_name='game_leveling_instructions' THEN a.field_value ELSE '' END) AS game_leveling_instructions,
                MAX(CASE WHEN a.field_name='game_leveling_requirements' THEN a.field_value ELSE '' END) AS game_leveling_requirements,
                MAX(CASE WHEN a.field_name='auto_unshelve_time' THEN a.field_value ELSE '' END) AS auto_unshelve_time,
                MAX(CASE WHEN a.field_name='game_leveling_amount' THEN a.field_value ELSE '' END) AS game_leveling_amount,
                MAX(CASE WHEN a.field_name='game_leveling_day' THEN a.field_value ELSE '' END) AS game_leveling_day,
                MAX(CASE WHEN a.field_name='game_leveling_hour' THEN a.field_value ELSE '' END) AS game_leveling_hour,
                MAX(CASE WHEN a.field_name='security_deposit' THEN a.field_value ELSE '' END) AS security_deposit,
                MAX(CASE WHEN a.field_name='efficiency_deposit' THEN a.field_value ELSE '' END) AS efficiency_deposit,
                MAX(CASE WHEN a.field_name='user_phone' THEN a.field_value ELSE '' END) AS user_phone,
                MAX(CASE WHEN a.field_name='user_qq' THEN a.field_value ELSE '' END) AS user_qq,
                MAX(CASE WHEN a.field_name='source_price' THEN a.field_value ELSE '' END) AS source_price,
                MAX(CASE WHEN a.field_name='client_name' THEN a.field_value ELSE '' END) AS client_name,
                MAX(CASE WHEN a.field_name='client_phone' THEN a.field_value ELSE '' END) AS client_phone,
                MAX(CASE WHEN a.field_name='client_qq' THEN a.field_value ELSE '' END) AS client_qq,
                MAX(CASE WHEN a.field_name='client_wang_wang' THEN a.field_value ELSE '' END) AS client_wang_wang,
                MAX(CASE WHEN a.field_name='game_leveling_require_day' THEN a.field_value ELSE '' END) AS game_leveling_require_day,
                MAX(CASE WHEN a.field_name='game_leveling_require_hour' THEN a.field_value ELSE '' END) AS game_leveling_require_hour,
                MAX(CASE WHEN a.field_name='customer_service_remark' THEN a.field_value ELSE '' END) AS customer_service_remark,
                MAX(CASE WHEN a.field_name='receiving_time' THEN a.field_value ELSE '' END) AS receiving_time,
                MAX(CASE WHEN a.field_name='checkout_time' THEN a.field_value ELSE '' END) AS checkout_time,
                MAX(CASE WHEN a.field_name='customer_service_name' THEN a.field_value ELSE '' END) AS customer_service_name,
                MAX(CASE WHEN a.field_name='third_order_no' THEN a.field_value ELSE '' END) AS third_order_no,
                MAX(CASE WHEN a.field_name='third' THEN a.field_value ELSE '' END) AS third,
                MAX(CASE WHEN a.field_name='poundage' THEN a.field_value ELSE '' END) AS poundage,
                MAX(CASE WHEN a.field_name='price_markup' THEN a.field_value ELSE '' END) AS price_markup,
                MAX(CASE WHEN a.field_name='show91_order_no' THEN a.field_value ELSE '' END) AS show91_order_no,
                MAX(CASE WHEN a.field_name='mayi_order_no' THEN a.field_value ELSE '' END) AS mayi_order_no,
                MAX(CASE WHEN a.field_name='dd373_order_no' THEN a.field_value ELSE '' END) AS dd373_order_no,
                MAX(CASE WHEN a.field_name='dailianmama_order_no' THEN a.field_value ELSE '' END) AS dailianmama_order_no,
                MAX(CASE WHEN a.field_name='wanzi_order_no' THEN a.field_value ELSE '' END) AS wanzi_order_no,
                MAX(CASE WHEN a.field_name='hatchet_man_qq' THEN a.field_value ELSE '' END) AS hatchet_man_qq,
                MAX(CASE WHEN a.field_name='hatchet_man_phone' THEN a.field_value ELSE '' END) AS hatchet_man_phone,
                MAX(CASE WHEN a.field_name='order_password' THEN a.field_value ELSE '' END) AS order_password,
                MAX(CASE WHEN a.field_name='game_leveling_requirements_template' THEN a.field_value ELSE '' END) AS game_leveling_requirements_template,
                b.no,
                b.status as order_status,
                b.created_at as order_created_at,
                b.amount,
                b.creator_user_id, 
                b.creator_primary_user_id, 
                b.game_id, 
                b.gainer_user_id, 
                b.gainer_primary_user_id,
                c.user_id,
                c.amount AS pay_amount,
                c.deposit,
                c.api_amount,
                c.api_deposit,
                c.api_service,
                c.status,
                c.consult,
                c.complain,
                c.complete,
                c.remark,
                c.revoke_message,
                c.complain_message
            FROM order_details a
            LEFT JOIN orders b
            ON a.order_no = b.no
            LEFT JOIN leveling_consults c
            ON a.order_no = c.order_no
            WHERE a.order_no='$orderNo'");

        $collection = is_array($collectionArr) ? $collectionArr[0] : '';

        if (empty($collection) || ! $collection->no) {
            throw new DailianException('订单号错误');
        }

        return (array) $collection;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        dd(Show91Controller::orderDetail([
            'show91_order_no' => 'ORD180703131312436741'
        ]));
        $order = [
            '2018070302195800000033',
            '2018070313020300000175',
            '2018070313093000000184',
            '2018070313195500000193'
        ];
        foreach ($order as $key => $item) {
            $this->forceRevoke($item, 1);
        }

        die;

        $no = $this->argument('no');
        $user = $this->argument('user');
        dd((new Revoked())->run($no, '8711', 0));

        $this->show91Order();die;
        $a = openssl_encrypt('{"order_no":"2018061203083200000067","game_name":"\u523a\u6fc0\u6218\u573a","game_region":"IOS\u5fae\u4fe1","game_serve":"\u9ed8\u8ba4\u670d","game_role":"\u5b8b\u5c0f\u5b9d\u98de\u673a","game_account":"xiaohuiji1990","game_password":"1","game_leveling_type":"\u6392\u4f4d","game_leveling_title":"\u7b2c\u4e09\u4eba\u79f0\u56db\u6392\u966a\u73a9\u5403\u9e21\u56db\u76d8\u53f7\u4e3b\u94c2\u91d1","game_leveling_price":"50.00","game_leveling_day":"1","game_leveling_hour":"0","game_leveling_security_deposit":"15","game_leveling_efficiency_deposit":"15","game_leveling_requirements":"\u4e0d\u53ef\u4f7f\u7528\u5916\u6302+\u4efb\u4f55\u624b\u67c4\u4ee3\u6253\uff0c\u4f7f\u7528\u4f1a\u5bfc\u81f4\u5c01\u53f7\u3001\u53d1\u73b0\u4f7f\u7528\u8005\u5168\u90e8\u94b1\u6263 \u7edd\u5730\u6c42\u751f+\u8352\u91ceQQ\u7fa4\uff1a710393653\uff0cQQ\u70ab\u821e\u624b\u6e38QQ\u7fa4\uff1a611756448\uff0cQQ\u98de\u8f66\u7fa4\uff1a881400\uff0c \u8054\u7cfb\u4eba\u624b\u673a\u662f\u53f7\u4e3b\u624b\u673a\uff0c\u9700\u8981\u9a8c\u8bc1\u81ea\u5df1\u7535\u8bdd\u8054\u7cfb\u53f7\u4e3b\u62ff\u9a8c\u8bc1\uff0c\u5f02\u5730\u51bb\u7ed3\u3001\u5bc6\u7801\u9519\u8bef\u4e5f\u81ea\u5df1\u53bb\u8981\u65b0\u5bc6\u7801\uff0c\u5176\u4ed6\u4e0d\u51c6\u591a\u8bf4\u591a\u95ee\u3001\u53ea\u9700\u8981\u4ee3\u7ec3\uff0c\u4e0d\u9700\u8054\u7cfb\u53f7\u4e3b\u8981\u7ea2\u5305\u7ed9\u4ee3\u7ec3\u8d39\u63a5 \u79c1\u5355\u53d1\u73b0\u6263\u9664\u53cc\u91d1\u7ec8\u751f\u51bb\u7ed3\u5e73\u53f0\u5e10\u53f7\u52a0\u6263\u5168\u90e8\u5e73\u53f0\u91d1\u989d\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605 \u3002\u9a8c\u6536\u3001\u63a5\u9519\u5355\uff0c\u9000\u5355\u8054\u7cfbQQ3417584093\n \u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605\u2605 \u7279\u522b\u6ce8\u610f\uff1a\u7edd\u5730\u6c42\u751f\u7edd\u5bf9\u4e0d\u80fd\u7528\u5916\u6302\u548c\u6a21\u62df\u5668\u6253\uff0c\u53d1\u73b0\u9a6c\u4e0a\u76f4\u63a5\u51bb\u7ed3\u8d26\u6237 1\u3001\u4ee3\u7ec3\u6240\u5f97\u88c5\u5907\u7269\u54c1\u5747\u4e0d\u80fd\u52a8\u5f52\u53f7\u4e3b 2\u3001\u4e0d\u5f97\u52a8\u7528\u53f7\u4e3b\u7269\u54c1\uff0c\u5982\u9700\u4f7f\u7528\u9700\u4e8b\u5148\u8054\u7cfb\u6211\u3002 3\u3001\u4e0d\u8981\u5728\u6e38\u620f\u91cc\u53d1\u4ee3\u7ec3\u4fe1\u606f\uff0c\u4e0d\u51c6\u8054\u7cfb\u6e38\u620f\u597d\u53cb\u3002 4\u3001\u63a5\u5355\u534a\u5c0f\u65f6\u5185\u4e0a\u53f7\u5f00\u6253\u5e76\u53d1\u9001\u9996\u56fe\u5230\u7f51\u7ad9\uff0c\u5b8c\u6210\u4e4b\u524d\u53d1\u9001\u5b8c\u6210\u56fe\u3002 5\u3001\u63a5\u5355\u540e\u9000\u5355\u8981\u6263\u6548\u7387\u4fdd\u8bc1\u91d1\u3002 6\u3001\u63a5\u5355\u540e1\u5c0f\u65f6\u5185\u5fc5\u987b\u5f00\u59cb\u4ee3\u7ec3\uff0c\u5426\u5219\u53d1\u5355\u4eba\u53ef\u4ee5\u8981\u6c42\u9000\u5355\u5e76\u6263\u9664\u6548\u7387\u91d1\u3002 7\u3001\u6218\u7ee9\u8fde\u8dea\uff0c\u5c5e\u4e8e\u4f2a\u4ee3\u6211\u4eec\u6709\u6743\u5229\u7533\u8bc9\u6263\u9664\u53cc\u91d1\u3002 8\u3001\u4e0d\u80fd\u5f00\u5916\u6302\uff0c\u5f00\u5916\u6302\u6263\u53cc\u91d1\u3001\u52a0\u6c38\u4e45\u51bb\u7ed3\u5e73\u53f0\u5e10\u53f7\u52a0\u6263\u5168\u90e8\u5e73\u53f0\u91d1\u989d","game_leveling_instructions":"1","businessman_phone":"\u52a0\u5fae\u4fe1xiaohuiji1990","businessman_qq":"3417584093","order_password":"222","creator_username":""}', 'aes-128-cbc','45584685d8e4f5e8', true, '1234567891111152');

        echo base64_encode($a);die;

//        $this->show91Order();
        die;



        dd(Show91Controller::delete([
            'show91_order_no'     => 'ORD180609201239498796',
        ]));

        $order = [
            '2018052410595700000771',
            '2018052411030300000788',
            '2018052411070100000807',
            '2018052411092900000827',
            '2018052411125500000855',
            '2018052412444600001722',
            '2018052413052700001950',
            '2018052413125600002015',
            '2018052413293400002216',
            '2018052413365800002317',
            '2018052413485600002467',
            '2018052413572800002576',
            '2018052414011000002636',
            '2018052414035700002692',
            '2018052414361800003080',
            '2018052414364900003086',
            '2018052414365700003088',
            '2018052414381500003106',
            '2018052414484800003277',
            '2018052414521300003330',
            '2018052414580700003429',
            '2018052415083300003570',
            '2018052415094500003596',
            '2018052415110800003619',
            '2018052415164300003714',
            '2018052415171200003723',
            '2018052415275200003912',
            '2018052415282400003921',
            '2018052415323200003986',
            '2018052416063300004439',
            '2018052416272700004750',
            '2018052416274700004754',
            '2018052416275300004757',
            '2018052416291400004776',
            '2018052416311600004814',
            '2018052416383500004934',
            '2018052416475800005119',
            '2018052416513300005205',
            '2018052416533200005249',
            '2018052417260200005799',
            '2018052417284900005842',
            '2018052417591000006334',
            '2018052418143300006622',
            '2018052418214200006763',
            '2018052418263300006865',
            '2018052418450500007161',
            '2018052418532400007310',
            '2018052419074800007578',
            '2018052419083400007587',
            '2018052419115600007635',
            '2018052419162300007689',
            '2018052419463900007991',
            '2018052420161700008385',
            '2018052420171000008391',
            '2018052420214600008431',
            '2018052420483700008779',
            '2018052421322800009225',
            '2018052422180400009703',
            '2018052422211400009733',
            '2018052422441600010015',
            '2018052423031500010204',
            '2018052423210500010362',
            '2018052500464200000267',
            '2018052507190500000740',
            '2018052508521900000914',
            '2018052509410500001134',
            '2018052509453600001191',
            '2018052511160800001898',
            '2018052511303100001954',
            '2018052511571300002057',
            '2018052512093700002120',
            '2018052512403800002274',
            '2018052512460100002306',
            '2018052512541100002352',
            '2018052512580900002375',
            '2018052513102400002445',
            '2018052513151400002464',
            '2018052513181900002482',
            '2018052513300200002535',
            '2018052513372000002574',
            '2018052513492100002658',
            '2018052513501400002666',
            '2018052513524200002690',
            '2018052513581500002732',
            '2018052514022700002764',
            '2018052514131100002813',
            '2018052514195200002850',
            '2018052514221600002870',
            '2018052514292200002914',
            '2018052414424900003180',
            '2018052416301700004799',
            '2018052420185400008405',
            '2018052421005300008922',
            '2018052421071100008988',
            '2018052514362500002965',
            '2018052514384000002978',
            '2018052514392800002983',
            '2018052514450300003021',
            '2018052514505900003065',
            '2018052411304600000993',
            '2018052515081900003196',
            '2018052515164300003258',
            '2018052515200800003277',
            '2018052515342100003366',
            '2018052518301700004913',
            '2018052520342300005980',
            '2018052523254000007799',
            '2018052615291700002183',
            '2018052617100800002711',
            '2018052621211700003768',
            '2018052711322000000959',
            '2018052712582500001382',
            '2018052812303200000921',
            '2018052818363200001881',
            '2018052912061700000590',
            '2018052916321300001136',
            '2018052918044300001313',
            '2018053007211800000275',
            '2018053010401400000542',
            '2018053017142500001369',
            '2018053017184000001387',
            '2018053111072300000668',
            '2018053123421400002349',
            '2018060109113200000520',
            '2018060117450600001987',
            '2018060218042300001718',
        ];

        foreach ($order as $item) {
            $this->forceRevoke($item, 1);
        }

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

    /**
     * 查询91接的订单并生成csv
     */
    public function show91Order()
    {
        $order = \App\Models\Order::where('gainer_primary_user_id', 8456)->pluck('no')->toArray();

        // 打开文件资源，不存在则创建
        $fp = fopen(storage_path('logs/show91.csv'), 'a');
        fwrite($fp, chr(0xEF).chr(0xBB).chr(0xBF)); // 添加 BOM
        // 处理头部标题
        fputcsv($fp, [
            '内部订单',
            '91单号',
            '内部价格',
            '91价格',
            '内部状态',
            '91状态',
            '订单时间',
        ]);
        foreach ($order as $item) {
            // 查询我们的价格与91的价格与订单状态我们的状态与价格
            $orderInfo = \App\Models\Order::where('no', $item)->with(['detail'])->first();
            $orderDetail = $orderInfo->detail->pluck('field_value', 'field_name');

            // 查询91订单状态与价格
            if (isset($orderDetail['show91_order_no'])) {
                try {
                    $show91Order = Show91Controller::orderDetail([
                        'show91_order_no' => $orderDetail['show91_order_no'],
                        'order_no' => $orderInfo->no,
                    ]);

                    if ($show91Order && isset($show91Order['result'] ) && $show91Order['result'] == 0 ) {
                        // 写入并关闭资源
                        fputcsv($fp, [
                            $orderInfo->no . "\t",
                            $orderDetail['show91_order_no'],
                            $orderInfo->amount,
                            $show91Order['data']['price'] + 0,
                            config('order.status_leveling')[$orderInfo->status],
                            isset($this->show91Status[$show91Order['data']['order_status']]) ? $this->show91Status[$show91Order['data']['order_status']] : '',
                            $orderInfo->created_at,
                        ]);

                    } else {
                        myLog('noFund', [$orderDetail['show91_order_no']]);
                    }
                } catch (\Exception $exception) {
                    myLog('noFund', [$orderDetail['show91_order_no']]);
                }

            }
        }
        fclose($fp);
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

    /**
     * 强的撤销
     * @param $no
     * @param $user
     */
    public function forceRevoke($no, $user)
    {
        $order  = \App\Models\Order::where('no', $no)->first();
        if ($order) {
            (new ForceRevoke())->run($order->no, $order->gainer_primary_user_id);
        }

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
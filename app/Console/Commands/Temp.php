<?php

namespace App\Console\Commands;

use App\Events\OrderApplyComplete;
use App\Events\OrderArbitrationing;
use App\Events\OrderRevoking;
use App\Extensions\Dailian\Controllers\DailianFactory;
use App\Models\OrderDetail;
use App\Models\TaobaoTrade;
use App\Repositories\Frontend\OrderAttachmentRepository;
use App\Repositories\Frontend\OrderDetailRepository;
use App\Services\DailianMama;
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
    protected $signature = 'Temp {no}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '测试';

    protected $message = [];

    protected $messageBeginId = 0;
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $no = $this->argument('no');

        $orderDetail = Show91::orderDetail(['oid' => $no]);

        // 代练中
        if ($orderDetail['data']['order_status'] == 1) {
            $orderNO = OrderDetail::where('field_name', 'show91_order_no')->where('field_value', $no)->value('order_no');
            if ($orderNO) {
                $order = \App\Models\Order::where('no', $orderNO)->where('status', 0);
                if ($order && $order->status == 1) {
                    // 调用自己接单接口
                    $client = new Client();
                    $response = $client->request('POST', 'http://js.qsios.com/api/receive/order', [
                        'form_params' => [
                            'sign' => 'a46ae5de453bfaadc8548a3e48c151db',
                            'orderNo' => $no,
                        ],
                    ]);
                    $result = json_decode($response->getBody()->getContents());
                    myLog('temp-log', [$no, $result]);
                }
            }
        } else {
            myLog('temp-log', [$no, $orderDetail['data']['order_status']]);
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

}
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

        $sourceOrderNo = OrderDetail::where('order_no', $no)
            ->where('field_name_alias', 'source_order_no')
            ->pluck('field_value', 'field_name_alias')
            ->toArray();
        if (count($sourceOrderNo)) {
            $taobaoTrade = TaobaoTrade::select('tid', 'seller_nick')->whereIn('tid', $sourceOrderNo)->get();
            // 发货
            // 获取备注并更新
            $client = new TopClient;
            $client->format = 'json';
            $client->appkey = '12141884';
            $client->secretKey = 'fd6d9b9f6ff6f4050a2d4457d578fa09';
            foreach ($taobaoTrade as $item) {
                $req = new LogisticsDummySendRequest;
                $req->setTid($item->tid);
                $resp = $client->execute($req, taobaoAccessToken($item->seller_nick));
                dump($resp);
            }
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
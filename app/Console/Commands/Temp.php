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
        $client = new Client();
        $response = $client->request('POST', 'http://fulutop.kamennet.com/session/index', [
            'query' => http_build_query([
                'nickName' => '网游增值服务商',
                'sign' => strtoupper(md5('nickName网游增值服务商'. 'fltop31bf3856ad364e35'))
            ]),
        ]);
        $result = json_decode($response->getBody()->getContents());
        dd($result);

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
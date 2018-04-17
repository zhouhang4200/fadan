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

            // 获取备注并更新
            $client = new TopClient;
            $client->format = 'json';
            $client->appkey = '12141884';
            $client->secretKey = 'fd6d9b9f6ff6f4050a2d4457d578fa09';

            $req = new TraderatesGetRequest;
            $req->setFields("tid,oid,role,nick,result,created,rated_nick,item_title,item_price,content,reply,num_iid");
            $req->setRateType("get");
            $req->setRole("buyer");
            $req->setResult("good");
            $req->setPageNo("1");
            $req->setPageSize("150");
            $req->setUseHasNext("true");
            $req->setNumIid("545532985990");
            $resp = $client->execute($req, taobaoAccessToken('斗奇网游专营店'));

            dd($resp);
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
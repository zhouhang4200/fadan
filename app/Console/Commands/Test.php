<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log, Config, Weight, Order;
use TopClient;
use TradeFullinfoGetRequest;

/**
 * Class OrderTestData
 * @package App\Console\Commands
 */
class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '测试';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        define("TOP_SDK_WORK_DIR", "/tmp/");

        $c = new TopClient;
        $c->appkey = '12141884';
        $c->format = 'json';
        $c->secretKey = 'fd6d9b9f6ff6f4050a2d4457d578fa09';

        $req = new TradeFullinfoGetRequest;
        $req->setFields("tid, type, status, payment, orders, seller_memo");
        $req->setTid("129119112592396707");
        $resp = $c->execute($req, taobaoAccessToken('漆黑fin'));
        dd($resp->trade->seller_memo);
    }
}
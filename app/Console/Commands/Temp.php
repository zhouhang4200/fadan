<?php

namespace App\Console\Commands;

use App\Events\OrderApplyComplete;
use App\Events\OrderArbitrationing;
use App\Events\OrderRevoking;
use App\Extensions\Dailian\Controllers\DailianFactory;
use App\Models\OrderDetail;
use App\Repositories\Frontend\OrderAttachmentRepository;
use App\Repositories\Frontend\OrderDetailRepository;
use App\Services\DailianMama;
use App\Services\Show91;
use App\Services\SmSApi;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Log, Config, Weight, Order;
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
    protected $signature = 'Temp';

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
        $order = \App\Models\Order::where('no', '2018041623150800000007')->lockForUpdate()->first();

        event(new OrderRevoking($order));
//        45584685d8e4f5e8e4e2685

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
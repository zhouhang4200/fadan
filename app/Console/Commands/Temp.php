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
        $sourceOrderNo = OrderDetail::where('order_no', '2018041623150800000007')
            ->where('field_name_alias', 'source_order_no')
            ->pluck('field_value', 'field_name_alias')
            ->toArray();
        $taobaoTrade = TaobaoTrade::select('tid', 'seller_nick')->whereIn('tid', $sourceOrderNo)->get();
        dd($taobaoTrade);
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
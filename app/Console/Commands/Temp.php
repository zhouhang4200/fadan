<?php

namespace App\Console\Commands;

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
        $sendOrder = [
            'order_no' => 1,
            'game_name' => 1,
            'game_region' => 1,
            'game_serve' => 1,
            'game_role' => 1,
            'game_account' => 1,
            'game_password' => 1,
            'game_leveling_type' => 1,
            'game_leveling_title' => 2,
            'game_leveling_price' => 1,
            'game_leveling_day' => 1,
            'game_leveling_hour' => 1,
            'game_leveling_security_deposit' => 1,
            'game_leveling_efficiency_deposit' => 1,
            'game_leveling_requirements' => 11,
            'game_leveling_instructions' => 1,
            'businessman_phone' => 1,
            'businessman_qq' => 1,
        ];

        var_dump(base64_encode(openssl_encrypt(\GuzzleHttp\json_encode($sendOrder), 'aes-128-cbc', '45584685d8e4f5e8e4e2685', true, '1234567891111152')));

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
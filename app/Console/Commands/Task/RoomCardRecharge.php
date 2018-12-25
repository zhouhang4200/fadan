<?php

namespace App\Console\Commands\Task;

use DB;
use GuzzleHttp\Client;
use App\Models\Order;
use Illuminate\Console\Command;

class RoomCardRecharge extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Task:RoomCardRecharge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '房卡模拟充值';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->queryBalance();
        die;
        while (1) {
            // 获取要充的订单
            foreach (autoUnShelveGet() as $orderNo) {

                $orderInfo = Order::where('no', $orderNo)->where('status', 3)->first();
                if ($orderInfo) {
                    // 查询房卡数量，并记录

                    // 充值

                    // 成功再次查询房卡数量，并记录。否则写入异常

                    // 如果充值后的数量大于之前数量，则将订单改为成功，如果小于则异常写入异常表中

                    // 从任务中删除
                }
            }
            // 刷新页面

            // 暂停一秒
            sleep(1);
        }

    }

    /**
     *
     * 查询余额
     * @param $userId
     */
    public function queryBalance($userId = 1)
    {

        $client = new Client();
        $response = $client->request('GET', 'https://vip.xianlaihy.com/agent/getPlayerInfo?t=&userid=12978267&province=sichuan_db&type=P', [
            'headers' => [
                'Cookie' => 'SHRIOSESSIONID=48204a97-d7b5-4b51-8b89-2e4dbc656597',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.98 Safari/537.36 LBBROWSER',
            ],
            // ?=&userid=12978267&province=sichuan_db&type=P
        ]);
        $result = $response->getBody()->getContents();
        dd($result);
    }

    /**
     * 充值
     * @param $userId
     */
    public function recharge($userId)
    {
        $client = new Client();
        $response = $client->request('GET', 'https://vip.xianlaihy.com/agent/charge', [
            'headers' => [
                'Cookie' => 'SHRIOSESSIONID=228c5ba5-0661-4842-bc8c-0c75430fed64',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.98 Safari/537.36 LBBROWSER',
            ],
            'form_params' => [
                'chargecount' => '1',
                'province' => 'sichuan_db',
                'vipType' => 'P',
                'usercode' => 'l+BIZKkSqMbxxtfQgRmBew==',
                'playerid' => '12978267',
                'timems' => '1519889781530',
                't' => '0.07439337258497192'
            ]
        ]);
        $result = $response->getBody()->getContents();
        dd($result);
    }

    /**
     * @param $orderNo
     * @param $field
     * @param $value
     */
    protected function updateRecord($orderNo, $field, $value)
    {

    }
}
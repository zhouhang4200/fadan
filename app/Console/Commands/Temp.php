<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Log, Config, Weight, Order;
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

    }
}
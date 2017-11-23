<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log, Config, Weight, Order;

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
        for ($i = 1; $i <=100; $i++)
            app('weight')->run([8072,8073], $i);
    }
}
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * 自动下架（撤销）
 * Class AutoRevoke
 * @package App\Console\Commands
 */
class AutoRevoke extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Order:AutoRevoke';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '自动撤销';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // 获取所有待接并设置了自动下架时间的订单，如到期则自动下架

    }
}

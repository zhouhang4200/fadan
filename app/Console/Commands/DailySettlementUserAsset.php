<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

// 用户资产日结
class DailySettlementUserAsset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily-settlement:user-asset {date=yesterday}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'User asset daily settlement.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
    }
}

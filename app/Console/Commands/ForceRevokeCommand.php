<?php

namespace App\Console\Commands;

use App\Extensions\Dailian\Controllers\ForceRevoke;
use Illuminate\Console\Command;

class ForceRevokeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:force-revoke {no}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '订单操作：强制撤销';

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
     * @throws \App\Exceptions\DailianException
     */
    public function handle()
    {
        $orderData = \App\Models\Order::where('no', $this->argument('no'))->first();

        (new ForceRevoke())->run($orderData->no, 0);
    }
}

<?php

namespace App\Console\Commands;

use DB;
use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use Illuminate\Console\Command;
use App\Models\OrderStatistic as OrderStatisticModel;

class OrderStatistic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:statistic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '代练订单统计';

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
        $yestodayDate = Carbon::now()->subDays(1)->toDateString();
        $todayDate = Carbon::now()->toDateString();

        $datas = Order::whereBetween('updated_at', [$yestodayDate, $todayDate])
            ->
        dd($yestodayDate, $todayDate);
    }
}

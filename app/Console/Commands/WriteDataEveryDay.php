<?php

namespace App\Console\Commands;

use DB;
use Exception;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\DayData;
use Illuminate\Console\Command;

/**
 * 订单集市以及苹果卡等等各订单统计
 */
class WriteDataEveryDay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'write:data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'write everyday data to table';

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
        try {
            $yestodayStart = Carbon::now()->subDays(1)->startOfDay()->toDateTimeString();
            $yestodayEnd = Carbon::now()->subDays(1)->endOfDay()->toDateTimeString();

            // 新订单集市
            $newOrderMarket = Order::where('status', 8)
                ->whereBetween('updated_at', [$yestodayStart, $yestodayEnd])
                ->select(DB::raw('sum(amount) as total'))
                ->value('total');
                
            $datas = [
                'date' => Carbon::now()->subDays(1)->startOfDay()->toDateTimeString(),
                'stock_trusteeship' => 0,
                'stock_transaction' => 0,
                'transfer_transaction' => 0,
                'slow_recharge' => 0,
                'order_market' => $newOrderMarket,
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ];

            DB::table('day_datas')->insert($datas);

        } catch (Exception $e) {
            Log::error('写入错误!', ['table' => 'day_datas']);
        }
    }
}

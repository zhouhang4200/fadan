<?php

namespace App\Console\Commands;

use DB;
use Exception;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\DayData;
use Illuminate\Console\Command;

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

            $oldOrderMarket = DB::connection('qianshou')
                            ->table('thousand_client_orders')
                            ->select(DB::raw('sum(total_price) as total'))
                            ->whereIn('status', [3, 20])
                            ->where('recharge_channel', 12)
                            ->whereBetween('updated_at', [$yestodayStart, $yestodayEnd])
                            ->value('total');

            $orderMarket = bcadd($newOrderMarket, $oldOrderMarket, 2);
            //转单市场
            $transferTransaction = DB::connection('qianshou')
                            ->table('thousand_client_orders')
                            ->select(DB::raw('sum(total_price) as total'))
                            ->whereIn('status', [3, 20])
                            ->where('recharge_channel', 4)
                            ->whereBetween('updated_at', [$yestodayStart, $yestodayEnd])
                            ->value('total');
            // 库存托管
            $stock = DB::connection('qianshou')
                            ->table('plugin_voucher')
                            ->select(DB::raw('sum(denomination) as total'))
                            ->where('status', 1)
                            ->whereBetween('updated_at', [$yestodayStart, $yestodayEnd])
                            ->value('total');

            $apiOrder = DB::connection('qianshou')
                            ->table('thousand_client_orders')
                            ->select(DB::raw('sum(total_price) as total'))
                            ->whereIn('status', [3, 20])
                            ->where('recharge_channel', 3)
                            ->whereBetween('updated_at', [$yestodayStart, $yestodayEnd])
                            ->value('total');

            $stockTrusteeship = bcadd($stock, $apiOrder, 2);
            // 库存交易市场
            $stockTransaction = DB::connection('qianshou')
                            ->table('stock_trading_record')
                            ->select(DB::raw('sum(expense) as total'))
                            ->where('type', 1)
                            ->whereBetween('updated_at', [$yestodayStart, $yestodayEnd])
                            ->value('total');
            // 慢充
            $slowRecharge = DB::connection('qianshou')
                            ->table('apple_id_slow_charge')
                            ->select(DB::raw('sum(amount_payable) as total'))
                            ->where('status', 13)
                            ->whereBetween('updated_at', [$yestodayStart, $yestodayEnd])
                            ->value('total');
            // 写入数据库                 
            $datas = [
                'date' => Carbon::now()->subDays(1)->startOfDay()->toDateTimeString(),
                'stock_trusteeship' => $stockTrusteeship,
                'stock_transaction' => $stockTransaction,
                'transfer_transaction' => $transferTransaction,
                'slow_recharge' => $slowRecharge,
                'order_market' => $orderMarket,
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ];

            DB::table('day_datas')->insert($datas);

        } catch (Exception $e) {
            Log::error('写入错误!', ['table' => 'day_datas']);
        }
    }
}

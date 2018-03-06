<?php
namespace App\Console\Commands;

use App\Exceptions\DailianException;
use App\Extensions\Dailian\Controllers\DailianFactory;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

/**
 * 自动下架（撤销）
 * Class AutoRevoke
 * @package App\Console\Commands
 */
class OrderAutoUnShelve extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Order:AutoUnShelve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '自动下架';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // 获取所有待接并设置了自动下架时间的订单，如到期则自动下架
        while (1) {
            $carbon = new Carbon;
            foreach (autoUnShelveGet() as $orderNo => $data) {
                $data = json_decode($data);
                $time = Carbon::parse($data->time);
                $days = $carbon->diffInDays($time);

                if ($days >= $data->days) {
                    // 调用下架操作
                    try {
                        DailianFactory::choose('offSale')->run($orderNo, $data->user_id, 1);
                        // 下架后删除监听
                        autoUnShelveDel($orderNo);
                    } catch (DailianException $exception) {
                        myLog('exception', ['自动下架异常', $exception->getMessage()]);
                    }
                }
            }
        }
    }
}

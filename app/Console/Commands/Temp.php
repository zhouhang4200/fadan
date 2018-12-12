<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\UserAmountFlow;
use Illuminate\Console\Command;
use Log, Config, Weight;

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
    protected $signature = 'Temp {no?}{user?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '测试';

    protected $message = [];

    protected $show91Status = [
        0 => "已发布",
        1 => "代练中",
        2 => "待验收",
        3 => "待结算",
        4 => "已结算",
        5 => "已挂起",
        6 => "已撤单",
        7 => "已取消",
        10 => "等待工作室接单",
        11 => "等待玩家付款",
        12 => "玩家超时未付款",
    ];

    public function handle()
    {
      ini_set('memory_limit', '1024M');
      
      if ($this->argument('no') == 1) {
      
         $this->order();
      } else {
          $this->flow();
      }
      
        

        
    }

    public function order()
    {
      for($i=1; $i<=12; $i++) {
      
      
      
        $order = Order::select([
            'creator_primary_user_id',
            'gainer_primary_user_id',
            'no',
            'foreign_order_no',
            'source',
            'status',
            'service_name',
            'game_name',
            'goods_name',
            'quantity',
            'original_price',
            'original_amount',
            'price',
            'amount',
            'created_at',
            'service_id',
        ])
            ->filter(['startDate' => '2018-' . $i . '-01', 'endDate' => '2018-' . $i . '-30'])
            ->with(['gainerUser', 'gainerPrimaryUser', 'detail', 'history', 'foreignOrder', 'service']);

        $out = fopen(public_path('export/order-' . $i . '.csv'), 'w');
        fwrite($out, chr(0xEF).chr(0xBB).chr(0xBF)); // 添加 BOM
        fputcsv($out, [
            '服务',
            '买家',
            '卖家',
            '卖家ID备注',
            '订单号',
            '外部订单号',
            '游戏',
            '商品名',
            '账号',
            '版本',
            '数量',
            '单价',
            '总价',
            '天猫单价',
            '天猫总价',
            '渠道',
            '渠道名',
            '状态',
            '原因',
            '下单时间',
        ]);
        $order->chunk(1000, function ($items) use ($out) {
            $orders = $items->toArray();

            foreach ($orders as $k => $v) {

                $status = '';
                if ($v['status']) {
                    $status = isset(config('order.status')[$v['status']]) ? config('order.status')[$v['status']] : config('order.status_leveling')[$v['status']];
                }
                if ($v['source']) {
                    $v['source'] = config('order.source')[$v['source']];
                }
                $reason = '';
                if (isset($v['history'])) {
                    if (in_array($v['status'], [5, 10])) {
                        $count = count($v['history']) - 1;
                        $temp = explode('，', $v['history'][$count]['description']);
                        $reason = $temp[1] ?? '';
                    }
                }
                // 订单详情
                $detail = collect($v['detail'])->pluck( 'field_value','field_name');
                $data = [
                    $v['service']['name'],
                    $v['creator_primary_user_id'],
                    $v['gainer_primary_user_id'],
                    $v['gainer_primary_user']['nickname'] ?? '',
                    $v['no']  . "\t",
                    $v['foreign_order_no']  . "\t",
                    $v['game_name'],
                    $v['goods_name'],
                    $detail['account'] ?? '',
                    $detail['version'] ?? '',
                    $v['quantity'],
                    $v['price'],
                    $v['amount'],
                    $v['original_price'],
                    $v['original_amount'],
                    $v['source'],
                    $v['foreign_order']['channel_name'] ?? '',
                    $status,
                    $reason,
                    $v['created_at']
                ];
                fputcsv($out, $data);
            }
        });
        fclose($out);
        
        }
    }

    public function flow()
    {
      for($i=1; $i<=12; $i++) {
        $query = UserAmountFlow::adminFilter([
            'timeStart' => '2018-' . $i . '-01',
            'timeEnd' => '2018-' . $i . '-12 23:59:59',
        ])
            ->orderBy('id', 'desc');

        $out = fopen(public_path('export/flow-' . $i . '.csv'), 'w');
        fwrite($out, chr(0xEF).chr(0xBB).chr(0xBF)); // 添加 BOM
        fputcsv($out,[
            '流水号',
            '用户',
            '管理员',
            '类型',
            '子类型',
            '相关单号',
            '金额',
            '备注',
            '平台资金',
            '平台托管',
            '用户余额',
            '用户冻结',
            '累计用户加款',
            '累计用户提现',
            '累计用户消费',
            '累计退款给用户',
            '累计用户支出',
            '累计用户收入',
            '时间',
        ]);
        $query->chunk(1000, function ($items) use ($out) {
            $datas = $items->toArray();
            $tradetypePlatform = config('tradetype.platform');
            $tradesubtypePlatformSub = config('tradetype.platform_sub');

            foreach ($datas as $k => $value) {
                $arr = [
                    $value['id'],
                    $value['user_id'],
                    $value['admin_user_id'],
                    $tradetypePlatform[$value['trade_type']],
                    $tradesubtypePlatformSub[$value['trade_subtype']],
                    $value['trade_no'],
                    $value['fee'] + 0,
                    $value['remark'],
                    $value['amount'] ?? 0,
                    $value['managed'] ?? 0,
                    $value['balance'] + 0,
                    $value['frozen'] + 0,
                    $value['total_recharge'] + 0,
                    $value['total_withdraw'] + 0,
                    $value['total_consume'] + 0,
                    $value['total_refund'] + 0,
                    $value['total_expend'] ?? 0,
                    $value['total_income'] ?? 0,
                    $value['created_at'],
                ];
                fputcsv($out, $arr);
            }
        });
      }
    }
}
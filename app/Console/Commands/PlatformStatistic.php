<?php

namespace App\Console\Commands;

use DB;
use Exception;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\PlatformStatistic as PlatformStatisticModel;

class PlatformStatistic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'platform:statistic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '平台订单统计';

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
            $yestodayDate = Carbon::now()->subDays(1)->toDateString();
            $todayDate = Carbon::now()->toDateString();

            $platformStatistics = DB::select("
                SELECT 
                    mm.date, mm.creator_user_id AS user_id, mm.parent_id, mm.third, mm.game_id, 
                    COUNT(mm.no) AS order_count, /*发布单数*/
                    COUNT(mm.client_wang_wang) AS client_wang_wang_count,
                    COUNT(DISTINCT(mm.client_wang_wang)) AS distinct_client_wang_wang_count,
                    FLOOR(SUM(CASE WHEN mm.STATUS IN (19, 20, 21) THEN UNIX_TIMESTAMP(mm.checkout_time)
                    -UNIX_TIMESTAMP(mm.receiving_time) ELSE 0 END)) AS done_order_use_time, /*完单总接单时间戳*/
                    SUM(CASE WHEN mm.STATUS NOT IN (1, 22, 24) THEN 1 ELSE 0 END) AS receive_order_count, /*被接单数*/
                    SUM(CASE WHEN mm.STATUS = 20 THEN 1 ELSE 0 END) AS complete_order_count, /*已结算单数*/
                    SUM(CASE WHEN mm.STATUS = 20 THEN mm.amount ELSE 0 END) AS complete_order_amount, /*已结算总支付*/
                    SUM(CASE WHEN mm.STATUS = 19 THEN 1 ELSE 0 END) AS revoke_order_count, /*已撤销单数*/
                    SUM(CASE WHEN mm.STATUS = 21 THEN 1 ELSE 0 END) AS arbitrate_order_count, /*已仲裁单数*/
                    SUM(CASE WHEN mm.STATUS IN (19, 20, 21) THEN 1 ELSE 0 END) AS done_order_count, /*已完结单数*/
                    SUM(CASE WHEN mm.STATUS IN (19, 20, 21) THEN mm.security_deposit ELSE 0 END) AS done_order_security_deposit, /*完单安全保证金*/
                    SUM(CASE WHEN mm.STATUS IN (19, 20, 21) THEN mm.efficiency_deposit ELSE 0 END) AS done_order_efficiency_deposit, /*完单效率保证金*/
                    SUM(CASE WHEN mm.STATUS IN (19, 20, 21) THEN mm.original_amount ELSE 0 END) AS done_order_original_amount, /*完单总来源价格*/
                    SUM(CASE WHEN mm.STATUS IN (19, 20, 21) THEN mm.amount ELSE 0 END) AS done_order_amount, /*完单总发单价格*/
                    SUM(nn.revoke_payment) AS revoke_payment, /*撤销总支付*/
                    SUM(nn.arbitrate_payment) AS arbitrate_payment, /*仲裁总支付*/
                    SUM(nn.revoke_income) AS revoke_income, /*撤销总收入*/
                    SUM(nn.arbitrate_income) AS arbitrate_income, /*仲裁总收入*/
                    SUM(nn.poundage) AS poundage, /*总手续费*/
                    SUM(CASE WHEN mm.STATUS IN (19, 20, 21) THEN mm.original_amount ELSE 0 END)-SUM(CASE WHEN mm.STATUS = 20 THEN mm.amount ELSE 0 END)
                        -SUM(nn.revoke_payment)-SUM(nn.arbitrate_payment)+SUM(nn.revoke_income)+SUM(nn.arbitrate_income)-SUM(nn.poundage) AS user_profit, /*商户总利润*/
                    SUM(nn.revoke_payment)+SUM(nn.arbitrate_payment)+SUM(nn.poundage)+SUM(CASE WHEN mm.STATUS = 20 THEN mm.amount ELSE 0 END)-SUM(nn.revoke_income)-SUM(nn.arbitrate_income)
                        AS platform_profit /*平台总利润*/
                FROM
                    (SELECT m.no, m.game_id, m.status, m.original_amount, m.amount, m.creator_user_id,m.service_id, m.created_at,
                        DATE_FORMAT(m.created_at, '%Y-%m-%d') AS DATE, n.*, j.name, j.username, j.parent_id
                    FROM orders m
                    LEFT JOIN 
                        (
                        SELECT order_no, 
                        MAX(CASE WHEN field_name='client_wang_wang' AND field_value != '' THEN field_value ELSE NULL END) AS client_wang_wang,
                        MAX(CASE WHEN field_name='security_deposit' THEN field_value ELSE 0 END) AS security_deposit,
                        MAX(CASE WHEN field_name='efficiency_deposit' THEN field_value ELSE 0 END) AS efficiency_deposit,
                        MAX(CASE WHEN field_name='receiving_time' THEN field_value ELSE 0 END) AS receiving_time,
                        MAX(CASE WHEN field_name='checkout_time' THEN field_value ELSE 0 END) AS checkout_time,
                        MAX(CASE WHEN field_name='third' THEN field_value ELSE '' END) AS third
                        FROM order_details GROUP BY order_no 
                        ) n
                    ON m.no = n.order_no
                    LEFT JOIN users j
                    ON m.creator_user_id = j.id
                    ) mm
                LEFT JOIN 
                    (SELECT a.no, a.creator_user_id, a.status, a.created_at,a.creator_primary_user_id ,
                        SUM(CASE WHEN b.trade_subtype = 76 THEN b.fee ELSE 0 END) AS complete_payment,
                         /* 发单总支出代练费*/
                        SUM(CASE WHEN b.trade_subtype = 87 THEN (a.amount-b.fee) ELSE 0 END) AS two_status_payment, 
                        /* 撤销和仲裁总支出*/
                        SUM(CASE WHEN b.trade_subtype = 87 AND a.status = 19 THEN (a.amount-b.fee) ELSE 0 END) AS revoke_payment, /* 撤销总支出*/
                        SUM(CASE WHEN b.trade_subtype = 87 AND a.status = 21 THEN (a.amount-b.fee) ELSE 0 END) AS arbitrate_payment, /* 仲裁总支出*/
                        SUM(CASE WHEN b.trade_subtype IN (810, 811) THEN b.fee ELSE 0 END) AS two_status_income, 
                        /* 撤销和总裁总赔偿*/
                        SUM(CASE WHEN b.trade_subtype IN (810, 811) AND a.status = 19 THEN b.fee ELSE 0 END) AS revoke_income, /* 撤销总赔偿*/
                        SUM(CASE WHEN b.trade_subtype IN (810, 811) AND a.status = 21 THEN b.fee ELSE 0 END) AS arbitrate_income, /* 总裁总赔偿*/
                        SUM(CASE WHEN b.trade_subtype = 73 THEN b.fee ELSE 0 END) AS poundage /* 发单总支出手续费*/
                    FROM orders a LEFT JOIN user_amount_flows b ON a.no = b.trade_no AND b.user_id = a.creator_primary_user_id 
                GROUP BY trade_no) nn
                ON mm.no = nn.no
                WHERE mm.date >= '$yestodayDate' AND mm.date < '$todayDate' AND mm.service_id = 4
                GROUP BY mm.creator_user_id, mm.third, mm.game_id 
            ");

            if ($platformStatistics) {         
                $platformStatistics = array_map(function ($data) {
                    return (array) $data;
                }, $platformStatistics);

                if (! $platformStatistics[0]) {
                    throw new Exception('不存在数据');
                }

                $has = PlatformStatisticModel::where('date', $platformStatistics[0]['date'])->first();
                
                if ($has) {
                    throw new Exception('数据已存在!');
                }
                PlatformStatisticModel::insert($platformStatistics);
                echo '写入成功！';
            } else {
                echo '数据库无数据!';
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}

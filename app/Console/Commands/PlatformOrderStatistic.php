<?php

namespace App\Console\Commands;

use DB;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\PlatformOrderStatistic as PlatformOrderStatisticModel;

class PlatformOrderStatistic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'platformOrder:statistic';

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
        $todayDate = Carbon::now()->toDateString();
        $yestodayDate = Carbon::now()->subDays(1)->toDateString();

        $playformOrders = DB::select("
            SELECT 
                mm.no AS order_no, mm.date, mm.creator_user_id AS user_id, 
                mm.parent_id, mm.service_id,mm.updated_at, mm.checkout_time, mm.receiving_time,
                COUNT(mm.no) AS total_order_count, /*发布单数*/
                SUM(CASE WHEN mm.STATUS IN (19, 20, 21) THEN UNIX_TIMESTAMP(mm.checkout_time)-UNIX_TIMESTAMP(mm.receiving_time) ELSE 0 END) AS use_time, /*完单总接单时间戳*/
                ROUND(SUM(CASE WHEN mm.STATUS IN (19, 20, 21) THEN UNIX_TIMESTAMP(mm.checkout_time)-UNIX_TIMESTAMP(mm.receiving_time) ELSE 0 END)
                    /SUM(CASE WHEN mm.STATUS IN (19, 20, 21) THEN 1 ELSE 0 END), 2) AS use_time_avg, /*平均完单总接单时间戳*/
                ROUND(COUNT(mm.no)/COUNT(distinct(mm.client_wang_wang)), 2) AS wang_wang_order_evg, /*单旺旺号平均发单*/
                SUM(CASE WHEN mm.STATUS NOT IN (1, 22, 24) THEN 1 ELSE 0 END) AS receive_order_count, /*被接单数*/
                SUM(CASE WHEN mm.STATUS = 20 THEN 1 ELSE 0 END) AS complete_order_count, /*已结算单数*/
                ROUND(SUM(CASE WHEN mm.STATUS = 20 THEN 1 ELSE 0 END)/SUM(CASE WHEN mm.STATUS NOT IN (1, 22, 24) THEN 1 ELSE 0 END), 2) AS complete_order_rate, /*已结算占比*/
                SUM(CASE WHEN mm.STATUS = 20 THEN mm.amount ELSE 0 END) AS complete_order_amount, /*已结算总支付*/
                ROUND(SUM(CASE WHEN mm.STATUS = 20 THEN mm.amount ELSE 0 END)/SUM(CASE WHEN mm.STATUS = 20 THEN 1 ELSE 0 END), 2) AS complete_order_amount_avg, /*已结算平均支付*/
                SUM(CASE WHEN mm.STATUS = 19 THEN 1 ELSE 0 END) AS revoke_order_count, /*已撤销单数*/
                ROUND(SUM(CASE WHEN mm.STATUS = 19 THEN 1 ELSE 0 END)/SUM(CASE WHEN mm.STATUS NOT IN (1, 22, 24) THEN 1 ELSE 0 END), 2) AS revoke_order_rate, /*已撤销占比*/
                SUM(CASE WHEN mm.STATUS = 21 THEN 1 ELSE 0 END) AS arbitrate_order_count, /*已仲裁单数*/
                ROUND(SUM(CASE WHEN mm.STATUS = 21 THEN 1 ELSE 0 END)/SUM(CASE WHEN mm.STATUS NOT IN (1, 22, 24) THEN 1 ELSE 0 END), 2) AS complain_order_rate, /*已仲裁占比*/
                SUM(CASE WHEN mm.STATUS IN (19, 20, 21) THEN 1 ELSE 0 END) AS done_order_count, /*已完结单数*/
                SUM(CASE WHEN mm.STATUS IN (19, 20, 21) THEN mm.security_deposit ELSE 0 END) AS total_security_deposit, /*完单安全保证金*/
                ROUND(SUM(CASE WHEN mm.STATUS IN (19, 20, 21) THEN mm.security_deposit ELSE 0 END)/SUM(CASE WHEN mm.STATUS IN (19, 20, 21) THEN 1 ELSE 0 END), 2) AS security_deposit_avg, /*完单平均安全保证金*/
                SUM(CASE WHEN mm.STATUS IN (19, 20, 21) THEN mm.efficiency_deposit ELSE 0 END) AS total_efficiency_deposit, /*完单效率保证金*/
                ROUND(SUM(CASE WHEN mm.STATUS IN (19, 20, 21) THEN mm.efficiency_deposit ELSE 0 END)/SUM(CASE WHEN mm.STATUS IN (19, 20, 21) THEN 1 ELSE 0 END), 2) AS efficiency_deposit_avg, /*完单平均效率保证金*/
                SUM(CASE WHEN mm.STATUS IN (19, 20, 21) THEN mm.original_amount ELSE 0 END) AS total_original_amount, /*完单总来源价格*/
                ROUND(SUM(CASE WHEN mm.STATUS IN (19, 20, 21) THEN mm.original_amount ELSE 0 END)/SUM(CASE WHEN mm.STATUS IN (19, 20, 21) THEN 1 ELSE 0 END), 2) AS original_amount_avg, /*完单平均来源价格*/
                SUM(CASE WHEN mm.STATUS IN (19, 20, 21) THEN mm.amount ELSE 0 END) AS total_amount, /*完单总发单价格*/
                ROUND(SUM(CASE WHEN mm.STATUS IN (19, 20, 21) THEN mm.amount ELSE 0 END)/SUM(CASE WHEN mm.STATUS IN (19, 20, 21) THEN 1 ELSE 0 END), 2) AS amount_avg, /*完单平均发单价格*/
                SUM(nn.revoke_payment) AS total_revoke_payment, /*撤销总支付*/
                ROUND(SUM(nn.revoke_payment)/SUM(CASE WHEN mm.STATUS = 19 THEN 1 ELSE 0 END), 2) AS revoke_payment_avg, /*撤销平均支付*/
                SUM(nn.complain_payment) AS total_complain_payment, /*仲裁总支付*/
                ROUND(SUM(nn.complain_payment)/SUM(CASE WHEN mm.STATUS = 21 THEN 1 ELSE 0 END), 2) AS complain_payment_avg, /*仲裁平均支付*/
                SUM(nn.revoke_income) AS total_revoke_payment, /*撤销总收入*/
                ROUND(SUM(nn.revoke_income)/SUM(CASE WHEN mm.STATUS = 19 THEN 1 ELSE 0 END), 2) AS revoke_income_avg, /*撤销平均收入*/
                SUM(nn.complain_income) AS total_complain_income, /*仲裁总收入*/
                ROUND(SUM(nn.complain_income)/SUM(CASE WHEN mm.STATUS = 21 THEN 1 ELSE 0 END), 2) AS complain_income_avg, /*仲裁平均收入*/
                SUM(nn.poundage) AS total_poundage, /*总手续费*/
                Round(SUM(nn.poundage)/SUM(CASE WHEN mm.STATUS IN (19, 21) THEN 1 ELSE 0 END), 2) AS poundage_avg, /*平均手续费*/
                SUM(CASE WHEN mm.STATUS IN (19, 20, 21) THEN mm.original_amount ELSE 0 END)-SUM(CASE WHEN mm.STATUS = 20 THEN mm.amount ELSE 0 END)
                    -SUM(nn.revoke_payment)-SUM(nn.complain_payment)+SUM(nn.revoke_income)+SUM(nn.complain_income)-SUM(nn.poundage) AS user_total_profit, /*商户总利润*/
                Round((SUM(CASE WHEN mm.STATUS IN (19, 20, 21) THEN mm.original_amount ELSE 0 END)-SUM(CASE WHEN mm.STATUS = 20 THEN mm.amount ELSE 0 END)
                    -SUM(nn.revoke_payment)-SUM(nn.complain_payment)+SUM(nn.revoke_income)+SUM(nn.complain_income)-SUM(nn.poundage))/
                    SUM(CASE WHEN mm.STATUS IN (19, 20, 21) THEN 1 ELSE 0 END), 2) AS user_profit_avg, /*平均利润*/
                SUM(nn.revoke_payment)+SUM(nn.complain_payment)+SUM(nn.poundage)+SUM(CASE WHEN mm.STATUS = 20 THEN mm.amount ELSE 0 END)-SUM(nn.revoke_income)-SUM(nn.complain_income)
                    AS platform_total_profit, /*平台总利润*/
                Round((SUM(nn.revoke_payment)+SUM(nn.complain_payment)+SUM(nn.poundage)+SUM(CASE WHEN mm.STATUS = 20 THEN mm.amount ELSE 0 END)-SUM(nn.revoke_income)-SUM(nn.complain_income))
                    /SUM(CASE WHEN mm.STATUS IN (19, 20, 21) THEN 1 ELSE 0 END), 2) AS platform_profit_avg /*平台平均利润*/
            FROM
                (SELECT m.no, m.status, m.original_amount, m.amount, m.creator_user_id,m.service_id, m.updated_at,
                    DATE_format(m.updated_at, '%Y-%m-%d') AS date, n.*, j.name, j.username, j.parent_id
                FROM orders m
                LEFT JOIN 
                    (SELECT aa.order_no, aa.client_wang_wang, bb.security_deposit, cc.efficiency_deposit, dd.receiving_time, ee.checkout_time
                    FROM
                        (SELECT order_no, field_value AS client_wang_wang
                        FROM order_details WHERE field_name = 'client_wang_wang') aa
                    LEFT JOIN 
                        (SELECT order_no, field_value AS security_deposit
                        FROM order_details WHERE field_name = 'security_deposit' ) bb
                    ON aa.order_no = bb.order_no
                    LEFT JOIN 
                        (SELECT order_no, field_value AS efficiency_deposit
                        FROM order_details WHERE field_name = 'efficiency_deposit' ) cc
                    ON aa.order_no = cc.order_no
                    LEFT JOIN 
                        (SELECT order_no, field_value AS receiving_time
                        FROM order_details WHERE field_name = 'receiving_time' ) dd
                    ON aa.order_no = dd.order_no 
                    LEFT JOIN 
                        (SELECT order_no, field_value AS checkout_time
                        FROM order_details WHERE field_name = 'checkout_time' ) ee
                    ON aa.order_no = ee.order_no 
                    ) n
                ON m.no = n.order_no
                LEFT JOIN users j
                ON m.creator_user_id = j.id
                ) mm
            LEFT JOIN 
                (SELECT a.no, a.creator_user_id, a.status, a.updated_at,
                    SUM(case when b.trade_subtype = 76 then b.fee else 0 end) as complete_payment, /* 发单总支出代练费*/
                    SUM(CASE WHEN b.trade_subtype = 87 THEN (a.amount-b.fee) ELSE 0 END) AS two_status_payment, /* 撤销和仲裁总支出*/
                    SUM(CASE WHEN b.trade_subtype = 87 and a.status = 19 THEN (a.amount-b.fee) ELSE 0 END) AS revoke_payment, /* 撤销总支出*/
                    SUM(CASE WHEN b.trade_subtype = 87 and a.status = 21 THEN (a.amount-b.fee) ELSE 0 END) AS complain_payment, /* 仲裁总支出*/
                    SUM(CASE WHEN b.trade_subtype IN (810, 811) THEN b.fee ELSE 0 END) AS two_status_income, /* 撤销和总裁总赔偿*/
                    SUM(CASE WHEN b.trade_subtype IN (810, 811) and a.status = 19 THEN b.fee ELSE 0 END) AS revoke_income, /* 撤销总赔偿*/
                    SUM(CASE WHEN b.trade_subtype IN (810, 811) and a.status = 21 THEN b.fee ELSE 0 END) AS complain_income, /* 总裁总赔偿*/
                    SUM(CASE WHEN b.trade_subtype = 73 THEN b.fee ELSE 0 END) AS poundage /* 发单总支出手续费*/
                FROM orders a LEFT JOIN user_amount_flows b ON a.no = b.trade_no AND b.user_id = a.creator_user_id 
                
                GROUP BY trade_no) nn
            ON mm.no = nn.no
            WHERE mm.updated_at >= '$yestodayDate' AND mm.updated_at < '$todayDate' AND mm.service_id = 2
            GROUP BY mm.creator_user_id
        ");

        if ($playformOrders) {         
            $playformOrders = array_map(function ($userData) {
                return (array) $userData;
            }, $playformOrders);

            $userDataCheck = $playformOrders[0];

            if ($userDataCheck) {
                $date = $userDataCheck['date'];
                $has = PlatformOrderStatisticModel::where('date', $date)->first();

                if ($has) {
                    throw new Exception('数据已存在!');
                }
            }
            PlatformOrderStatisticModel::insert($playformOrders);

            echo '写入成功！';
        } else {
            echo '数据库无数据!';
        }
    }
}

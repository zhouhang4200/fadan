<?php

namespace App\Console\Commands;

use DB;
use Exception;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Console\Command;
use App\Models\EmployeeStatistic as EmployeeStatisticModel;

class EmployeeStatistic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employee:statistic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '每天凌晨更新商户的员工每天的代练订单数据';

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
        DB::beginTransaction();
        try {
            $yestodayDate = Carbon::now()->subDays(1)->toDateString();
            $todayDate = Carbon::now()->toDateString();

            $userDatas = DB::select("
                SELECT 
                    n.name, 
                    n.id AS user_id, 
                    n.username, 
                    n.parent_id, 
                    m.complete_order_count, 
                    m.revoke_order_count, 
                    m.arbitrate_order_count,m.complete_order_amount, 
                    DATE_FORMAT(m.updated_at, '%Y-%m-%d') AS date, 
                    DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s') AS created_at, 
                    DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s') AS updated_at,
                    m.three_status_original_amount-m.complete_order_amount-m.two_status_payment+m.two_status_income-m.poundage AS profit
                FROM 
                    (SELECT 
                        d.creator_user_id,
                        d.updated_at,
                        COUNT(*) AS send_order_count, 
                        SUM(CASE WHEN d.STATUS NOT IN (1, 22, 24) THEN 1 ELSE 0 END) AS receive_order_count,
                        SUM(CASE WHEN d.STATUS = 20 THEN 1 ELSE 0 END) AS complete_order_count, 
                        SUM(CASE WHEN d.STATUS = 19 THEN 1 ELSE 0 END) AS revoke_order_count, 
                        SUM(CASE WHEN d.STATUS = 21 THEN 1 ELSE 0 END) AS arbitrate_order_count,
                        SUM(CASE WHEN d.STATUS IN (19, 20, 21) THEN d.original_amount ELSE 0 END) AS three_status_original_amount,
                        SUM(CASE WHEN d.STATUS = 20 THEN d.amount ELSE 0 END) AS complete_order_amount,
                        SUM(c.two_status_payment) AS two_status_payment,
                        SUM(c.two_status_income) AS two_status_income,
                        SUM(c.poundage) AS poundage
                    FROM    
                        (SELECT 
                            a.no, 
                            a.creator_user_id, 
                            a.status, 
                            a.updated_at,
                            SUM(CASE WHEN b.trade_subtype = 87 THEN (a.amount-b.fee) ELSE 0 END) AS two_status_payment,
                            SUM(CASE WHEN b.trade_subtype IN (810, 811) THEN b.fee ELSE 0 END) AS two_status_income,
                            SUM(CASE WHEN b.trade_subtype = 73 THEN b.fee ELSE 0 END) AS poundage
                            FROM orders a 
                            LEFT JOIN user_amount_flows b ON a.no = b.trade_no AND b.user_id = a.creator_user_id 
                            WHERE a.updated_at >= '$yestodayDate' AND a.updated_at < '$todayDate' AND a.service_id = 4
                            GROUP BY trade_no
                        ) c LEFT JOIN orders d ON c.no = d.no 
                    GROUP BY d.creator_user_id ) m LEFT JOIN users n ON m.creator_user_id = n.id
                ");

            if ($userDatas) {
                $userDatas = array_map(function ($userData) {
                    return (array) $userData;
                }, $userDatas);

                $userDataCheck = $userDatas[0];

                if ($userDataCheck) {
                    $date = $userDataCheck['date'];
                    $has = EmployeeStatisticModel::where('date', $date)->first();

                    if ($has) {
                        throw new Exception('数据已存在!');
                    }
                }
                EmployeeStatisticModel::insert($userDatas);

                echo '写入成功!';
            } else {
                echo '数据库无数据!';
            }
        } catch (Exception $e) {
            DB::rollback();
            echo '写入失败:'.$e->getMessage();
        }
        DB::commit();
        echo '运行完毕!';
    }
}

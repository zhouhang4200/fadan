<?php

namespace App\Console\Commands;

use DB;
use Exception;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Console\Command;
use App\Models\EmployeeStatistic as EmployeeStatisticModel;

/**
 * 代练平台员工统计
 */
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
     * 每天更新员工数据到员工统计表
     *
     * @return mixed
     */
    public function handle()
    {   
        DB::beginTransaction();
        try {
            $yestodayDate = Carbon::now()->subDays(1)->toDateString();
            $todayDate = Carbon::now()->toDateString();
            //订单表根据接单者（creator_user_id）分组，获取每天接单者相关数据，存入员工统计表
            $userDatas = DB::select("
                SELECT 
                    n.name, 
                    n.id AS user_id, 
                    n.username, 
                    n.parent_id, 
                    m.complete_order_count, 
                    m.revoke_order_count, 
                    m.arbitrate_order_count,
                    m.complete_order_amount, 
                    DATE_FORMAT(m.created_at, '%Y-%m-%d') AS date, 
                    DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s') AS created_at, 
                    DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s') AS updated_at,
                    m.three_status_original_amount-m.complete_order_amount-m.two_status_payment+m.two_status_income+m.poundage AS profit,
                    m.count as all_count,
                    m.all_original_price,
                    m.all_price,
                    m.subtract_price
                FROM 
                    (
                        SELECT 
                            d.creator_user_id,
                            d.created_at,
                            COUNT(*) AS send_order_count, 
                            SUM(CASE WHEN d.STATUS NOT IN (1, 22, 24) THEN 1 ELSE 0 END) AS receive_order_count,
                            SUM(CASE WHEN d.STATUS = 20 THEN 1 ELSE 0 END) AS complete_order_count, 
                            SUM(CASE WHEN d.STATUS = 19 THEN 1 ELSE 0 END) AS revoke_order_count, 
                            SUM(CASE WHEN d.STATUS = 21 THEN 1 ELSE 0 END) AS arbitrate_order_count,
                            SUM(CASE WHEN d.STATUS IN (19, 20, 21) THEN d.original_amount ELSE 0 END) AS three_status_original_amount,
                            SUM(CASE WHEN d.STATUS = 20 THEN d.amount ELSE 0 END) AS complete_order_amount,
                            SUM(c.two_status_payment) AS two_status_payment,
                            SUM(c.two_status_income) AS two_status_income,
                            SUM(c.poundage) AS poundage,
                            count(c.no) as count,
                            sum(c.original_price) as all_original_price,
                            sum(c.price) as all_price,
                            sum(c.original_price)-sum(c.price) as subtract_price
                        FROM    
                            (SELECT 
                                a.no, 
                                a.creator_user_id, 
                                a.status, 
                                a.created_at,
                                a.creator_primary_user_id, 
                                a.original_price,
                                a.price,
                                SUM(CASE WHEN b.trade_subtype = 87 THEN (a.amount-b.fee) ELSE 0 END) AS two_status_payment,
                                SUM(CASE WHEN b.trade_subtype IN (810, 811) THEN b.fee ELSE 0 END) AS two_status_income,
                                SUM(CASE WHEN b.trade_subtype = 73 THEN b.fee ELSE 0 END) AS poundage
                                FROM orders a 
                                LEFT JOIN user_amount_flows b 
                                ON a.no = b.trade_no AND b.user_id = a.creator_primary_user_id 
                                WHERE a.created_at >= '$yestodayDate' AND a.created_at < '$todayDate' AND a.service_id = 4
                                GROUP BY trade_no
                            ) c 
                            LEFT JOIN orders d 
                            ON c.no = d.no 
                        GROUP BY d.creator_user_id 
                    ) m LEFT JOIN users n ON m.creator_user_id = n.id
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
                        throw new Exception('数据已存在');
                    }
                }
                EmployeeStatisticModel::insert($userDatas);

            } else {
                myLog('employee-statistic', ['写入失败！数据库无数据！']);
            }
            myLog('employee-statistic', ['写入成功']);
        } catch (Exception $e) {
            DB::rollback();
            myLog('employee-statistic', ['结果' => '写入失败', '原因' => $e->getMessage()]);
        }
        DB::commit();
    }
}

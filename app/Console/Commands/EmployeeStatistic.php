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

            $userDatas = DB::select("SELECT NOW() as created_at, NOW() as updated_at, a.creator_primary_user_id as parent_id, c.name, c.user_name, DATE_FORMAT(a.updated_at, '%Y-%m-%d') AS date, a.creator_user_id as user_id, COUNT(CASE WHEN a.status = 20 THEN a.id ELSE NULL END) AS complete_order_count, SUM(CASE WHEN a.status = 20 THEN a.amount ELSE 0 END) AS send_order_amount, COUNT(CASE WHEN a.STATUS = 19 THEN a.id ELSE NULL END) AS revoke_order_count, COUNT(CASE WHEN a.STATUS = 21 THEN a.id ELSE NULL END) AS arbitrate_order_count, SUM(case when a.status in (19, 20, 21) then a.original_amount else 0 end) as a.three_status_original_amount, SUM(case when a.status = 20 then a.amount else 0 end) as a.complete_order_amount, SUM(case when a.status in (19, 21) and a.creator_user_id = b.user_id and b.trade_subtype = 87 then (a.amount-b.fee) else 0 end) as a.two_status_payment, SUM(case when a.status in (19, 21) and a.creator_user_id = b.user_id and b.trade_subtype in (810, 811) then b.fee else 0 end) as a.two_status_income, SUM(case when a.status in (19, 21) and a.creator_user_id = b.user_id and b.trade_subtype = 73 then b.fee else 0 end) as a.poundage, SUM(a.three_status_original_amount-a.complete_order_amount-a.two_status_payment+a.two_status_income-a.poundage) as profit FROM orders a LEFT JOIN user_amount_flows b ON a.no = b.trade_no left join users c on a.creator_user_id = c.id WHERE a.updated_at >= '$yestodayDate' AND a.updated_at < '$todayDate' AND a.status IN (19, 20, 21) GROUP BY creator_user_id");

            if ($userDatas) {
                $userDatas = array_map(function ($userData) {
                    return (array) $userData;
                }, $userDatas);

                EmployeeStatisticModel::insert($userDatas);
            }
        } catch (Exception $e) {
            DB::rollback();
            echo '写入失败:'.$e->getMessage();
        }
        DB::commit();
        echo '写入成功!';
    }
}

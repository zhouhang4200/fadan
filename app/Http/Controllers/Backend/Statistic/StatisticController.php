<?php

namespace App\Http\Controllers\Backend\Statistic;

use DB;
use Excel;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\OrderBasicData;
use App\Http\Controllers\Controller;

/**
 * 代练平台订单数据统计
 */
class StatisticController extends Controller
{
    /**
    public function index(Request $request)
    {
    	$userId    = $request->user_id;
		$third     = $request->third;
		$gameId    = $request->game_id;
		$startDate = $request->start_date;
		$endDate   = $request->end_date;
		$fullUrl   = $request->fullUrl();

		if ($request->user_id) {
			$user = User::where('id', $request->user_id)->first();
			if ($user->parent_id == 0) {
				$userIds = [$userId];
			} else {
				$userIds = $user->children()->withTrashed()->pluck('id')->merge($userId);
			}
		} else {
			$userIds = [];
		}

		$filters = compact('startDate', 'endDate', 'userIds', 'third', 'gameId');

    	$users = DB::select("
    		SELECT DISTINCT b.name as username, a.user_id
	    	FROM platform_statistics a 
	    	LEFT JOIN users b 
	    	ON a.user_id = b.id
    	");

    	$games = DB::select("
			SELECT b.id, b.name 
			FROM third_games a 
			LEFT JOIN games b
			ON a.game_id = b.id
    	");

    	$paginatePlatformStatistics = PlatformStatistic::filter($filters)
			->select(DB::raw("
				date, 
				SUM(order_count) AS order_count,
				IFNULL(FLOOR(SUM(client_wang_wang_count)/SUM(distinct_client_wang_wang_count)), 0) AS wang_wang_order_avg,
				SUM(receive_order_count) AS receive_order_count,
				SUM(complete_order_count) AS complete_order_count,
				IFNULL(ROUND(SUM(complete_order_count)/SUM(receive_order_count), 4), 0) AS complete_order_rate,
				SUM(revoke_order_count) AS revoke_order_count,
				IFNULL(ROUND(SUM(revoke_order_count)/SUM(receive_order_count), 4), 0) AS revoke_order_rate,
				SUM(arbitrate_order_count) AS arbitrate_order_count,
				IFNULL(ROUND(SUM(arbitrate_order_count)/SUM(receive_order_count), 4), 0) AS arbitrate_order_rate,
				IFNULL(FLOOR(SUM(done_order_use_time)/SUM(done_order_count)), 0) AS done_order_use_time_avg,
				IFNULL(ROUND(SUM(done_order_security_deposit)/SUM(done_order_count), 2), 0) AS done_order_security_deposit_avg,
				IFNULL(ROUND(SUM(done_order_efficiency_deposit)/SUM(done_order_count), 2), 0) AS done_order_efficiency_deposit_avg,
				IFNULL(ROUND(SUM(done_order_original_amount)/SUM(done_order_count), 2), 0) AS done_order_original_amount_avg,
				SUM(done_order_original_amount) as done_order_original_amount,
				IFNULL(ROUND(SUM(done_order_amount)/SUM(done_order_count), 2), 0) AS done_order_amount_avg,
				SUM(done_order_amount) as done_order_amount,
				IFNULL(ROUND(SUM(complete_order_amount)/SUM(complete_order_count), 2), 0) AS complete_order_amount_avg,
				SUM(complete_order_amount) as complete_order_amount,
				IFNULL(ROUND(SUM(revoke_payment)/SUM(revoke_order_count), 2), 0) AS revoke_payment_avg,
				SUM(revoke_payment) as revoke_payment,
				IFNULL(ROUND(SUM(revoke_income)/SUM(revoke_order_count), 2), 0) AS revoke_income_avg,
				SUM(revoke_income) as revoke_income,
				IFNULL(ROUND(SUM(arbitrate_payment)/SUM(arbitrate_order_count), 2), 0) AS arbitrate_payment_avg,
				SUM(arbitrate_payment) as arbitrate_payment,
				IFNULL(ROUND(SUM(arbitrate_income)/SUM(arbitrate_order_count), 2), 0) AS arbitrate_income_avg,
				SUM(arbitrate_income) as arbitrate_income,
				IFNULL(ROUND(SUM(poundage)/(SUM(arbitrate_order_count)+SUM(revoke_order_count)), 2), 0) AS poundage_avg,
				SUM(poundage) as poundage,
				IFNULL(ROUND(SUM(user_profit)/SUM(done_order_count), 2), 0) AS user_profit_avg,
				SUM(user_profit) as user_profit,
				IFNULL(ROUND(SUM(platform_profit)/SUM(done_order_count), 2), 0) AS platform_profit_avg,
				SUM(platform_profit) as platform_profit
			"))
		->groupBy('date')
        ->latest('date')
		->paginate(config('backend.page'));

        // 总计
        $totalPlatformStatistics = PlatformStatistic::filter($filters)
            ->select(DB::raw("
                SUM(order_count) AS order_count,
                IFNULL(FLOOR(SUM(client_wang_wang_count)/SUM(distinct_client_wang_wang_count)), 0) AS wang_wang_order_avg,
                SUM(receive_order_count) AS receive_order_count,
                SUM(complete_order_count) AS complete_order_count,
                IFNULL(ROUND(SUM(complete_order_count)/SUM(receive_order_count), 4), 0) AS complete_order_rate,
                SUM(revoke_order_count) AS revoke_order_count,
                IFNULL(ROUND(SUM(revoke_order_count)/SUM(receive_order_count), 4), 0) AS revoke_order_rate,
                SUM(arbitrate_order_count) AS arbitrate_order_count,
                IFNULL(ROUND(SUM(arbitrate_order_count)/SUM(receive_order_count), 4), 0) AS arbitrate_order_rate,
                IFNULL(FLOOR(SUM(done_order_use_time)/SUM(done_order_count)), 0) AS done_order_use_time_avg,
                IFNULL(ROUND(SUM(done_order_security_deposit)/SUM(done_order_count), 2), 0) AS done_order_security_deposit_avg,
                IFNULL(ROUND(SUM(done_order_efficiency_deposit)/SUM(done_order_count), 2), 0) AS done_order_efficiency_deposit_avg,
                IFNULL(ROUND(SUM(done_order_original_amount)/SUM(done_order_count), 2), 0) AS done_order_original_amount_avg,
                SUM(done_order_original_amount) as done_order_original_amount,
                IFNULL(ROUND(SUM(done_order_amount)/SUM(done_order_count), 2), 0) AS done_order_amount_avg,
                SUM(done_order_amount) as done_order_amount,
                IFNULL(ROUND(SUM(complete_order_amount)/SUM(complete_order_count), 2), 0) AS complete_order_amount_avg,
                SUM(complete_order_amount) as complete_order_amount,
                IFNULL(ROUND(SUM(revoke_payment)/SUM(revoke_order_count), 2), 0) AS revoke_payment_avg,
                SUM(revoke_payment) as revoke_payment,
                IFNULL(ROUND(SUM(revoke_income)/SUM(revoke_order_count), 2), 0) AS revoke_income_avg,
                SUM(revoke_income) as revoke_income,
                IFNULL(ROUND(SUM(arbitrate_payment)/SUM(arbitrate_order_count), 2), 0) AS arbitrate_payment_avg,
                SUM(arbitrate_payment) as arbitrate_payment,
                IFNULL(ROUND(SUM(arbitrate_income)/SUM(arbitrate_order_count), 2), 0) AS arbitrate_income_avg,
                SUM(arbitrate_income) as arbitrate_income,
                IFNULL(ROUND(SUM(poundage)/(SUM(arbitrate_order_count)+SUM(revoke_order_count)), 2), 0) AS poundage_avg,
                SUM(poundage) as poundage,
                IFNULL(ROUND(SUM(user_profit)/SUM(done_order_count), 2), 0) AS user_profit_avg,
                SUM(user_profit) as user_profit,
                IFNULL(ROUND(SUM(platform_profit)/SUM(done_order_count), 2), 0) AS platform_profit_avg,
                SUM(platform_profit) as platform_profit
            "))
            ->first();

		if ($request->export && $paginatePlatformStatistics->count() > 0) {
			static::export($paginatePlatformStatistics->toArray()['data']);
		}
    	return view('backend.statistic.platform', compact('startDate', 'users', 'games', 'endDate', 
            'userId', 'third', 'gameId', 'fullUrl', 'paginatePlatformStatistics', 'totalPlatformStatistics'));
    }
    */
   
   public function index(Request $request)
   {
        $userId    = $request->user_id;
        $third     = $request->third;
        $gameId    = $request->game_id;
        $startDate = $request->start_date;
        $endDate   = $request->end_date;
        $fullUrl   = $request->fullUrl();

        $userIds = [];
        if ($request->user_id) {
            $user = User::where('id', $request->user_id)->first();
            if ($user->parent_id == 0) {
                $userIds = [$userId];
            } else {
                $userIds = $user->children()->withTrashed()->pluck('id')->merge($userId);
            }
        }   

        $users = DB::select("
            SELECT DISTINCT b.username, b.id as user_id
            FROM order_basic_datas a 
            LEFT JOIN users b 
            ON a.creator_user_id = b.id
        ");

        $games = DB::select("
            SELECT a.game_id, b.name
            FROM goods_templates a 
            LEFT JOIN games b
            ON a.game_id = b.id
            where a.service_id = 4
        ");

        $filters = compact('startDate', 'endDate', 'userIds', 'third', 'gameId');     

        $paginatePlatformStatistics = OrderBasicData::filter($filters)
            ->select(DB::raw("
                    date,
                    COUNT(order_no) AS count,
                    COUNT(DISTINCT client_wang_wang) AS client_wang_wang_count,
                    COUNT(DISTINCT creator_primary_user_id) AS primary_creator_count,
                    COUNT(DISTINCT third) AS third_count,
                    SUM(CASE WHEN STATUS = 13 THEN 1 ELSE 0 END) AS received_count,
                    SUM(CASE WHEN STATUS = 20 THEN 1 ELSE 0 END) AS completed_count,
                    SUM(CASE WHEN STATUS = 19 THEN 1 ELSE 0 END) AS revoked_count,
                    SUM(CASE WHEN STATUS = 21 THEN 1 ELSE 0 END) AS arbitrationed_count,
                    SUM(CASE WHEN STATUS = 23 THEN 1 ELSE 0 END) AS forced_count,
                    SUM(CASE WHEN STATUS = 24 THEN 1 ELSE 0 END) AS deleted_count,
                    SUM(CASE WHEN STATUS IN (19, 20, 21) THEN UNIX_TIMESTAMP(order_finished_at)-UNIX_TIMESTAMP(order_created_at) ELSE 0 END) AS total_use_time,
                    SUM(CASE WHEN STATUS IN (19, 20, 21) THEN security_deposit ELSE 0 END) AS total_security_deposit,
                    SUM(CASE WHEN STATUS IN (19, 20, 21) THEN efficiency_deposit ELSE 0 END) AS total_efficiency_deposit,
                    SUM(CASE WHEN STATUS IN (19, 20, 21) THEN original_price ELSE 0 END) AS total_original_price,
                    SUM(CASE WHEN STATUS IN (19, 20, 21) THEN price ELSE 0 END) AS total_price,
                    SUM(CASE WHEN STATUS = 20 THEN price ELSE 0 END) AS total_completed_price,
                    SUM(CASE WHEN STATUS = 19 THEN consult_amount ELSE 0 END) AS total_revoked_payment,
                    SUM(CASE WHEN STATUS = 21 THEN consult_amount ELSE 0 END) AS total_arbitrationed_payment,
                    SUM(CASE WHEN STATUS = 19 THEN consult_deposit ELSE 0 END) AS total_revoked_income,
                    SUM(CASE WHEN STATUS = 21 THEN consult_deposit ELSE 0 END) AS total_arbitrationed_income,
                    SUM(CASE WHEN STATUS IN (19, 21) THEN consult_poundage ELSE 0 END) AS total_poundage,
                    SUM(CASE WHEN STATUS = 20 THEN original_price-tm_income-price+creator_judge_income-creator_judge_payment ELSE 0 END) +
                    SUM(CASE WHEN STATUS = 19 THEN original_price-tm_income-consult_amount+consult_deposit-consult_poundage+creator_judge_income-creator_judge_payment ELSE 0 END) +
                    SUM(CASE WHEN STATUS = 21 THEN original_price-tm_income-consult_amount+consult_deposit-consult_poundage+creator_judge_income-creator_judge_payment ELSE 0 END) AS total_creator_profit,
                    SUM(CASE WHEN STATUS = 20 THEN price-creator_judge_income+creator_judge_payment ELSE 0 END) +
                    SUM(CASE WHEN STATUS = 19 THEN consult_amount-consult_deposit+consult_poundage-creator_judge_income+creator_judge_payment ELSE 0 END) +
                    SUM(CASE WHEN STATUS = 21 THEN consult_amount-consult_deposit+consult_poundage-creator_judge_income+creator_judge_payment ELSE 0 END) AS total_gainer_profit
                "))
        ->groupBy('date')
        ->latest('date')
        ->paginate(15);

            // dd($paginatePlatformStatistics);

        $totalPlatformStatistics = OrderBasicData::filter($filters)
            ->select(DB::raw("
                    date,
                    COUNT(order_no) AS count,
                    COUNT(DISTINCT client_wang_wang) AS client_wang_wang_count,
                    COUNT(DISTINCT creator_primary_user_id) AS primary_creator_count,
                    COUNT(DISTINCT third) AS third_count,
                    SUM(CASE WHEN STATUS = 13 THEN 1 ELSE 0 END) AS received_count,
                    SUM(CASE WHEN STATUS = 20 THEN 1 ELSE 0 END) AS completed_count,
                    SUM(CASE WHEN STATUS = 19 THEN 1 ELSE 0 END) AS revoked_count,
                    SUM(CASE WHEN STATUS = 21 THEN 1 ELSE 0 END) AS arbitrationed_count,
                    SUM(CASE WHEN STATUS = 23 THEN 1 ELSE 0 END) AS forced_count,
                    SUM(CASE WHEN STATUS = 24 THEN 1 ELSE 0 END) AS deleted_count,
                    SUM(CASE WHEN STATUS IN (19, 20, 21) THEN UNIX_TIMESTAMP(order_finished_at)-UNIX_TIMESTAMP(order_created_at) ELSE 0 END) AS total_use_time,
                    SUM(CASE WHEN STATUS IN (19, 20, 21) THEN security_deposit ELSE 0 END) AS total_security_deposit,
                    SUM(CASE WHEN STATUS IN (19, 20, 21) THEN efficiency_deposit ELSE 0 END) AS total_efficiency_deposit,
                    SUM(CASE WHEN STATUS IN (19, 20, 21) THEN original_price ELSE 0 END) AS total_original_price,
                    SUM(CASE WHEN STATUS IN (19, 20, 21) THEN price ELSE 0 END) AS total_price,
                    SUM(CASE WHEN STATUS = 20 THEN price ELSE 0 END) AS total_completed_price,
                    SUM(CASE WHEN STATUS = 19 THEN consult_amount ELSE 0 END) AS total_revoked_payment,
                    SUM(CASE WHEN STATUS = 21 THEN consult_amount ELSE 0 END) AS total_arbitrationed_payment,
                    SUM(CASE WHEN STATUS = 19 THEN consult_deposit ELSE 0 END) AS total_revoked_income,
                    SUM(CASE WHEN STATUS = 21 THEN consult_deposit ELSE 0 END) AS total_arbitrationed_income,
                    SUM(CASE WHEN STATUS IN (19, 21) THEN consult_poundage ELSE 0 END) AS total_poundage,
                    SUM(CASE WHEN STATUS = 20 THEN original_price-tm_income-price+creator_judge_income-creator_judge_payment ELSE 0 END) +
                    SUM(CASE WHEN STATUS = 19 THEN original_price-tm_income-consult_amount+consult_deposit-consult_poundage+creator_judge_income-creator_judge_payment ELSE 0 END) +
                    SUM(CASE WHEN STATUS = 21 THEN original_price-tm_income-consult_amount+consult_deposit-consult_poundage+creator_judge_income-creator_judge_payment ELSE 0 END) AS total_creator_profit,
                    SUM(CASE WHEN STATUS = 20 THEN price-creator_judge_income+creator_judge_payment ELSE 0 END) +
                    SUM(CASE WHEN STATUS = 19 THEN consult_amount-consult_deposit+consult_poundage-creator_judge_income+creator_judge_payment ELSE 0 END) +
                    SUM(CASE WHEN STATUS = 21 THEN consult_amount-consult_deposit+consult_poundage-creator_judge_income+creator_judge_payment ELSE 0 END) AS total_gainer_profit
                "))
        ->first();

        if ($request->export && $paginatePlatformStatistics->count() > 0) {
            static::export($paginatePlatformStatistics->toArray()['data']);
        }
        return view('backend.statistic.platform', compact('startDate', 'users', 'games', 'endDate', 
            'userId', 'third', 'gameId', 'fullUrl', 'paginatePlatformStatistics', 'totalPlatformStatistics'));
   }

    /**
     * 导出
     * @param  [type] $datas [description]
     * @return [type]        [description]
     */
    public static function export($datas)
    {
    	$title = [
    		'发布时间',
            '发布单数',
            '单旺旺号平均发送',
            '被接单数',
            '已结算单数',
            '已结算占比',
            '已撤销单数',
            '已撤销占比',
            '已仲裁单数',
            '已仲裁占比',
            '完单平均代练时间',
            '完单平均安全保证金',
            '完单平均效率保证金',
            '完单平均来源价格',
            '完单总来源价格',
            '完单平均发单价格',
            '完单总发单价格',
            '结算平均支付',
            '结算总支付',
            '撤销平均支付',
            '撤销总支付',
            '撤销平均赔偿',
            '撤销总赔偿',
            '仲裁平均支付',
            '仲裁总支付',
            '仲裁平均赔偿',
            '仲裁总赔偿',
            '平均手续费',
            '总手续费',
            '商户平均利润',
            '商户总利润',
            '平台平均利润',
            '平台总利润'
    	];

    	$chunkDatas = array_chunk($datas, 100);

    	Excel::create('代练平台统计', function ($excel) use ($chunkDatas, $title) {

            foreach ($chunkDatas as $chunkData) {
                // 内容
                $arr = [];
                foreach ($chunkData as $key => $data) {
                    $arr[] = [
                        $data['date'],
                        $data['count'],
                        $data['client_wang_wang_count'] == 0 ? 0 : bcdiv($data['count'], $data['client_wang_wang_count'], 2),
                        $data['received_count'],
                        $data['completed_count'],
                        $data['count'] == 0 ? 0 : bcdiv($data['completed_count'], $data['count'], 2),
                        $data['revoked_count'],
                        $data['count'] == 0 ? 0 : bcdiv($data['revoked_count'], $data['count'], 2),
                        $data['arbitrationed_count'],
                        $data['count'] == 0 ? 0 : bcdiv($data['arbitrationed_count'], $data['count'], 2),
                        $data['completed_count']+$data['revoked_count']+$data['arbitrationed_count'] == 0 ? 0 : sec2Time(bcdiv($data['total_use_time'], ($data['completed_count']+$data['revoked_count']+$data['arbitrationed_count']))),
                        $data['completed_count']+$data['revoked_count']+$data['arbitrationed_count'] == 0 ? 0 : bcdiv($data['total_security_deposit'], ($data['completed_count']+$data['revoked_count']+$data['arbitrationed_count']), 2),
                        $data['completed_count']+$data['revoked_count']+$data['arbitrationed_count'] == 0 ? 0 : bcdiv($data['total_efficiency_deposit'], ($data['completed_count']+$data['revoked_count']+$data['arbitrationed_count']), 2),
                        $data['completed_count']+$data['revoked_count']+$data['arbitrationed_count'] == 0 ? 0 : bcdiv($data['total_original_price'], ($data['completed_count']+$data['revoked_count']+$data['arbitrationed_count']), 2),
                        $data['completed_count']+$data['revoked_count']+$data['arbitrationed_count'] == 0 ? 0 : bcdiv($data['total_price'], ($data['completed_count']+$data['revoked_count']+$data['arbitrationed_count']), 2),
                        $data['total_price'],
                        $data['completed_count'] == 0 ? 0 : bcdiv($data['total_completed_price'], $data['completed_count'], 2),
                        $data['total_completed_price'],
                        $data['revoked_count'] == 0 ? 0 : bcdiv($data['total_revoked_payment'], $data['revoked_count'], 2),
                        $data['total_revoked_payment'],
                        $data['revoked_count'] == 0 ? 0 : bcdiv($data['total_revoked_income'], $data['revoked_count'], 2),
                        $data['total_revoked_income'],
                        $data['arbitrationed_count'] == 0 ? 0 : bcdiv($data['total_arbitrationed_payment'], $data['arbitrationed_count'], 2),
                        $data['total_arbitrationed_payment'],
                        $data['arbitrationed_count'] == 0 ? 0 : bcdiv($data['total_arbitrationed_income'], $data['arbitrationed_count'], 2),
                        $data['total_arbitrationed_income'],
                        $data['arbitrationed_count']+$data['revoked_count'] == 0 ? 0 : bcdiv($data['total_poundage'], ($data['arbitrationed_count']+$data['revoked_count']), 2),
                        $data['total_poundage'],
                        $data['primary_creator_count'] == 0 ? 0 : bcdiv($data['total_creator_profit'], $data['primary_creator_count'], 2),
                        $data['total_creator_profit'],
                        $data['third_count'] == 0 ? 0 : bcdiv($data['total_gainer_profit'], $data['third_count'], 2),
                        $data['total_gainer_profit'],
                    ];
                }
                // 将标题加入到数组
                array_unshift($arr, $title);
                // 每页多少数据
                $excel->sheet("页数", function ($sheet) use ($arr) {
                    $sheet->rows($arr);
                });
            }
        })->export('xls');
    }
}

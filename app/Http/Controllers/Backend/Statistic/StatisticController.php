<?php

namespace App\Http\Controllers\Backend\Statistic;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PlatformOrderStatistic;
use App\Models\PlatformGameStatistic;
use App\Models\PlatformThirdStatistic;

/**
 * 代练平台订单数据统计
 */
class StatisticController extends Controller
{
    public function index(Request $request)
    {
    	$username = $request->username;
    	$startDate = $request->start_date;
    	$endDate = $request->end_date;
    	$fullUrl = $request->fullUrl();
    	$filters = compact('startDate', 'endDate', 'username');

    	$users = DB::select("
    		SELECT DISTINCT b.name as username, a.user_id
	    	FROM platform_order_statistics a 
	    	LEFT JOIN users b 
	    	ON a.user_id = b.id
    	");

    	$gameFilters = compact('startDate', 'endDate', 'gameId');
    	$thirdFilters = compact('startDate', 'endDate', 'thirdId');

    	if ($request->game_id && $request->third_id) {
    		// return back()->withErrors('游戏和第三方平台以及发单商户只能选择一个');
    	} elseif ($request->game_id && ! $request->third_id) {
    // 		$paginatePlatformOrderStatistics = PlatformGameStatistic::filter($gameFilters)
				// ->select(DB::raw("
				// 	date, 
				// 	SUM(total_order_count) AS total_order_count,
				// 	IFNULL(FLOOR(SUM(wang_wang_order_evg)/COUNT(user_id)), 0) AS wang_wang_order_evg,
				// 	SUM(use_time) AS use_time,
				// 	IFNULL(FLOOR(SUM(use_time)/COUNT(user_id)), 0) AS use_time_avg,
				// 	SUM(receive_order_count) AS receive_order_count,
				// 	SUM(complete_order_count) AS complete_order_count,
				// 	IFNULL(ROUND(SUM(complete_order_rate)/COUNT(user_id), 2), 0) AS complete_order_rate,
				// 	SUM(complete_order_amount) AS complete_order_amount,
				// 	IFNULL(ROUND(SUM(complete_order_amount_avg)/COUNT(user_id), 2), 0) AS complete_order_amount_avg,
				// 	SUM(revoke_order_count) AS revoke_order_count,
				// 	IFNULL(ROUND(SUM(revoke_order_rate)/COUNT(user_id), 2), 0) AS revoke_order_rate,
				// 	SUM(arbitrate_order_count) AS arbitrate_order_count,
				// 	IFNULL(ROUND(SUM(complain_order_rate)/COUNT(user_id), 2), 0) AS complain_order_rate,
				// 	SUM(done_order_count) AS done_order_count,
				// 	SUM(total_security_deposit) AS total_security_deposit,
				// 	IFNULL(ROUND(SUM(security_deposit_avg)/COUNT(user_id), 2), 0) AS security_deposit_avg,
				// 	SUM(total_efficiency_deposit) AS total_efficiency_deposit,
				// 	IFNULL(ROUND(SUM(efficiency_deposit_avg)/COUNT(user_id), 2), 0) AS efficiency_deposit_avg,
				// 	SUM(total_original_amount) AS total_original_amount,
				// 	IFNULL(ROUND(SUM(original_amount_avg)/COUNT(user_id), 2), 0) AS original_amount_avg,
				// 	SUM(total_amount) AS total_amount,
				// 	IFNULL(ROUND(SUM(amount_avg)/COUNT(user_id), 2), 0) AS amount_avg,
				// 	SUM(total_revoke_payment) AS total_revoke_payment,
				// 	IFNULL(ROUND(SUM(revoke_payment_avg)/COUNT(user_id), 2), 0) AS revoke_payment_avg,
				// 	SUM(total_complain_payment) AS total_complain_payment,
				// 	IFNULL(ROUND(SUM(complain_payment_avg)/COUNT(user_id), 2), 0) AS complain_payment_avg,
				// 	SUM(total_revoke_income) AS total_revoke_income,
				// 	IFNULL(ROUND(SUM(revoke_income_avg)/COUNT(user_id), 2), 0) AS revoke_income_avg,
				// 	SUM(total_complain_income) AS total_complain_income,
				// 	IFNULL(ROUND(SUM(complain_income_avg)/COUNT(user_id), 2), 0) AS complain_income_avg,
				// 	SUM(total_poundage) AS total_poundage,
				// 	IFNULL(ROUND(SUM(poundage_avg)/COUNT(user_id), 2), 0) AS poundage_avg,
				// 	SUM(user_total_profit) AS user_total_profit,
				// 	IFNULL(ROUND(SUM(user_profit_avg)/COUNT(user_id), 2), 0) AS user_profit_avg,
				// 	SUM(platform_total_profit) AS platform_total_profit,
				// 	IFNULL(ROUND(SUM(platform_profit_avg)/COUNT(user_id), 2), 0) AS platform_profit_avg
				// "))
				// ->groupBy('date')
				// ->paginate(config('backend.page'));
    	} elseif ($request->third_id && ! $request->game_id) {
    // 		$paginatePlatformOrderStatistics = PlatformThirdStatistic::filter($thirdFilters)
				// ->select(DB::raw("
				// 	date, 
				// 	SUM(total_order_count) AS total_order_count,
				// 	IFNULL(FLOOR(SUM(wang_wang_order_evg)/COUNT(user_id)), 0) AS wang_wang_order_evg,
				// 	SUM(use_time) AS use_time,
				// 	IFNULL(FLOOR(SUM(use_time)/COUNT(user_id)), 0) AS use_time_avg,
				// 	SUM(receive_order_count) AS receive_order_count,
				// 	SUM(complete_order_count) AS complete_order_count,
				// 	IFNULL(ROUND(SUM(complete_order_rate)/COUNT(user_id), 2), 0) AS complete_order_rate,
				// 	SUM(complete_order_amount) AS complete_order_amount,
				// 	IFNULL(ROUND(SUM(complete_order_amount_avg)/COUNT(user_id), 2), 0) AS complete_order_amount_avg,
				// 	SUM(revoke_order_count) AS revoke_order_count,
				// 	IFNULL(ROUND(SUM(revoke_order_rate)/COUNT(user_id), 2), 0) AS revoke_order_rate,
				// 	SUM(arbitrate_order_count) AS arbitrate_order_count,
				// 	IFNULL(ROUND(SUM(complain_order_rate)/COUNT(user_id), 2), 0) AS complain_order_rate,
				// 	SUM(done_order_count) AS done_order_count,
				// 	SUM(total_security_deposit) AS total_security_deposit,
				// 	IFNULL(ROUND(SUM(security_deposit_avg)/COUNT(user_id), 2), 0) AS security_deposit_avg,
				// 	SUM(total_efficiency_deposit) AS total_efficiency_deposit,
				// 	IFNULL(ROUND(SUM(efficiency_deposit_avg)/COUNT(user_id), 2), 0) AS efficiency_deposit_avg,
				// 	SUM(total_original_amount) AS total_original_amount,
				// 	IFNULL(ROUND(SUM(original_amount_avg)/COUNT(user_id), 2), 0) AS original_amount_avg,
				// 	SUM(total_amount) AS total_amount,
				// 	IFNULL(ROUND(SUM(amount_avg)/COUNT(user_id), 2), 0) AS amount_avg,
				// 	SUM(total_revoke_payment) AS total_revoke_payment,
				// 	IFNULL(ROUND(SUM(revoke_payment_avg)/COUNT(user_id), 2), 0) AS revoke_payment_avg,
				// 	SUM(total_complain_payment) AS total_complain_payment,
				// 	IFNULL(ROUND(SUM(complain_payment_avg)/COUNT(user_id), 2), 0) AS complain_payment_avg,
				// 	SUM(total_revoke_income) AS total_revoke_income,
				// 	IFNULL(ROUND(SUM(revoke_income_avg)/COUNT(user_id), 2), 0) AS revoke_income_avg,
				// 	SUM(total_complain_income) AS total_complain_income,
				// 	IFNULL(ROUND(SUM(complain_income_avg)/COUNT(user_id), 2), 0) AS complain_income_avg,
				// 	SUM(total_poundage) AS total_poundage,
				// 	IFNULL(ROUND(SUM(poundage_avg)/COUNT(user_id), 2), 0) AS poundage_avg,
				// 	SUM(user_total_profit) AS user_total_profit,
				// 	IFNULL(ROUND(SUM(user_profit_avg)/COUNT(user_id), 2), 0) AS user_profit_avg,
				// 	SUM(platform_total_profit) AS platform_total_profit,
				// 	IFNULL(ROUND(SUM(platform_profit_avg)/COUNT(user_id), 2), 0) AS platform_profit_avg
				// "))
				// ->groupBy('date')
				// ->paginate(config('backend.page'));
    	} else {
	    	$paginatePlatformOrderStatistics = PlatformOrderStatistic::filter($filters)
				->select(DB::raw("
					date, 
					SUM(total_order_count) AS total_order_count,
					IFNULL(FLOOR(SUM(wang_wang_order_evg)/COUNT(user_id)), 0) AS wang_wang_order_evg,
					SUM(use_time) AS use_time,
					IFNULL(FLOOR(SUM(use_time)/COUNT(user_id)), 0) AS use_time_avg,
					SUM(receive_order_count) AS receive_order_count,
					SUM(complete_order_count) AS complete_order_count,
					IFNULL(ROUND(SUM(complete_order_rate)/COUNT(user_id), 2), 0) AS complete_order_rate,
					SUM(complete_order_amount) AS complete_order_amount,
					IFNULL(ROUND(SUM(complete_order_amount_avg)/COUNT(user_id), 2), 0) AS complete_order_amount_avg,
					SUM(revoke_order_count) AS revoke_order_count,
					IFNULL(ROUND(SUM(revoke_order_rate)/COUNT(user_id), 2), 0) AS revoke_order_rate,
					SUM(arbitrate_order_count) AS arbitrate_order_count,
					IFNULL(ROUND(SUM(complain_order_rate)/COUNT(user_id), 2), 0) AS complain_order_rate,
					SUM(done_order_count) AS done_order_count,
					SUM(total_security_deposit) AS total_security_deposit,
					IFNULL(ROUND(SUM(security_deposit_avg)/COUNT(user_id), 2), 0) AS security_deposit_avg,
					SUM(total_efficiency_deposit) AS total_efficiency_deposit,
					IFNULL(ROUND(SUM(efficiency_deposit_avg)/COUNT(user_id), 2), 0) AS efficiency_deposit_avg,
					SUM(total_original_amount) AS total_original_amount,
					IFNULL(ROUND(SUM(original_amount_avg)/COUNT(user_id), 2), 0) AS original_amount_avg,
					SUM(total_amount) AS total_amount,
					IFNULL(ROUND(SUM(amount_avg)/COUNT(user_id), 2), 0) AS amount_avg,
					SUM(total_revoke_payment) AS total_revoke_payment,
					IFNULL(ROUND(SUM(revoke_payment_avg)/COUNT(user_id), 2), 0) AS revoke_payment_avg,
					SUM(total_complain_payment) AS total_complain_payment,
					IFNULL(ROUND(SUM(complain_payment_avg)/COUNT(user_id), 2), 0) AS complain_payment_avg,
					SUM(total_revoke_income) AS total_revoke_income,
					IFNULL(ROUND(SUM(revoke_income_avg)/COUNT(user_id), 2), 0) AS revoke_income_avg,
					SUM(total_complain_income) AS total_complain_income,
					IFNULL(ROUND(SUM(complain_income_avg)/COUNT(user_id), 2), 0) AS complain_income_avg,
					SUM(total_poundage) AS total_poundage,
					IFNULL(ROUND(SUM(poundage_avg)/COUNT(user_id), 2), 0) AS poundage_avg,
					SUM(user_total_profit) AS user_total_profit,
					IFNULL(ROUND(SUM(user_profit_avg)/COUNT(user_id), 2), 0) AS user_profit_avg,
					SUM(platform_total_profit) AS platform_total_profit,
					IFNULL(ROUND(SUM(platform_profit_avg)/COUNT(user_id), 2), 0) AS platform_profit_avg
				"))
			->groupBy('date')
			->paginate(config('backend.page'));
    	}

    	return view('backend.statistic.platform', compact('startDate', 'users', 'endDate', 'username', 'fullUrl', 'paginatePlatformOrderStatistics'));
    }
}

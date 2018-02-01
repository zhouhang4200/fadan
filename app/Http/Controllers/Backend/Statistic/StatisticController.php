<?php

namespace App\Http\Controllers\Backend\Statistic;

use DB;
use Excel;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\PlatformStatistic;
use App\Http\Controllers\Controller;

/**
 * 代练平台订单数据统计
 */
class StatisticController extends Controller
{
    public function index(Request $request)
    {
    	$userId    = $request->user_id;
		$third     = $request->thrid;
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
	    	FROM platform_order_statistics a 
	    	LEFT JOIN users b 
	    	ON a.user_id = b.id
    	");

    	$games = DB::select("
			SELECT a.id, a.name 
			FROM games a 
			LEFT JOIN third_games b
			ON a.id = b.game_id
    	");

    	$paginatePlatformStatistics = PlatformStatistic::filter($filters)
			->select(DB::raw("
				date, 
				SUM(order_count) AS order_count,
				IFNULL(FLOOR(SUM(client_wang_wang_count)/SUM(distinct_client_wang_wang_count)), 0) AS wang_wang_order_avg,
				SUM(receive_order_count) AS receive_order_count,
				SUM(complete_order_count) AS complete_order_count,
				IFNULL(ROUND(SUM(complete_order_count)/SUM(receive_order_count), 2), 0) AS complete_order_rate,
				SUM(revoke_order_count) AS revoke_order_count,
				IFNULL(ROUND(SUM(revoke_order_count)/SUM(receive_order_count), 2), 0) AS revoke_order_rate,
				SUM(arbitrate_order_count) AS arbitrate_order_count,
				IFNULL(ROUND(SUM(arbitrate_order_count)/SUM(receive_order_count), 2), 0) AS arbitrate_order_rate,
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

		if ($request->export && $paginatePlatformStatistics->count() > 0) {
			static::export($paginatePlatformStatistics->toArray()['data']);
		}
    	return view('backend.statistic.platform', compact('startDate', 'users', 'games', 'endDate', 'userId', 'third', 'gameId', 'fullUrl', 'paginatePlatformStatistics'));
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
                        $data['order_count'],
                        $data['wang_wang_order_avg'],
                        $data['receive_order_count'],
                        $data['complete_order_count'],
                        $data['complete_order_rate'],
                        $data['revoke_order_count'],
                        $data['revoke_order_rate'],
                        $data['arbitrate_order_count'],
                        $data['arbitrate_order_rate'],
                        $data['done_order_use_time_avg'],
                        $data['done_order_security_deposit_avg'],
                        $data['done_order_efficiency_deposit_avg'],
                        $data['done_order_original_amount_avg'],
                        $data['done_order_original_amount'],
                        $data['done_order_amount_avg'],
                        $data['done_order_amount'],
                        $data['complete_order_amount_avg'],
                        $data['complete_order_amount'],
                        $data['revoke_payment_avg'],
                        $data['revoke_payment'],
                        $data['revoke_income_avg'],
                        $data['revoke_income'],
                        $data['arbitrate_payment_avg'],
                        $data['arbitrate_payment'],
                        $data['arbitrate_income_avg'],
                        $data['arbitrate_income'],
                        $data['poundage_avg'],
                        $data['poundage'],
                        $data['user_profit_avg'],
                        $data['user_profit'],
                        $data['platform_profit_avg'],
                        $data['platform_profit'],
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

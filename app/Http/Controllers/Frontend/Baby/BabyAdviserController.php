<?php

namespace App\Http\Controllers\Frontend\Baby;

use DB;
use Auth;
use Excel;
use Exception;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\OrderBasicData;
use App\Http\Controllers\Controller;

class BabyAdviserController extends Controller
{
    /**
     * 宝贝外包订单
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function index(Request $request)
    {
        // 所有宝贝
    	$games = DB::select("
    		SELECT b.id, b.name FROM goods_templates a
			LEFT JOIN games b
			ON a.game_id = b.id
			WHERE a.service_id = 4 AND a.status = 1
		");

		$gameId = $request->game_id;
		$startDate = $request->start_date;
		$endDate = $request->end_date;
		$fullUrl = $request->fullUrl();

		$filters = compact('gameId', 'startDate', 'endDate');

		$user = Auth::user();
        // 主账号下所有的用户
		if ($user->parent_id == 0) {
			$userId = $user->id;
		} else {
			$userId = User::getPrimaryUserId($user->id);
		}
        // 根据日期获取数据
		$datas = OrderBasicData::filterBaby($filters)
			->where('creator_primary_user_id', $userId)
			->select(DB::raw("
					date,
					COUNT(order_no) AS count,
					SUM(CASE WHEN STATUS = 13 THEN 1 ELSE 0 END) AS received_count,
					SUM(CASE WHEN STATUS = 20 THEN 1 ELSE 0 END) AS completed_count,
					SUM(CASE WHEN STATUS = 19 THEN 1 ELSE 0 END) AS revoked_count,
					SUM(CASE WHEN STATUS = 21 THEN 1 ELSE 0 END) AS arbitrationed_count,
					SUM(CASE WHEN STATUS IN (19, 20, 21) THEN original_price ELSE 0 END) AS three_original_price,
					SUM(CASE WHEN STATUS = 20 THEN price ELSE 0 END) AS completed_price,
					SUM(CASE WHEN STATUS IN (19, 21) THEN consult_amount ELSE 0 END) AS consult_amount,
					SUM(CASE WHEN STATUS IN (19, 21) THEN consult_deposit ELSE 0 END) AS consult_deposit,
					SUM(CASE WHEN STATUS IN (19 ,21) THEN consult_poundage ELSE 0 END) AS consult_poundage,
					SUM(CASE WHEN STATUS = 20 THEN original_price-tm_income-price+creator_judge_income-creator_judge_payment ELSE 0 END) +
					SUM(CASE WHEN STATUS IN (19, 21) THEN original_price-tm_income-consult_amount+consult_deposit+consult_poundage+creator_judge_income-creator_judge_payment ELSE 0 END) AS profit
				"))
			->groupBy('date')
			->paginate(15);

        // 总计
		$total = OrderBasicData::filterBaby($filters)
			->where('creator_primary_user_id', $userId)
			->select(DB::raw("
					COUNT(order_no) AS count,
					SUM(CASE WHEN STATUS = 13 THEN 1 ELSE 0 END) AS received_count,
					SUM(CASE WHEN STATUS = 20 THEN 1 ELSE 0 END) AS completed_count,
					SUM(CASE WHEN STATUS = 19 THEN 1 ELSE 0 END) AS revoked_count,
					SUM(CASE WHEN STATUS = 21 THEN 1 ELSE 0 END) AS arbitrationed_count,
					SUM(CASE WHEN STATUS IN (19, 20, 21) THEN original_price ELSE 0 END) AS three_original_price,
					SUM(CASE WHEN STATUS = 20 THEN price ELSE 0 END) AS completed_price,
					SUM(CASE WHEN STATUS IN (19, 21) THEN consult_amount ELSE 0 END) AS consult_amount,
					SUM(CASE WHEN STATUS IN (19, 21) THEN consult_deposit ELSE 0 END) AS consult_deposit,
					SUM(CASE WHEN STATUS IN (19 ,21) THEN consult_poundage ELSE 0 END) AS consult_poundage,
					SUM(CASE WHEN STATUS = 20 THEN original_price-tm_income-price+creator_judge_income-creator_judge_payment ELSE 0 END) +
					SUM(CASE WHEN STATUS IN (19, 21) THEN original_price-tm_income-consult_amount+consult_deposit+consult_poundage+creator_judge_income-creator_judge_payment ELSE 0 END) AS profit
				"))
			->first();

        // 导出
		if ($request->export) {
			$this->export($datas, $total);
		}

		return view('frontend.v1.baby.index', compact('games', 'datas', 'total', 'gameId', 'startDate', 'endDate', 'userId', 'fullUrl'));
    }

    /**
     * 宝贝外包订单导出
     * @param  string $datas [description]
     * @param  string $total [description]
     * @return [type]        [description]
     */
    public function export($datas = '', $total = '')
    {
		if (empty($datas) || empty($total)) {
			return response()->ajax(0, '暂无数据');
		}
		$datas = $datas->toArray()['data'];
		$total = $total->toArray();
        // 标题
        $title = [
            '发布时间',
            '发布单数',
            '被接单数',
            '已结算单数',
            '已结算占比',
            '已撤销单数',
            '已仲裁单数',
            '已结算/撤销/仲裁来源价格',
            '已结算单发单金额',
            '撤销/仲裁支付金额',
            '撤销/仲裁获得赔偿',
            '手续费',
            '利润',
        ];
        // 数组分割,反转
        $chunkDatas = array_chunk($datas, 1000);

        Excel::create('运营宝贝数据', function ($excel) use ($chunkDatas, $title, $total) {

            foreach ($chunkDatas as $chunkData) {
                // 内容
                $arr = [];
                foreach ($chunkData as $key => $data) {
                    $arr[] = [
                        $data['date'] ?? '',
                        $data['count'] ?? 0,
                        $data['received_count'] ?? 0,
                        $data['completed_count'] ?? 0,
                        ($data['count'] == 0 ? 0 : bcmul(bcdiv($data['completed_count'], $data['count']), 100, 2)+0).'%',
                        $data['revoked_count'] + 0,
                        $data['arbitrationed_count'] + 0,
                        $data['three_original_price'] + 0,
                        $data['completed_price'] + 0,
                        $data['consult_amount'] + 0,
                        $data['consult_deposit'] + 0,
                        $data['consult_poundage'] + 0,
                        $data['profit'] + 0,
                    ];
                }
                $arr1 = [
            		'总计',
                    $total['count'] ?? 0,
                    $total['received_count'] ?? 0,
                    $total['completed_count'] ?? 0,
                    ($total['count'] == 0 ? 0 : bcmul(bcdiv($total['completed_count'], $total['count']), 100, 2)+0).'%',
                    $total['revoked_count'] + 0,
                    $total['arbitrationed_count'] + 0,
                    $total['three_original_price'] + 0,
                    $total['completed_price'] + 0,
                    $total['consult_amount'] + 0,
                    $total['consult_deposit'] + 0,
                    $total['consult_poundage'] + 0,
                    $total['profit'] + 0,
                ];
                array_push($arr, $arr1);
                // 将标题加入到数组
                array_unshift($arr, $title);
                // 每页多少数据
                $excel->sheet("页数", function ($sheet) use ($arr) {
                    $sheet->rows($arr);
                });
            }
        })->export('xls');
    }

    /**
     * 宝贝运营状况
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function show(Request $request)
    {
        //  // 所有宝贝
        // $games = DB::select("
        //     SELECT b.id, b.name FROM goods_templates a
        //     LEFT JOIN games b
        //     ON a.game_id = b.id
        //     WHERE a.service_id = 4 AND a.status = 1
        // ");

        // $gameId = $request->game_id;
        // $startDate = $request->start_date;
        // $endDate = $request->end_date;
        // $fullUrl = $request->fullUrl();

        // $filters = compact('gameId', 'startDate', 'endDate');

        // $user = Auth::user();
        // // 主账号下所有的用户
        // if ($user->parent_id == 0) {
        //     $userId = $user->id;
        // } else {
        //     $userId = User::getPrimaryUserId($user->id);
        // }
        // // 根据游戏获取数据
        // $datas = OrderBasicData::filterBaby($filters)
        //     ->where('creator_primary_user_id', $userId)
        //     ->select(DB::raw("
        //             COUNT(DISTINCT client_wang_wang) AS count,
        //             SUM(original_price) AS original_price,
        //             SUM(CASE WHEN STATUS IN (13, 19, 20, 21) THEN 1 ELSE 0 END) AS payment_count,
        //             SUM(CASE WHEN STATUS IN (13, 19, 20, 21) THEN price ELSE 0 END) AS payment_amount,

        //             SUM(CASE WHEN STATUS = 20 THEN 1 ELSE 0 END) AS completed_count,
        //             SUM(CASE WHEN STATUS = 19 THEN 1 ELSE 0 END) AS revoked_count,
        //             SUM(CASE WHEN STATUS = 21 THEN 1 ELSE 0 END) AS arbitrationed_count,
        //             SUM(CASE WHEN STATUS = 20 THEN price ELSE 0 END) AS completed_price,
        //             SUM(CASE WHEN STATUS IN (19, 21) THEN consult_amount ELSE 0 END) AS consult_amount,
        //             SUM(CASE WHEN STATUS IN (19, 21) THEN consult_deposit ELSE 0 END) AS consult_deposit,
        //             SUM(CASE WHEN STATUS IN (19 ,21) THEN consult_poundage ELSE 0 END) AS consult_poundage,
        //             SUM(CASE WHEN STATUS = 20 THEN original_price-tm_income-price+creator_judge_income-creator_judge_payment ELSE 0 END) +
        //             SUM(CASE WHEN STATUS IN (19, 21) THEN original_price-tm_income-consult_amount+consult_deposit+consult_poundage+creator_judge_income-creator_judge_payment ELSE 0 END) AS profit
        //         "))
        //     ->groupBy('game_id')
        //     ->paginate(15);

        // // 导出
        // if ($request->export) {
        //     $this->export($datas, $total);
        // }

        // return view('frontend.v1.baby.index', compact('games', 'datas', 'total', 'gameId', 'startDate', 'endDate', 'userId', 'fullUrl'));
    }
}

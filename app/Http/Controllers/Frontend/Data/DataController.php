<?php

namespace App\Http\Controllers\Frontend\Data;

use Auth, DB;
use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\UserOrderMoney;
use App\Models\UserOrderDetail;
use App\Http\Controllers\Controller;

class DataController extends Controller
{
    public function index(Request $request)
    {
    	try {
	    	$startDate = $request->startDate;

	    	$endDate = $request->endDate;

	    	$masterUser = Auth::user()->getPrimaryUserId();

	    	if (($startDate && ! $endDate) || (! $startDate && $endDate)) {

	    		return back()->with('timeError', '请选择开始日期和结束日期！');
	    	}

	    	if ($startDate && $endDate) {

	    		$start = $startDate . ' 00:00:00';

	    		$end = $endDate . ' 23:59:59';

	    		// 接单+发单，所有渠道
	    		$datas = UserOrderDetail::where('user_id', $masterUser)
			    		->whereBetween('time', [$start, $end])
			    		->where('type', 3)
			    		->where('source', 0)
			    		->select(DB::raw('sum(total) as total, sum(waite_user_receive) as waite_user_receive, sum(distributing) as distributing, sum(received) as received, sum(sended) as sended, sum(failed) as failed, sum(after_saling) as after_saling, sum(after_saled) as after_saled, sum(successed) as successed, sum(canceled) as canceled, sum(waite_pay) as waite_pay'))
			    		->first();

			    $mostGame = UserOrderDetail::where('user_id', $masterUser)
			    		->whereBetween('time', [$start, $end])
			    		->where('type', 3)
			    		->where('source', 0)
			    		->groupBy('most_game_name')
			    		->select(DB::raw('sum(most_game_amount) as total, most_game_name'))
			    		->latest('total')
			    		->first();

			    // 接单+发单 ， 分渠道
			    $sourceDatas = UserOrderDetail::where('user_id', $masterUser)
			    		->whereBetween('time', [$start, $end])
			    		->where('type', 3)
			    		->where('source', '!=', 0)
			    		->groupBy('source')
			    		->select(DB::raw('sum(total) as total, sum(waite_user_receive) as waite_user_receive, sum(distributing) as distributing, sum(received) as received, sum(sended) as sended, sum(failed) as failed, sum(after_saling) as after_saling, sum(after_saled) as after_saled, sum(successed) as successed, sum(canceled) as canceled, sum(waite_pay) as waite_pay, source'))
			    		->get();

			    $resourceGame = DB::select("select source, most_game_name, max(count) as max from (select sum(most_game_amount) as count, most_game_name, source from user_order_details where user_id = '$masterUser' and time between '$start' and '$end' and type = '3' and source != '0' group by source,most_game_name order by source, count desc) a group by source");

			    $resourceGame = collect($resourceGame);

			    // 接单 ， 分渠道
			    $receiveDatas = UserOrderDetail::where('user_id', $masterUser)
			    		->whereBetween('time', [$start, $end])
			    		->where('type', 1)
			    		->where('source', '!=', 0)
			    		->groupBy('source')
			    		->select(DB::raw('sum(total) as total, sum(waite_user_receive) as waite_user_receive, sum(distributing) as distributing, sum(received) as received, sum(sended) as sended, sum(failed) as failed, sum(after_saling) as after_saling, sum(after_saled) as after_saled, sum(successed) as successed, sum(canceled) as canceled, sum(waite_pay) as waite_pay, source'))
			    		->get();

			    $receiveGame = DB::select("select source, most_game_name, max(count) as max from (select sum(most_game_amount) as count, most_game_name, source from user_order_details where user_id = '$masterUser' and time between '$start' and '$end' and type = '1' and source != '0' group by source,most_game_name order by source, count desc) a group by source");

			    $receiveGame = collect($receiveGame);

			    // 发单 ， 分渠道
			    $sendDatas = UserOrderDetail::where('user_id', $masterUser)
			    		->whereBetween('time', [$start, $end])
			    		->where('type', 2)
			    		->where('source', '!=', 0)
			    		->groupBy('source')
			    		->select(DB::raw('sum(total) as total, sum(waite_user_receive) as waite_user_receive, sum(distributing) as distributing, sum(received) as received, sum(sended) as sended, sum(failed) as failed, sum(after_saling) as after_saling, sum(after_saled) as after_saled, sum(successed) as successed, sum(canceled) as canceled, sum(waite_pay) as waite_pay, source'))
			    		->get();

			    $sendGame = DB::select("select source, most_game_name, max(count) as max from (select sum(most_game_amount) as count, most_game_name, source from user_order_details where user_id = '$masterUser' and time between '$start' and '$end' and type = '2' and source != '0' group by source,most_game_name order by source, count desc) a group by source");

			    $sendGame = collect($sendGame);
			    
			    // 金额
			    // 接单+发单，所有渠道
	    		$moneyDatas = UserOrderMoney::where('user_id', $masterUser)
			    		->whereBetween('time', [$start, $end])
			    		->where('type', 3)
			    		->where('source', 0)
			    		->select(DB::raw('sum(total) as total, sum(waite_user_receive) as waite_user_receive, sum(distributing) as distributing, sum(received) as received, sum(sended) as sended, sum(failed) as failed, sum(after_saling) as after_saling, sum(after_saled) as after_saled, sum(successed) as successed, sum(canceled) as canceled, sum(waite_pay) as waite_pay'))
			    		->first();

			    // 接单+发单 ， 分渠道
			    $sourceMoneyDatas = UserOrderMoney::where('user_id', $masterUser)
			    		->whereBetween('time', [$start, $end])
			    		->where('type', 3)
			    		->where('source', '!=', 0)
			    		->groupBy('source')
			    		->select(DB::raw('sum(total) as total, sum(waite_user_receive) as waite_user_receive, sum(distributing) as distributing, sum(received) as received, sum(sended) as sended, sum(failed) as failed, sum(after_saling) as after_saling, sum(after_saled) as after_saled, sum(successed) as successed, sum(canceled) as canceled, sum(waite_pay) as waite_pay, source'))
			    		->get();
			    
			    // 接单 ， 分渠道
			    $receiveMoneyDatas = UserOrderMoney::where('user_id', $masterUser)
			    		->whereBetween('time', [$start, $end])
			    		->where('type', 1)
			    		->where('source', '!=', 0)
			    		->groupBy('source')
			    		->select(DB::raw('sum(total) as total, sum(waite_user_receive) as waite_user_receive, sum(distributing) as distributing, sum(received) as received, sum(sended) as sended, sum(failed) as failed, sum(after_saling) as after_saling, sum(after_saled) as after_saled, sum(successed) as successed, sum(canceled) as canceled, sum(waite_pay) as waite_pay, source'))
			    		->get();

			    // 发单 ， 分渠道
			    $sendMoneyDatas = UserOrderMoney::where('user_id', $masterUser)
			    		->whereBetween('time', [$start, $end])
			    		->where('type', 2)
			    		->where('source', '!=', 0)
			    		->groupBy('source')
			    		->select(DB::raw('sum(total) as total, sum(waite_user_receive) as waite_user_receive, sum(distributing) as distributing, sum(received) as received, sum(sended) as sended, sum(failed) as failed, sum(after_saling) as after_saling, sum(after_saled) as after_saled, sum(successed) as successed, sum(canceled) as canceled, sum(waite_pay) as waite_pay, source'))
			    		->get();
    		} else {

	    		$start = Carbon::now()->subDays(1)->startOfDay()->toDateTimeString();

	    		$end = Carbon::now()->subDays(1)->endOfDay()->toDateTimeString();

	    		// 接单+发单，所有渠道
	    		$datas = UserOrderDetail::where('user_id', $masterUser)
			    		->whereBetween('time', [$start, $end])
			    		->where('type', 3)
			    		->where('source', 0)
			    		->select(DB::raw('sum(total) as total, sum(waite_user_receive) as waite_user_receive, sum(distributing) as distributing, sum(received) as received, sum(sended) as sended, sum(failed) as failed, sum(after_saling) as after_saling, sum(after_saled) as after_saled, sum(successed) as successed, sum(canceled) as canceled, sum(waite_pay) as waite_pay'))
			    		->first();

			    $mostGame = UserOrderDetail::where('user_id', $masterUser)
			    		->whereBetween('time', [$start, $end])
			    		->where('type', 3)
			    		->where('source', 0)
			    		->groupBy('most_game_name')
			    		->select(DB::raw('sum(most_game_amount) as total, most_game_name'))
			    		->latest('total')
			    		->first();

			    // 接单+发单 ， 分渠道
			    $sourceDatas = UserOrderDetail::where('user_id', $masterUser)
			    		->whereBetween('time', [$start, $end])
			    		->where('type', 3)
			    		->where('source', '!=', 0)
			    		->groupBy('source')
			    		->select(DB::raw('sum(total) as total, sum(waite_user_receive) as waite_user_receive, sum(distributing) as distributing, sum(received) as received, sum(sended) as sended, sum(failed) as failed, sum(after_saling) as after_saling, sum(after_saled) as after_saled, sum(successed) as successed, sum(canceled) as canceled, sum(waite_pay) as waite_pay, source'))
			    		->get();

			    $resourceGame = DB::select("select source, most_game_name, max(count) as max from (select sum(most_game_amount) as count, most_game_name, source from user_order_details where user_id = '$masterUser' and time between '$start' and '$end' and type = '3' and source != '0' group by source,most_game_name order by source, count desc) a group by source");

			    $resourceGame = collect($resourceGame);

			    // 接单 ， 分渠道
			    $receiveDatas = UserOrderDetail::where('user_id', $masterUser)
			    		->whereBetween('time', [$start, $end])
			    		->where('type', 1)
			    		->where('source', '!=', 0)
			    		->groupBy('source')
			    		->select(DB::raw('sum(total) as total, sum(waite_user_receive) as waite_user_receive, sum(distributing) as distributing, sum(received) as received, sum(sended) as sended, sum(failed) as failed, sum(after_saling) as after_saling, sum(after_saled) as after_saled, sum(successed) as successed, sum(canceled) as canceled, sum(waite_pay) as waite_pay, source'))
			    		->get();

			    $receiveGame = DB::select("select source, most_game_name, max(count) as max from (select sum(most_game_amount) as count, most_game_name, source from user_order_details where user_id = '$masterUser' and time between '$start' and '$end' and type = '1' and source != '0' group by source,most_game_name order by source, count desc) a group by source");

			    $receiveGame = collect($receiveGame);

			    // 发单 ， 分渠道
			    $sendDatas = UserOrderDetail::where('user_id', $masterUser)
			    		->whereBetween('time', [$start, $end])
			    		->where('type', 2)
			    		->where('source', '!=', 0)
			    		->groupBy('source')
			    		->select(DB::raw('sum(total) as total, sum(waite_user_receive) as waite_user_receive, sum(distributing) as distributing, sum(received) as received, sum(sended) as sended, sum(failed) as failed, sum(after_saling) as after_saling, sum(after_saled) as after_saled, sum(successed) as successed, sum(canceled) as canceled, sum(waite_pay) as waite_pay, source'))
			    		->get();

			    $sendGame = DB::select("select source, most_game_name, max(count) as max from (select sum(most_game_amount) as count, most_game_name, source from user_order_details where user_id = '$masterUser' and time between '$start' and '$end' and type = '2' and source != '0' group by source,most_game_name order by source, count desc) a group by source");

			    $sendGame = collect($sendGame);

			    // 金额
			    // 接单+发单，所有渠道
	    		$moneyDatas = UserOrderMoney::where('user_id', $masterUser)
			    		->whereBetween('time', [$start, $end])
			    		->where('type', 3)
			    		->where('source', 0)
			    		->select(DB::raw('sum(total) as total, sum(waite_user_receive) as waite_user_receive, sum(distributing) as distributing, sum(received) as received, sum(sended) as sended, sum(failed) as failed, sum(after_saling) as after_saling, sum(after_saled) as after_saled, sum(successed) as successed, sum(canceled) as canceled, sum(waite_pay) as waite_pay'))
			    		->first();

			    // 接单+发单 ， 分渠道
			    $sourceMoneyDatas = UserOrderMoney::where('user_id', $masterUser)
			    		->whereBetween('time', [$start, $end])
			    		->where('type', 3)
			    		->where('source', '!=', 0)
			    		->groupBy('source')
			    		->select(DB::raw('sum(total) as total, sum(waite_user_receive) as waite_user_receive, sum(distributing) as distributing, sum(received) as received, sum(sended) as sended, sum(failed) as failed, sum(after_saling) as after_saling, sum(after_saled) as after_saled, sum(successed) as successed, sum(canceled) as canceled, sum(waite_pay) as waite_pay, source'))
			    		->get();

			    // 接单 ， 分渠道
			    $receiveMoneyDatas = UserOrderMoney::where('user_id', $masterUser)
			    		->whereBetween('time', [$start, $end])
			    		->where('type', 1)
			    		->where('source', '!=', 0)
			    		->groupBy('source')
			    		->select(DB::raw('sum(total) as total, sum(waite_user_receive) as waite_user_receive, sum(distributing) as distributing, sum(received) as received, sum(sended) as sended, sum(failed) as failed, sum(after_saling) as after_saling, sum(after_saled) as after_saled, sum(successed) as successed, sum(canceled) as canceled, sum(waite_pay) as waite_pay, source'))
			    		->get();

			    // 发单 ， 分渠道
			    $sendMoneyDatas = UserOrderMoney::where('user_id', $masterUser)
			    		->whereBetween('time', [$start, $end])
			    		->where('type', 2)
			    		->where('source', '!=', 0)
			    		->groupBy('source')
			    		->select(DB::raw('sum(total) as total, sum(waite_user_receive) as waite_user_receive, sum(distributing) as distributing, sum(received) as received, sum(sended) as sended, sum(failed) as failed, sum(after_saling) as after_saling, sum(after_saled) as after_saled, sum(successed) as successed, sum(canceled) as canceled, sum(waite_pay) as waite_pay, source'))
			    		->get();	    		
	    	}

	    	return view('frontend.data.index', compact('startDate', 'endDate', 'datas', 'mostGame', 'sourceDatas', 'resourceGame', 'receiveDatas', 'receiveGame', 'sendDatas', 'sendGame', 'moneyDatas', 'sourceMoneyDatas', 'receiveMoneyDatas', 'sendMoneyDatas'));

	    } catch (Exception $e) {

			Log::error('数据错误', ['table' => 'user_order_details and user_order_moneys']);
		}
    }

    public function show()
    {
    	$startDate = $request->startDate;

    	$endDate = $request->endDate;

    	$masterUser = Auth::user()->getPrimaryUserId();

    	if (($startDate && ! $endDate) || (! $startDate && $endDate)) {

    		return back()->with('timeError', '请选择正确的起止日期！');
    	}

    	if ($startDate && $endDate) {

    		$start = $startDate . ' 00:00:00';

    		$end = $endDate . ' 23:59:59';

	    	$alls = Order::where(function ($query) use ($masterUser) {
					$query->where('creator_primary_user_id', $masterUser)
						->orWhere('gainer_primary_user_id', $masterUser);
				})
    			->whereBetween('created_at', [$start, $end])
    			->select(DB::raw('count(id) as orderCount, sum(case when status = 1 then 1 else 0 end) as waite, sum(case when status = 2 then 1 else 0 end) as fenpei, sum(case when status = 3 then 1 else 0 end) as jiedan, sum(case when status = 4 then 1 else 0 end) as fahuo, sum(case when status = 5 then 1 else 0 end) as shibai, sum(case when status = 6 then 1 else 0 end) as shouhou, sum(case when status = 7 then 1 else 0 end) as shouhouwancheng, sum(case when status = 8 then 1 else 0 end) as dingdanwancheng, sum(case when status = 10 then 1 else 0 end) as quxiao, sum(case when status = 11 then 1 else 0 end) as weifukuan'))
				->first();

			$game = Order::where(function ($query) use ($masterUser) {
					$query->where('creator_primary_user_id', $masterUser)
						->orWhere('gainer_primary_user_id', $masterUser);
				})
    			->whereBetween('created_at', [$start, $end])
    			->select(DB::raw('count(game_id) as count, game_name'))
    			->groupBy('game_id')
    			->latest('count')
    			->first();

			$datas = Order::where(function ($query) use ($masterUser) {
					$query->where('creator_primary_user_id', $masterUser)
						->orWhere('gainer_primary_user_id', $masterUser);
				})
    			->whereBetween('created_at', [$start, $end])
    			->groupBy(['source'])
				->select(DB::raw('count(id) as orderCount, sum(amount) as moneyCount, source, status, count(source) as sourceCount, count(status) as statusCount, count(game_id) as gameNumber, sum(case when status = 1 then 1 else 0 end) as waite, sum(case when status = 2 then 1 else 0 end) as fenpei, sum(case when status = 3 then 1 else 0 end) as jiedan, sum(case when status = 4 then 1 else 0 end) as fahuo, sum(case when status = 5 then 1 else 0 end) as shibai, sum(case when status = 6 then 1 else 0 end) as shouhou, sum(case when status = 7 then 1 else 0 end) as shouhouwancheng, sum(case when status = 8 then 1 else 0 end) as dingdanwancheng, sum(case when status = 10 then 1 else 0 end) as quxiao, sum(case when status = 11 then 1 else 0 end) as weifukuan'))
				->get();

			$resourceGame = DB::select("select source, game_id, game_name, max(count) as max from (select count(*) as count, game_name, game_id, source from orders where (creator_primary_user_id = '$masterUser' or gainer_primary_user_id = '$masterUser') and created_at between '$start' and '$end' group by source,game_id order by source, count desc, game_id) a group by source");

			// 接单
			$allReceives = Order::where('gainer_primary_user_id', $masterUser)
    			->whereBetween('created_at', [$start, $end])
    			->select(DB::raw('count(id) as orderCount, sum(case when status = 1 then 1 else 0 end) as waite, sum(case when status = 2 then 1 else 0 end) as fenpei, sum(case when status = 3 then 1 else 0 end) as jiedan, sum(case when status = 4 then 1 else 0 end) as fahuo, sum(case when status = 5 then 1 else 0 end) as shibai, sum(case when status = 6 then 1 else 0 end) as shouhou, sum(case when status = 7 then 1 else 0 end) as shouhouwancheng, sum(case when status = 8 then 1 else 0 end) as dingdanwancheng, sum(case when status = 10 then 1 else 0 end) as quxiao, sum(case when status = 11 then 1 else 0 end) as weifukuan'))
				->first();

			$dataReceives = Order::where('gainer_primary_user_id', $masterUser)
    			->whereBetween('created_at', [$start, $end])
    			->groupBy(['source'])
				->select(DB::raw('count(id) as orderCount, sum(amount) as moneyCount, source, status, count(source) as sourceCount, count(status) as statusCount, count(game_id) as gameNumber, sum(case when status = 1 then 1 else 0 end) as waite, sum(case when status = 2 then 1 else 0 end) as fenpei, sum(case when status = 3 then 1 else 0 end) as jiedan, sum(case when status = 4 then 1 else 0 end) as fahuo, sum(case when status = 5 then 1 else 0 end) as shibai, sum(case when status = 6 then 1 else 0 end) as shouhou, sum(case when status = 7 then 1 else 0 end) as shouhouwancheng, sum(case when status = 8 then 1 else 0 end) as dingdanwancheng, sum(case when status = 10 then 1 else 0 end) as quxiao, sum(case when status = 11 then 1 else 0 end) as weifukuan'))
				->get();

			$receiveGame = DB::select("select source, game_id, game_name, max(count) as max from (select count(*) as count, game_name, game_id, source from orders where gainer_primary_user_id = '$masterUser' and created_at between '$start' and '$end' group by source,game_id order by source, count desc, game_id) a group by source");

			// 发单
			$allSends = Order::where('creator_primary_user_id', $masterUser)
    			->whereBetween('created_at', [$start, $end])
    			->select(DB::raw('count(id) as orderCount, sum(case when status = 1 then 1 else 0 end) as waite, sum(case when status = 2 then 1 else 0 end) as fenpei, sum(case when status = 3 then 1 else 0 end) as jiedan, sum(case when status = 4 then 1 else 0 end) as fahuo, sum(case when status = 5 then 1 else 0 end) as shibai, sum(case when status = 6 then 1 else 0 end) as shouhou, sum(case when status = 7 then 1 else 0 end) as shouhouwancheng, sum(case when status = 8 then 1 else 0 end) as dingdanwancheng, sum(case when status = 10 then 1 else 0 end) as quxiao, sum(case when status = 11 then 1 else 0 end) as weifukuan'))
				->first();

			$dataSends = Order::where('creator_primary_user_id', $masterUser)
    			->whereBetween('created_at', [$start, $end])
    			->groupBy(['source'])
				->select(DB::raw('count(id) as orderCount, sum(amount) as moneyCount, source, status, count(source) as sourceCount, count(status) as statusCount, count(game_id) as gameNumber, sum(case when status = 1 then 1 else 0 end) as waite, sum(case when status = 2 then 1 else 0 end) as fenpei, sum(case when status = 3 then 1 else 0 end) as jiedan, sum(case when status = 4 then 1 else 0 end) as fahuo, sum(case when status = 5 then 1 else 0 end) as shibai, sum(case when status = 6 then 1 else 0 end) as shouhou, sum(case when status = 7 then 1 else 0 end) as shouhouwancheng, sum(case when status = 8 then 1 else 0 end) as dingdanwancheng, sum(case when status = 10 then 1 else 0 end) as quxiao, sum(case when status = 11 then 1 else 0 end) as weifukuan'))
				->get();

			$sendGame = DB::select("select source, game_id, game_name, max(count) as max from (select count(*) as count, game_name, game_id, source from orders where creator_primary_user_id = '$masterUser' and created_at between '$start' and '$end' group by source,game_id order by source, count desc, game_id) a group by source");

    	} else {
    		$start = Carbon::now()->subDays(1)->startOfDay()->toDateTimeString();

    		$end = Carbon::now()->subDays(1)->endOfDay()->toDateTimeString();

    		$alls = Order::where(function ($query) use ($masterUser) {
					$query->where('creator_primary_user_id', $masterUser)
						->orWhere('gainer_primary_user_id', $masterUser);
				})
    			->whereBetween('created_at', [$start, $end])
    			->select(DB::raw('count(id) as orderCount, sum(case when status = 1 then 1 else 0 end) as waite, sum(case when status = 2 then 1 else 0 end) as fenpei, sum(case when status = 3 then 1 else 0 end) as jiedan, sum(case when status = 4 then 1 else 0 end) as fahuo, sum(case when status = 5 then 1 else 0 end) as shibai, sum(case when status = 6 then 1 else 0 end) as shouhou, sum(case when status = 7 then 1 else 0 end) as shouhouwancheng, sum(case when status = 8 then 1 else 0 end) as dingdanwancheng, sum(case when status = 10 then 1 else 0 end) as quxiao, sum(case when status = 11 then 1 else 0 end) as weifukuan'))
				->first();

			$game = Order::where(function ($query) use ($masterUser) {
					$query->where('creator_primary_user_id', $masterUser)
						->orWhere('gainer_primary_user_id', $masterUser);
				})
    			->whereBetween('created_at', [$start, $end])
    			->select(DB::raw('count(game_id) as count, game_name'))
    			->groupBy('game_id')
    			->latest('count')
    			->first();

			$datas = Order::where(function ($query) use ($masterUser) {
					$query->where('creator_primary_user_id', $masterUser)
						->orWhere('gainer_primary_user_id', $masterUser);
				})
    			->whereBetween('created_at', [$start, $end])
    			->groupBy(['source'])
				->select(DB::raw('count(id) as orderCount, sum(amount) as moneyCount, source, status, count(source) as sourceCount, count(status) as statusCount, count(game_id) as gameNumber, sum(case when status = 1 then 1 else 0 end) as waite, sum(case when status = 2 then 1 else 0 end) as fenpei, sum(case when status = 3 then 1 else 0 end) as jiedan, sum(case when status = 4 then 1 else 0 end) as fahuo, sum(case when status = 5 then 1 else 0 end) as shibai, sum(case when status = 6 then 1 else 0 end) as shouhou, sum(case when status = 7 then 1 else 0 end) as shouhouwancheng, sum(case when status = 8 then 1 else 0 end) as dingdanwancheng, sum(case when status = 10 then 1 else 0 end) as quxiao, sum(case when status = 11 then 1 else 0 end) as weifukuan'))
				->get();

			$resourceGame = DB::select("select source, game_id, game_name, max(count) as max from (select count(*) as count, game_name, game_id, source from orders where (creator_primary_user_id = '$masterUser' or gainer_primary_user_id = '$masterUser') and created_at between '$start' and '$end' group by source,game_id order by source, count desc, game_id) a group by source");

			// 接单
			$allReceives = Order::where('gainer_primary_user_id', $masterUser)
    			->whereBetween('created_at', [$start, $end])
    			->select(DB::raw('count(id) as orderCount, sum(case when status = 1 then 1 else 0 end) as waite, sum(case when status = 2 then 1 else 0 end) as fenpei, sum(case when status = 3 then 1 else 0 end) as jiedan, sum(case when status = 4 then 1 else 0 end) as fahuo, sum(case when status = 5 then 1 else 0 end) as shibai, sum(case when status = 6 then 1 else 0 end) as shouhou, sum(case when status = 7 then 1 else 0 end) as shouhouwancheng, sum(case when status = 8 then 1 else 0 end) as dingdanwancheng, sum(case when status = 10 then 1 else 0 end) as quxiao, sum(case when status = 11 then 1 else 0 end) as weifukuan'))
				->first();

			$dataReceives = Order::where('gainer_primary_user_id', $masterUser)
    			->whereBetween('created_at', [$start, $end])
    			->groupBy(['source'])
				->select(DB::raw('count(id) as orderCount, sum(amount) as moneyCount, source, status, count(source) as sourceCount, count(status) as statusCount, count(game_id) as gameNumber, sum(case when status = 1 then 1 else 0 end) as waite, sum(case when status = 2 then 1 else 0 end) as fenpei, sum(case when status = 3 then 1 else 0 end) as jiedan, sum(case when status = 4 then 1 else 0 end) as fahuo, sum(case when status = 5 then 1 else 0 end) as shibai, sum(case when status = 6 then 1 else 0 end) as shouhou, sum(case when status = 7 then 1 else 0 end) as shouhouwancheng, sum(case when status = 8 then 1 else 0 end) as dingdanwancheng, sum(case when status = 10 then 1 else 0 end) as quxiao, sum(case when status = 11 then 1 else 0 end) as weifukuan'))
				->get();

			$receiveGame = DB::select("select source, game_id, game_name, max(count) as max from (select count(*) as count, game_name, game_id, source from orders where gainer_primary_user_id = '$masterUser' and created_at between '$start' and '$end' group by source,game_id order by source, count desc, game_id) a group by source");

			// 发单
			$allSends = Order::where('creator_primary_user_id', $masterUser)
    			->whereBetween('created_at', [$start, $end])
    			->select(DB::raw('count(id) as orderCount, sum(case when status = 1 then 1 else 0 end) as waite, sum(case when status = 2 then 1 else 0 end) as fenpei, sum(case when status = 3 then 1 else 0 end) as jiedan, sum(case when status = 4 then 1 else 0 end) as fahuo, sum(case when status = 5 then 1 else 0 end) as shibai, sum(case when status = 6 then 1 else 0 end) as shouhou, sum(case when status = 7 then 1 else 0 end) as shouhouwancheng, sum(case when status = 8 then 1 else 0 end) as dingdanwancheng, sum(case when status = 10 then 1 else 0 end) as quxiao, sum(case when status = 11 then 1 else 0 end) as weifukuan'))
				->first();

			$dataSends = Order::where('creator_primary_user_id', $masterUser)
    			->whereBetween('created_at', [$start, $end])
    			->groupBy(['source'])
				->select(DB::raw('count(id) as orderCount, sum(amount) as moneyCount, source, status, count(source) as sourceCount, count(status) as statusCount, count(game_id) as gameNumber, sum(case when status = 1 then 1 else 0 end) as waite, sum(case when status = 2 then 1 else 0 end) as fenpei, sum(case when status = 3 then 1 else 0 end) as jiedan, sum(case when status = 4 then 1 else 0 end) as fahuo, sum(case when status = 5 then 1 else 0 end) as shibai, sum(case when status = 6 then 1 else 0 end) as shouhou, sum(case when status = 7 then 1 else 0 end) as shouhouwancheng, sum(case when status = 8 then 1 else 0 end) as dingdanwancheng, sum(case when status = 10 then 1 else 0 end) as quxiao, sum(case when status = 11 then 1 else 0 end) as weifukuan'))
				->get();

			$sendGame = DB::select("select source, game_id, game_name, max(count) as max from (select count(*) as count, game_name, game_id, source from orders where creator_primary_user_id = '$masterUser' and created_at between '$start' and '$end' group by source,game_id order by source, count desc, game_id) a group by source");
    	}
    	return view('frontend.data.index', compact('datas', 'startDate', 'endDate', 'alls', 'game', 'allReceives', 'dataReceives', 'allSends', 'dataSends', 'resourceGame', 'receiveGame', 'sendGame'));
    }

    public function money(Request $request)
    {
    	$startDate = $request->startDate;

    	$endDate = $request->endDate;

    	$masterUser = Auth::user()->getPrimaryUserId();

    	if (($startDate && ! $endDate) || (! $startDate && $endDate)) {

    		return back()->with('timeError', '请选择开始日期和结束日期！');
    	}

    	if ($startDate && $endDate) {

    		$start = $startDate . ' 00:00:00';

    		$end = $endDate . ' 23:59:59';

    		$datas = UserOrderDetail::where('user_id', $masterUser)->whereBetween('time', [$start, $end])->get();

    	} else {
    		$start = Carbon::now()->subDays(1)->startOfDay()->toDateTimeString();

    		$end = Carbon::now()->subDays(1)->endOfDay()->toDateTimeString();

    		// $start = Carbon::now()->startOfDay()->toDateTimeString();

    		// $end = Carbon::now()->endOfDay()->toDateTimeString();

    		$statusMoney = Order::where(function ($query) use ($masterUser) {
					$query->where('creator_primary_user_id', $masterUser)
						->orWhere('gainer_primary_user_id', $masterUser);
				})
    			->whereBetween('created_at', [$start, $end])
				->select(DB::raw('sum(case when status = 1 then amount else 0 end) as waite, sum(case when status = 2 then amount else 0 end) as fenpei, sum(case when status = 3 then amount else 0 end) as jiedan, sum(case when status = 4 then amount else 0 end) as fahuo, sum(case when status = 5 then amount else 0 end) as shibai, sum(case when status = 6 then amount else 0 end) as shouhou, sum(case when status = 7 then amount else 0 end) as shouhouwancheng, sum(case when status = 8 then amount else 0 end) as dingdanwancheng, sum(case when status = 10 then amount else 0 end) as quxiao, sum(case when status = 11 then amount else 0 end) as weifukuan'))
				->first();

			$sourceMoney = Order::where(function ($query) use ($masterUser) {
					$query->where('creator_primary_user_id', $masterUser)
						->orWhere('gainer_primary_user_id', $masterUser);
				})
    			->whereBetween('created_at', [$start, $end])
    			->groupBy(['source'])
				->select(DB::raw('sum(case when status = 1 then amount else 0 end) as waite, sum(case when status = 2 then amount else 0 end) as fenpei, sum(case when status = 3 then amount else 0 end) as jiedan, sum(case when status = 4 then amount else 0 end) as fahuo, sum(case when status = 5 then amount else 0 end) as shibai, sum(case when status = 6 then amount else 0 end) as shouhou, sum(case when status = 7 then amount else 0 end) as shouhouwancheng, sum(case when status = 8 then amount else 0 end) as dingdanwancheng, sum(case when status = 10 then amount else 0 end) as quxiao, sum(case when status = 11 then amount else 0 end) as weifukuan'))
				->get();

			// 发单
			$sendMoney = Order::where('creator_primary_user_id', $masterUser)
    			->whereBetween('created_at', [$start, $end])
    			->groupBy(['source'])
				->select(DB::raw('sum(case when status = 1 then amount else 0 end) as waite, sum(case when status = 2 then amount else 0 end) as fenpei, sum(case when status = 3 then amount else 0 end) as jiedan, sum(case when status = 4 then amount else 0 end) as fahuo, sum(case when status = 5 then amount else 0 end) as shibai, sum(case when status = 6 then amount else 0 end) as shouhou, sum(case when status = 7 then amount else 0 end) as shouhouwancheng, sum(case when status = 8 then amount else 0 end) as dingdanwancheng, sum(case when status = 10 then amount else 0 end) as quxiao, sum(case when status = 11 then amount else 0 end) as weifukuan'))
				->get();

			// 接单
			$receiveMoney = Order::where('gainer_primary_user_id', $masterUser)
    			->whereBetween('created_at', [$start, $end])
    			->groupBy(['source'])
				->select(DB::raw('sum(case when status = 1 then amount else 0 end) as waite, sum(case when status = 2 then amount else 0 end) as fenpei, sum(case when status = 3 then amount else 0 end) as jiedan, sum(case when status = 4 then amount else 0 end) as fahuo, sum(case when status = 5 then amount else 0 end) as shibai, sum(case when status = 6 then amount else 0 end) as shouhou, sum(case when status = 7 then amount else 0 end) as shouhouwancheng, sum(case when status = 8 then amount else 0 end) as dingdanwancheng, sum(case when status = 10 then amount else 0 end) as quxiao, sum(case when status = 11 then amount else 0 end) as weifukuan'))
				->get();
			dd($statusMoney);
    	}

    	return view('frontend.data.show', compact('datas', 'startDate', 'endDate'));
    }
}

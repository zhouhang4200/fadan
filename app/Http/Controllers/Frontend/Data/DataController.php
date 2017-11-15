<?php

namespace App\Http\Controllers\Frontend\Data;

use Auth, DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DataController extends Controller
{
    public function index(Request $request)
    {
    	$startDate = $request->startDate;

    	$endDate = $request->endDate;

    	$masterUser = Auth::user()->getPrimaryUserId();

    	if (($startDate && ! $endDate) || (! $startDate && $endDate)) {

    		return back()->with('timeError', '请选择正确的起止日期！');
    	}

    	if ($startDate && $endDate) {

    		$start = $startDate . ' 00:00:00';

    		$end = $endDate . '23:59:59';

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

			$resourceGame = DB::select("select source, game_id, game_name, count(count) as max from (select count(*) as count, game_name, game_id, source from orders where (creator_primary_user_id = '$masterUser' or gainer_primary_user_id = '$masterUser') and created_at between '$start' and '$end' group by source,game_id order by source, count desc, game_id) a group by source");

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

			$receiveGame = DB::select("select source, game_id, game_name, count(count) as max from (select count(*) as count, game_name, game_id, source from orders where gainer_primary_user_id = '$masterUser' and created_at between '$start' and '$end' group by source,game_id order by source, count desc, game_id) a group by source");

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

			$sendGame = DB::select("select source, game_id, game_name, count(count) as max from (select count(*) as count, game_name, game_id, source from orders where creator_primary_user_id = '$masterUser' and created_at between '$start' and '$end' group by source,game_id order by source, count desc, game_id) a group by source");

    	} else {
    		$start = Carbon::now()->startOfDay()->toDateTimeString();

    		$end = Carbon::now()->endOfDay()->toDateTimeString();

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

			$resourceGame = DB::select("select source, game_id, game_name, count(count) as max from (select count(*) as count, game_name, game_id, source from orders where (creator_primary_user_id = '$masterUser' or gainer_primary_user_id = '$masterUser') and created_at between '$start' and '$end' group by source,game_id order by source, count desc, game_id) a group by source");

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

			$receiveGame = DB::select("select source, game_id, game_name, count(count) as max from (select count(*) as count, game_name, game_id, source from orders where gainer_primary_user_id = '$masterUser' and created_at between '$start' and '$end' group by source,game_id order by source, count desc, game_id) a group by source");

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

			$sendGame = DB::select("select source, game_id, game_name, count(count) as max from (select count(*) as count, game_name, game_id, source from orders where creator_primary_user_id = '$masterUser' and created_at between '$start' and '$end' group by source,game_id order by source, count desc, game_id) a group by source");
    	}

    	return view('frontend.data.index', compact('datas', 'startDate', 'endDate', 'alls', 'game', 'allReceives', 'dataReceives', 'allSends', 'dataSends', 'resourceGame', 'receiveGame', 'sendGame'));
    }
}

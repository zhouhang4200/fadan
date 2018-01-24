<?php

namespace App\Http\Controllers\Backend\Statistic;

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
    	// $userName = $request->user_name;
    	// $startDate = $request->start_date;
    	// $endDate = $request->end_date;
    	// $fullUrl = $request->fullUrl();
    	// $filters = compact('userName', 'startDate', 'endDate');

    	// $paginatePlatformOrderStatistics = PlatformOrderStatistic::filter($filters)
    	// 		->selcet()
    	// 		->
    }
}

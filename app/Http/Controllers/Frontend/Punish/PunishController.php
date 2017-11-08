<?php

namespace App\Http\Controllers\Frontend\Punish;

use Auth;
use Asset;
use App\Models\Punish;
use Illuminate\Http\Request;
use App\Extensions\Asset\Consume;
use App\Http\Controllers\Controller;

class PunishController extends Controller
{
    public function index(Request $request) 
    {
    	$startDate = $request->startDate;

    	$endDate = $request->endDate;

    	$type = $request->type;

    	$filters = compact('startDate', 'endDate', 'type');

    	$punishes = Punish::homeFilter($filters)->where('user_id', Auth::id())->paginate(config('frontend.page'));

    	return view('frontend.punish.index', compact('startDate', 'endDate', 'punishes', 'type'));
    }

    public function payment(Request $request)
    {
    	try {
    		$punish = Punish::find($request->id);

            Asset::handle(new Consume($punish->money, 2, $punish->order_no, '违规扣款', $punish->user_id));

            // 写多态关联
	        if (!$punish->userAmountFlows()->save(Asset::getUserAmountFlow())) {
	            DB::rollback();
	            throw new Exception('操作失败');
	        }

	        if (!$punish->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
	            DB::rollback();
	            throw new Exception('操作失败');
	        }

	        Punish::where('id', $request->id)->update(['type' => 1]);

            return response()->json(['code' => 1, 'message' => '付款成功!']);

        } catch (Exception $exception) {

            return response()->json(['code' => 0, 'message' => '付款失败!']);
        }
    }
}

<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Models\RealNameIdent;
use App\Http\Controllers\Controller;

class RealNameIdentController extends Controller
{	
	/**
	 * 审核 查找 界面
	 * @param  Request $request 
	 * @return response
	 */
    public function showAudit(Request $request)
    {
    	$userId = $request->userId;

    	$phone = $request->phone;

    	$filters = compact('userId', 'phone');

    	$users = RealNameIdent::filter($filters)->paginate(config('backend.page'));

    	// return view('users.audit', compact('users'));
    }

    /**
     * ajax 审核通过
     * @param  Request $request [description]
     * @return string
     */
    public function pass(Request $request)
    {
    	$userId = $request->userId;

		if (RealNameIdent::where('user_id', $userId)->update(['status' => 1])) {
    		return 1;
		}
		return 0;    	
    }

    /**
     * ajax 审核不通过
     * @param  Request $request [description]
     * @return string
     */
    public function refuse(Request $request)
    {
    	$userId = $request->userId;

		if (RealNameIdent::where('user_id', $userId)->update(['status' => 2, 'fail_message' => '请按要求填写资料!'])) {
    		return 2;
		}
		return 0; 
    }
}

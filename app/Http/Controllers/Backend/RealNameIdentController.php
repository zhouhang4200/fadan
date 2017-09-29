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
}
